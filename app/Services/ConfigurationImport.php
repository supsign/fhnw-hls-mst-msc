<?php

namespace App\Services;

use App\Imports\Configuration AS ConfigurationImporter;
use App\Models\Cluster;
use App\Models\Course;
use App\Models\CourseCourseGroup;
use App\Models\CourseGroup;
use App\Models\CourseGroupSpecialization;
use App\Models\CourseSemester;
use App\Models\Link;
use App\Models\PageContent;
use App\Models\Specialization;
use App\Models\Thesis;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Facades\Storage;
use Spatie\FlareClient\Http\Exceptions\InvalidData;
use stdClass;

class ConfigurationImport
{
    protected array $tablesToTruncate = [
        Cluster::class,
        Course::class,
        CourseCourseGroup::class,
        CourseGroup::class,
        CourseGroupSpecialization::class,
        CourseSemester::class,
        Link::class,
        PageContent::class,
        Specialization::class,
        Thesis::class,
    ];

    public function __construct(
        protected ConfigurationImporter $configurationImporter, 
        protected Excel $excel
    ) {}

    public function __invoke(string $file): stdClass
    {
        if (!Storage::exists($file)) {
            throw new Exception('"'.$file.'" not found');
        }

        DB::beginTransaction();

        try {
            $this
                ->truncateTables()
                ->excel
                    ->import($this->configurationImporter, $file);
        } catch (InvalidData $e) {
            DB::rollBack();

            return (object)[
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }

        DB::commit();

        return (object)[
            'status' => 'success',
        ];
    }

    protected function truncateTables(): self
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        foreach ($this->tablesToTruncate AS $table) {
            $table::getQuery()->delete();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        return $this;
    }
}