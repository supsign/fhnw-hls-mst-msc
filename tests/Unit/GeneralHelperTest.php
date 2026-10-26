<?php

namespace Tests\Unit;

use App\Helpers\GeneralHelper;
use PHPUnit\Framework\TestCase;

class GeneralHelperTest extends TestCase
{
    public function test_split_on_uppercase_applies_the_cast_to_every_part(): void
    {
        // so bauen die Enums ihre Labels
        $this->assertSame('Core Competences', GeneralHelper::splitStringOnUppercase('CoreCompetences', ' ', 'ucfirst'));
        $this->assertSame('Cluster-Specific', GeneralHelper::splitStringOnUppercase('ClusterSpecific', '-', 'ucfirst'));
        $this->assertSame('part-time', GeneralHelper::splitStringOnUppercase('PartTime', '-', 'strtolower'));
        $this->assertSame('Elective', GeneralHelper::splitStringOnUppercase('Elective', ' ', 'ucfirst'));
    }

    public function test_initials_are_the_uppercase_letters(): void
    {
        $this->assertSame('CC', GeneralHelper::getInitialsOnUpperCase('CoreCompetences'));
        $this->assertSame('CS', GeneralHelper::getInitialsOnUpperCase('ClusterSpecific'));
        $this->assertSame('S', GeneralHelper::getInitialsOnUpperCase('Specialization'));
    }

    public function test_snake_case_request_keys_become_the_pdf_template_variables(): void
    {
        // GetPdfData uebergibt die Request-Felder als $givenName, $doubleDegreeSemester, ...
        $this->assertSame('givenName', GeneralHelper::snakeToCamelCase('given_name'));
        $this->assertSame('doubleDegreeSemester', GeneralHelper::snakeToCamelCase('double_degree_semester'));
        $this->assertSame('thesisFurtherDetails', GeneralHelper::snakeToCamelCase('thesis_further_details'));
        $this->assertSame('surname', GeneralHelper::snakeToCamelCase('surname'));
    }
}
