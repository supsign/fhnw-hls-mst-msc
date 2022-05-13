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
        <div class="text-lg font-bold mb-3">Study Programme</div>
        @foreach($selectedCourses AS $semester)
            <div class="mb-1 font-bold">{{ $semester->name }}</div>
            <table class='border mb-5'>
                <tr class='border-b p-1'>
                    <th class='border-r p-1'>Module Title</th>
                    <th class='border-r p-1'>Type</th>
                    <th class='border-r p-1'>ECTS</th>
                    <th class='p-1'>Venue</th>
                </tr>
                @foreach($semester->selectedCourses AS $course)
                    <tr class='border-b'>
                        <td class='border-r p-1'>{{ $course->name }}</td>
                        <td class='border-r p-1'>{{ $course->typeLabelShort }}</td>
                        <td class='border-r p-1'>{{ $course->ects }}</td>
                        <td class='p-1'>{{ $course->venue->name }}</td>
                    </tr>
                @endforeach
            </table>
        @endforeach
    </div>
    @if(!empty($modulesOutside))
    <div class="mb-5">
        <div class="text-lg font-bold">Modules outside the Curriculum</div>
        <table class='border mt-2'>
            <tr class='border-b p-1'>
                <th class='border-r p-1'>Module Title</th>
                <th class='border-r p-1'>ECTS</th>
                <th class='p-1'>University</th>
            </tr>

            @foreach($modulesOutside AS $outsideModule)
                <tr class='border-b'>
                    <td class='border-r p-1'>{{ $outsideModule['title'] }}</td>
                    <td class='border-r p-1'>{{ $outsideModule['ects'] }}</td>
                    <td class='p-1'>{{ $outsideModule['university'] }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    @endif
<div class="mb-5">
    @if($overlappingCourses->count())
        <div>The following modules you have selected will probably take place at the same time. Please change your choice: </div>

        @foreach ($overlappingCourses AS $semesterSlots)
            @if(!$semesterSlots->slots->count())
                @continue
            @endif

            <div class="text-lg mb-1"><b>{{ $semesterSlots->semester->name }}</b></div>

            <table>

            @foreach ($semesterSlots->slots AS $slot)
                <tr class='border-b p-1'>
                    <th class='border-r p-1'>Slot {{ $slot->name }}</th>
                </tr>

                @foreach ($slot->courses AS $course)
                    <tr class='border-b'>
                        <td class='border-r p-1'>{{ $course->name }}</td>
                    </tr>
                @endforeach
            @endforeach

            </table>
        @endforeach
    @else
        <div>The modules you have selected will probably not take place at the same time.</div>
    @endif
</div>
    <div class="mb-5">
        <div class="text-lg font-bold">Double Degree</div>
        <div>
            @if($doubleDegree)
                Yes
            @else
                No
            @endif
        </div>
    </div>
    <div class="mb-5">
        <div class="text-lg font-bold">Master Thesis</div>
        <div>Master Thesis planned for {{ $thesisStart }} to {{ $thesisEnd }}</div>
        <div>Broad Subject Area</div>
        <ul class="list-disc list-inside">
            @foreach($thesis AS $value)
                <li>{{ $value->name }}</li>
            @endforeach
        </ul>
        <br />
        @isset($thesisFurtherDetails)
        <div class="text-lg font-bold">Further Details on Thesis (Optional)</div>
            <div>{{$thesisFurtherDetails}}</div>
        @endisset
    </div>
    <br />
    @isset($additionalComments)
        <div class="mb-5">
            <div class="text-lg font-bold">Additional Comments</div>
            <div>{{ $additionalComments}}</div>
        </div>
        <br />
    @endisset
    <div>
        <div class="text-lg font-bold">Summary Statistics</div>
        <div>Number of Specialization Modules: {{ $statistics['specialization'] }}</div>
        <div>Number of Cluster-specific Modules: {{ $statistics['cluster'] }} </div>
        <div>Number Core Competence Modules: {{ $statistics['core'] }}</div>
        <div>Total number of ECTS: {{ $statistics['ects'] }} (50 required)</div>
    </div>
    <br />
    <div>Please note that the module offer and the timing of the modules may change in the future.</div>
    <br />
    <div >
        <div class="float-left mr-10">
            <div class='border-b border-black pb-10'>Agreed, Date</div>
        </div>
        <div class="float-left mr-10">
            <div class='border-b border-black pb-10'>Signature Student</div>
        </div>
        <div class="float-left mr-10">
            <div class='border-b border-black pb-10'>Signature Director of Study Programme</div>
        </div>
    </div>
</div>
</body>
</html>
