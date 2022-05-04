<!DOCTYPE html>
<html lang="de">
<head>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<div>
    <div>
        <div class="text-2xl float-left mr-10">Your Study Plan MSc in Life Sciences FHWN</div>
        <img class="h-8" src="{{ asset('img/logos/fhnw-logo-klein.png') }}" alt="Logo FHNW">
    </div>
    <br />
    <div>{{ $givenName }} {{ $surname }}</div>
    <div>Specialization: {{ $specialization->name }}</div>
    <br />
    <div>
        @foreach($selected_courses AS $semester)
            <div class="text-lg mb-1"><b>{{ $semester->name }}</b></div>
            <table class='border'>
                <tr class='border-b p-1'>
                    <th class='border-r p-1'>Module Title</th>
                    <th class='border-r p-1'>Type</th>
                    <th class='p-1'>ECTS Venue</th>
                </tr>
                @foreach($semester->selectedCourses AS $course)
                    <tr class='border-b'>
                        <td class='border-r p-1'>{{ $course->name }}</td>
                        <td class='border-r p-1'>{{ $course->courseGroup?->courseGroupTypeShortName }}</td>
                        <td class='p-1'>{{ $course->ects }}</td>
                    </tr>
                @endforeach
            </table>
            <br />
        @endforeach
    </div>
    <br />
    <div>
        <div>Master Thesis planned for {{ $thesis_start->start_date->format('d.m.Y') }} to EndDate</div>
        <div>Broad Subject Area</div>
        <ul class="list-disc list-inside">
            @foreach($thesis_subject AS $value)
                <li>{{ $value->name }}</li>
            @endforeach
        </ul>
        <br />
        <div>FurtherDetailsOnMscTopic</div>
    </div>
    <div>
        <div>Summary Statistics</div>
        <div>{{ $counts['specialization'] }} of Specialization Modules</div>
        <div>{{ $counts['cluster_specific'] }} of Cluster-specific Modules</div>
        <div>{{ $counts['core_compentences'] }} Core Competence Modules</div>
        <div>{{ $ects }} Total number of ECTS</div>
    </div>
    <br />
    <div>Please note that the module offer and the timing of the modules may change in the future.</div>
    <br />
    <div >
        <div class="float-left mr-5">
            <div>Agreed, Date</div><div>___________</div>
        </div>
        <div class="float-left mr-5">
            <div>Signature Student</div><div>___________</div>
        </div>
        <div class="float-left mr-5">
            <div>Signature Director of Study Programme</div><div>___________</div>
        </div>
    </div>
</div>
</body>
</html>
