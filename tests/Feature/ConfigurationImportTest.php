<?php

namespace Tests\Feature;

use App\Models\Cluster;
use App\Models\Course;
use App\Models\Link;
use App\Models\PageContent;
use App\Models\Specialization;
use App\Models\Thesis;
use App\Models\Venue;
use App\Services\ConfigurationImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use stdClass;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Support\ConfigurationWorkbook;
use Tests\Support\InspectsConfigurationTables;
use Tests\Support\RemovesCreatedFiles;
use Tests\TestCase;

/**
 * Konfigurations-Import ueber den Admin-Endpoint mit einer zur Laufzeit
 * erzeugten Excel (Tests\Support\ConfigurationWorkbook) und dem echten
 * ConfigurationImport: Leeren per SET FOREIGN_KEY_CHECKS, Ersetzen, Rollback
 * und die aus MySQL-Fehlertexten gebauten Meldungen. Laeuft deshalb nur gegen
 * MySQL (CI-Job tests-mysql); was aus den Sheets wird und was Planer und PDF
 * daraus machen, prueft ConfigurationSheetsTest auch auf SQLite.
 */
class ConfigurationImportTest extends TestCase
{
    use InspectsConfigurationTables;
    use RefreshDatabase;
    use RemovesCreatedFiles;

    /**
     * Transaktionen, die der letzte Request ueber die von RefreshDatabase hinaus
     * offen gelassen hat (siehe import()).
     */
    protected int $transactionsLeftOpen = 0;

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped('ConfigurationImport braucht MySQL (SET FOREIGN_KEY_CHECKS, MySQL-Fehlertexte), siehe CI-Job tests-mysql.');
        }

        // GetSemester vervollstaendigt zweistellige Jahre mit dem aktuellen Jahr
        $this->travelTo(Carbon::parse('2026-06-15 10:00:00'));
        $this->removeFilesCreatedDuringTest('local');
    }

    /**
     * Was aus den einzelnen Sheets wird, prueft ConfigurationSheetsTest; hier
     * geht es um den Endpoint: Upload, Leeren der Tabellen, Import, Redirect.
     */
    public function test_valid_configuration_replaces_the_tables_and_redirects_to_the_admin_page(): void
    {
        // Altbestand, der beim Import verschwinden muss
        Cluster::create(['id' => 9, 'name' => 'Old Cluster']);
        Specialization::create(['id' => 9, 'cluster_id' => 9, 'name' => 'Old Specialisation', 'short_name' => 'OLD']);
        PageContent::create(['name' => 'pdf_text', 'content' => 'alter Hinweis']);

        $this->import(ConfigurationWorkbook::realistic())
            ->assertRedirect(config('app.url').'/admin/config');

        Storage::disk('local')->assertExists('config/phpunit-konfiguration.xlsx');
        $this->assertSame(ConfigurationWorkbook::REALISTIC_ROW_COUNTS, $this->rowCounts());
        $this->assertNull(Cluster::find(9));
        $this->assertSame(
            '<p><b>Please note</b> that the module offer may change. Änderungen vorbehalten – Stand: HS 2026.</p>',
            PageContent::getContentByName('pdf_text')
        );
    }

    public function test_reimport_replaces_the_previous_configuration_completely(): void
    {
        $this->import(ConfigurationWorkbook::realistic())->assertRedirect();

        $this->import(
            ConfigurationWorkbook::realistic()
                ->withoutRow('Modules', 211)
                ->withoutRow('ClusterCore', 3)
                ->withoutRow('links', 2)
                ->withValue('Texte', 'pdf_text', 'content', '<p>Neuer Hinweis</p>')
                ->withRow('Thesis', ['subject' => 'biomarker discovery', 'specialisation' => 2])
        )->assertRedirect();

        // Semester, Slots und Venues werden nur ergaenzt, nie geloescht
        $this->assertSame(array_replace(ConfigurationWorkbook::REALISTIC_ROW_COUNTS, [
            'clusters' => 2,
            'theses' => 6,
            'courses' => 14,
            'links' => 1,
        ]), $this->rowCounts());

        $this->assertNull(Cluster::find(3));
        $this->assertNull(Course::find(211));
        $this->assertSame(['msc_programme'], Link::pluck('name')->all());
        $this->assertSame('<p>Neuer Hinweis</p>', PageContent::getContentByName('pdf_text'));
        $this->assertTrue(Thesis::where('name', 'biomarker discovery')->exists());
        $this->assertTrue(Venue::where('name', 'Olten')->exists());
    }

    public function test_unknown_specialisation_returns_500_and_keeps_the_previous_configuration(): void
    {
        $this->import(ConfigurationWorkbook::realistic())->assertRedirect();
        $before = $this->tableContents();

        $response = $this->import(ConfigurationWorkbook::realistic()->withValue('Module Groups', 4, 'specialisation', '1,99'));

        // InvalidData wird gefangen und zurueckgerollt, AdminController antwortet mit
        // abort(500); ein "Class not found" waere auch 500, aber keine HttpException
        $response->assertStatus(500);
        $this->assertInstanceOf(HttpException::class, $response->exception);
        // zurueckgerollt hat der Service selbst, nicht erst das Request-Ende
        $this->assertSame(0, $this->transactionsLeftOpen);
        $this->assertSame($before, $this->tableContents());
    }

    public function test_missing_module_name_returns_500_and_keeps_the_previous_configuration(): void
    {
        $this->import(ConfigurationWorkbook::realistic())->assertRedirect();
        $before = $this->tableContents();

        $response = $this->import(ConfigurationWorkbook::realistic()->withValue('Modules', 112, 'moduleName', null));

        $response->assertStatus(500);
        $this->assertInstanceOf(HttpException::class, $response->exception);
        $this->assertSame(0, $this->transactionsLeftOpen);
        $this->assertSame($before, $this->tableContents());
    }

    /**
     * Bekannter Fehler: ein fehlendes Sheet ist keine InvalidData-Exception, der
     * Import bricht ohne eigenes rollBack ab und MySQL verwirft die offene
     * Transaktion erst am Request-Ende (siehe import()). Der Admin sieht nur den
     * 500, die Ursache steht ausschliesslich im Log.
     */
    public function test_missing_sheet_returns_500_and_keeps_the_previous_configuration(): void
    {
        $this->import(ConfigurationWorkbook::realistic())->assertRedirect();
        $before = $this->tableContents();
        Log::spy();

        $this->import(ConfigurationWorkbook::realistic()->withSheetRenamed('links', 'Links (alt)'))
            ->assertStatus(500);

        $this->assertSame($before, $this->tableContents());
        Log::shouldHaveReceived('error')
            ->withArgs(fn (string $message): bool => str_contains($message, 'sheet name [links]'))
            ->once();
    }

    /**
     * Die Meldungen entstehen aus MySQL-Fehlertexten (Constraint-Namen,
     * "'name' cannot be null"); AdminController gibt sie nicht weiter, daher
     * direkt ueber den Service geprueft.
     */
    public function test_invalid_rows_are_reported_with_a_message_naming_the_row(): void
    {
        $this->assertEquals((object)[
            'status' => 'error',
            'error' => 'invalid specialisation id "99" found in module group "4" (Cluster-Specific Modules Chemistry and Biotechnology)',
        ], $this->importDirectly(ConfigurationWorkbook::realistic()->withValue('Module Groups', 4, 'specialisation', '1,99')));
        // der Service hat seine Transaktion zurueckgerollt, offen ist nur die von RefreshDatabase
        $this->assertSame(1, DB::transactionLevel());

        $this->assertEquals((object)[
            'status' => 'error',
            'error' => 'the column "moduleName" found in course id "112" (MSLS-S-ACB-02) can not be empty',
        ], $this->importDirectly(ConfigurationWorkbook::realistic()->withValue('Modules', 112, 'moduleName', null)));
        $this->assertSame(1, DB::transactionLevel());
    }

    /**
     * Upload wie das Formular in Config.vue (multipart, ohne JSON-Accept). Der
     * MIME-Typ wird wie bei einem echten Upload aus dem Dateiinhalt bestimmt.
     */
    protected function import(ConfigurationWorkbook $workbook): TestResponse
    {
        $path = tempnam(sys_get_temp_dir(), 'phpunit-xlsx-');
        $this->beforeApplicationDestroyed(fn () => unlink($path));
        $workbook->save($path);

        $response = $this->post('/api/admin/configuration', [
            'password' => 'admin-test-password',
            'config_file' => new UploadedFile($path, 'phpunit-konfiguration.xlsx', null, null, true),
        ]);

        // Request-Ende wie in Produktion: die Verbindung schliesst, MySQL verwirft
        // eine offen gebliebene Transaktion. Level 1 ist die von RefreshDatabase.
        $this->transactionsLeftOpen = DB::transactionLevel() - 1;
        DB::rollBack(1);

        return $response;
    }

    protected function importDirectly(ConfigurationWorkbook $workbook): stdClass
    {
        Storage::disk('local')->makeDirectory('config');
        $workbook->save(Storage::disk('local')->path('config/phpunit-konfiguration.xlsx'));

        return app(ConfigurationImport::class)('config/phpunit-konfiguration.xlsx');
    }
}
