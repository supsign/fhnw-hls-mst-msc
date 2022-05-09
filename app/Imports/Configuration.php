<?php

namespace App\Imports;

use App\Imports\ConfigurationSheets\ClusterSheetImport;
use App\Imports\ConfigurationSheets\CourseGroupSheetImport;
use App\Imports\ConfigurationSheets\CourseSheetImport;
use App\Imports\ConfigurationSheets\LinkSheetImport;
use App\Imports\ConfigurationSheets\PageContentSheetImport;
use App\Imports\ConfigurationSheets\SpecialisationSheetImport;
use App\Imports\ConfigurationSheets\ThesisSheetImport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Configuration implements WithMultipleSheets
{
	protected array $clusters;

   	public function sheets(): array
    {
    	return [
    		'ClusterCore' => new ClusterSheetImport,
    		'Specialisation' => new SpecialisationSheetImport,
    		'Thesis' => new ThesisSheetImport,
    		'Module Groups' => new CourseGroupSheetImport,
    		'Modules' => new CourseSheetImport,
    		'Texte' => new PageContentSheetImport,
    		'links' => new LinkSheetImport,
    	];
    }
}