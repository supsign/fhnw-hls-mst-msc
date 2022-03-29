<?php

namespace App\Services\Imports;

use App\Imports\ConfigurationImport;
use App\Models\Cluster;
use App\Models\Course;
use App\Models\CourseCourseGroup;
use App\Models\CourseGroup;
use App\Models\CourseGroupSpecialization;
use App\Models\Link;
use App\Models\PageContent;
use App\Models\Slot;
use App\Models\Specialization;
use App\Models\Thesis;
use App\Models\Venue;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Facades\Storage;
use Spatie\FlareClient\Http\Exceptions\InvalidData;

class ConfigurationImportService
{
    protected array $tablesToTruncate = [
        Cluster::class,
        Course::class,
        CourseCourseGroup::class,
        CourseGroup::class,
        CourseGroupSpecialization::class,
        Link::class,
        PageContent::class,
        Slot::class,
        Specialization::class,
        Thesis::class,
        Venue::class,
    ];

    public function __construct(protected ConfigurationImport $configurationImport, protected Excel $excel)
    {
        
    }

    public function __invoke(string $file): array
    {
        if (!Storage::exists($file)) {
            throw new Exception('"'.$file.'" not found');
        }

        try {
            $this
                ->truncateTables()
                ->excel
                    ->import($this->configurationImport, $file);
        } catch (InvalidData $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }

        return [
            'status' => 'success',
        ];
    }

    protected function truncateTables(): self
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        foreach ($this->tablesToTruncate AS $table) {
            $table::getQuery()->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        return $this;
    }
}