<!DOCTYPE html>
<html lang="de">

<head>
    <link rel="stylesheet" type="text/css" href="{{asset('css/app.css')}}">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<div>
    <div class="2xl">Your Study Plan MSc in Life Sciences FHWN</div>
    <div>{{$data['givenName']}} {{$data['surname']}}</div>
    <div>Specialization: {{$data['specialization']}}</div>
    <div>
        @foreach($data['semesters'] AS $key => $value)
            <div>{{$key}}</div>
                <table class='border'>
                    <tr class='border-b p-1'>
                        <th class='border-r p-1'>Module Title</th>
                        <th class='border-r p-1'>Type</th>
                        <th class='p-1'>ECTS Venue</th>
                    </tr>
                    @foreach($value AS $course)
                    <tr class='border-b'>
                        <td class='border-r p-1'>{{$course['name']}}</td>
                        <td class='border-r p-1'>Type</td>
                        <td class='p-1'>{{$course['ects']}}</td>
                    </tr>
                    @endforeach
                </table>
        @endforeach
    </div>
    <div>
        <div>Master Thesis planned for StartDate to EndDate</div>
        <div>BroadSubjectArea</div>
        <div>FurtherDetailsOnMscTopic</div>
    </div>
    <div>
        <div>Summary Statistics</div>
        <div>{{$data['specialization_count']}} of Specialization Modules</div>
        <div># of Cluster-specific Modules</div>
        <div># Core Competence Modules</div>
        <div>{{$data['ects']}} Total number of ECTS</div>
    </div>
    <div>Please note that the module offer and the timing of the modules may change in the future.</div>
    <div><div>Agreed, Date</div><div>Signature Student</div><div>Signature Director of Study Programme</div></div>
</div>
</body>
</html>
