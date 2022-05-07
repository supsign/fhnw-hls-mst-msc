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
        @foreach($selectedCourses AS $semester)
            <div class="text-lg mb-1"><b>{{ $semester->name }}</b></div>
            <table class='border'>
                <tr class='border-b p-1'>
                    <th class='border-r p-1'>Module Title</th>
                    <th class='border-r p-1'>Type</th>
                    <th class='border-r p-1'>ECTS</th>
                    <th class='p-1'>Venue</th>
                </tr>
                @foreach($semester->selectedCourses AS $course)
                    <tr class='border-b'>
                        <td class='border-r p-1'>{{ $course->name }}</td>
                        <td class='border-r p-1'>{{ $course->courseGroup?->courseGroupTypeShortName }}</td>
                        <td class='border-r p-1'>{{ $course->ects }}</td>
                        <td class='p-1'>{{ $course->venue->name }}</td>
                    </tr>
                @endforeach
            </table>
            <br />
        @endforeach
    </div>

    <div>
        <div> {{ '<optional courses title>' }} </div>

        @foreach($optionalEnglish AS $semester)
            <div class="text-lg mb-1"><b>{{ $semester->name }}</b></div>
            <table class='border'>
                <tr class='border-b p-1'>
                    <th class='border-r p-1'>Module Title</th>
                    <th class='border-r p-1'>ECTS</th>
                    <th class='p-1'>Venue</th>
                </tr>
                @foreach($semester->selectedCourses AS $course)
                    <tr class='border-b'>
                        <td class='border-r p-1'>{{ $course->name }}</td>
                        <td class='border-r p-1'>{{ $course->ects }}</td>
                        <td class='p-1'>{{ $course->venue->name }}</td>
                    </tr>
                @endforeach
            </table>
            <br />
        @endforeach
    </div>

    <br />
    <div>
        <div> {{ '<outsideModules title>' }} </div>
        <table class='border'>
            <tr class='border-b p-1'>
                <th class='border-r p-1'>Module Title</th>
                <th class='border-r p-1'>ECTS</th>
                <th class='p-1'>University</th>
            </t>

            @foreach($modulesOutside AS $outsideModule)
                <tr class='border-b'>
                    <td class='border-r p-1'>{{ $outsideModule['title'] }}</td>
                    <td class='border-r p-1'>{{ $outsideModule['ects'] }}</td>
                    <td class='p-1'>{{ $outsideModule['university'] }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    <br />

    @if($overlappingCourses->count())
        <div> {{ '<overlapping courses title>' }} </div>

        @foreach ($overlappingCourses AS $semesterSlots)
            @if(!$semesterSlots->slots->count())
                @continue
            @endif

            <div class="text-lg mb-1"><b>{{ $semesterSlots->semester->name }}</b></div>

            <table>

            @foreach ($semesterSlots->slots AS $slot)
                <tr class='border-b p-1'>
                    <th class='border-r p-1'>Slot {{ $slot->name }}</th>
                </t>

                @foreach ($slot->courses AS $course)
                    <tr class='border-b'>
                        <td class='border-r p-1'>{{ $course->name }}</td>
                    </tr>
                @endforeach
            @endforeach

            </table>
        @endforeach
    @endif
    <br />

    <div>
        <div>Master Thesis planned for {{ $thesisStart->start_date->format('d.m.Y') }} to {{ $thesisEnd->format('d.m.Y') }}</div>
        <div>Broad Subject Area</div>
        <ul class="list-disc list-inside">
            @foreach($thesis AS $value)
                <li>{{ $value->name }}</li>
            @endforeach
        </ul>
        <br />
        @isset($thesisFurtherDetails)
            <div><b>Further Details on Thesis (Optional)</b></div>
            <div>{{$thesisFurtherDetails}}</div>
        @endisset
    </div>
    <br />
    @isset($additionalComments)
        <div>
            <div><b>Additional Comments</b></div>
            <div>{{ $additionalComments}}</div>
        </div>
        <br />
    @endisset
{{--     <div>
        <div>Summary Statistics</div>
        <div>{{ $counts['specialization'] }} of Specialization Modules</div>
        <div>{{ $counts['cluster_specific'] }} of Cluster-specific Modules</div>
        <div>{{ $counts['core_compentences'] }} Core Competence Modules</div>
        <div>{{ $ects }} Total number of ECTS</div>
    </div> --}}
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
