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
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Facades\Storage;

class ConfigurationImportService
{
    protected array $tablesToTruncate = [
        PageContent::class,
        Link::class,
        CourseCourseGroup::class,
        Course::class,
        CourseGroupSpecialization::class,
        CourseGroup::class,
        Thesis::class,
        Specialization::class,
        Cluster::class,
        Slot::class,
        Venue::class,
    ];

    public function __construct(protected ConfigurationImport $configurationImport, protected Excel $excel)
    {
        $this->truncateTables();
    }

    public function __invoke(string $file)
    {
        if (!Storage::exists($file)) {
            throw new Exception('"'.$file.'" not found');
        }

        $this->excel->import($this->configurationImport, $file);
    }

    protected function truncateTables(): self
    {
        foreach ($this->tablesToTruncate AS $table) {
            $table::getQuery()->delete();
        }

        return $this;
    }
}