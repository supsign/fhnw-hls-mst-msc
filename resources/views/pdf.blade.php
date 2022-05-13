<!DOCTYPE html>
<html lang="de">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<div>
    <div>
        <div style='font-size: 1.5rem; line-height: 2rem; float: left; margin-right: 2.5rem;'>Your Study Plan MSc in Life Sciences FHWN</div>
        <img style='height:2rem;' src="{{ asset('img/logos/fhnw-logo-klein.png') }}" alt="Logo FHNW">
    </div>
    <br />
    <div>{{ $givenName }} {{ $surname }}</div>
    <div>Specialization: {{ $specialization->name }}</div>
    <br />
    <div>
        <div style='font-size: 1.125rem; line-height: 1.75rem; font-weight: 700; margin-bottom: 0.75rem'>Study Programme</div>
        @foreach($selectedCourses AS $semester)
            <div style='font-weight: 700; margin-bottom: 0.25rem;'>{{ $semester->name }}</div>
            <table style='border-width: 1px; margin-bottom: 1.25rem; width: 100%'>
                <tr style='border-bottom-width: 1px; padding: 0.25rem;'>
                    <th style='border-right-width: 1px; padding: 0.25rem;  width: 25rem;'>Module Title</th>
                    <th style='border-right-width: 1px; padding: 0.25rem; width: 3rem;'>Type</th>
                    <th style='border-right-width: 1px; padding: 0.25rem; width:3.5rem;'>ECTS</th>
                    <th style='padding: 0.25rem; width: 10rem;'>Venue</th>
                </tr>
                @foreach($semester->selectedCourses AS $course)
                    <tr style='border-bottom-width: 1px;'>
                        <td style='border-right-width: 1px; padding: 0.25rem; width: 25rem;'>{{ $course->name }}</td>
                        <td style='border-right-width: 1px; padding: 0.25rem; width: 3rem;'>{{ $course->typeLabelShort }}</td>
                        <td style='border-right-width: 1px; padding: 0.25rem; text-align: right; width: 3.5rem;'>{{ $course->ects }}</td>
                        <td style='padding: 0.25rem; width: 10rem;'>{{ $course->venue->name }}</td>
                    </tr>
                @endforeach
            </table>
        @endforeach
    </div>
    @if(!empty($modulesOutside))
    <div style='margin-bottom: 1.25rem;'>
        <div style='font-size: 1.125rem; line-height: 1.75rem; font-weight: 700; margin-bottom: 0.75rem'>Modules outside the Curriculum</div>
        <table style='border-width: 1px; margin-bottom: 1.25rem; width: 100%'>
            <tr style='border-bottom-width: 1px;'>
                <th style='border-right-width: 1px; padding: 0.25rem; width: 23rem;'>Module Title</th>
                <th style='border-right-width: 1px; padding: 0.25rem; width:3.5rem;'>ECTS</th>
                <th style='padding: 0.25rem; width: 15rem;'>University</th>
            </tr>

            @foreach($modulesOutside AS $outsideModule)
                <tr style='border-bottom-width: 1px;'>
                    <td style='border-right-width: 1px; padding: 0.25rem; width: 23rem;'>{{ $outsideModule['title'] }}</td>
                    <td style='border-right-width: 1px; padding: 0.25rem; text-align: right; width:3.5rem;'>{{ $outsideModule['ects'] }}</td>
                    <th style='padding: 0.25rem; width: 15rem;'>{{ $outsideModule['university'] }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    @endif
    <div style='margin-bottom: 1.25rem;'>
        <div style='font-size: 1.125rem; line-height: 1.75rem; font-weight: 700; margin-bottom: 0.75rem'>Double Degree</div>
        <div>
            @if($doubleDegree)
                Yes
            @else
                No
            @endif
        </div>
    </div>
    <div style='margin-bottom: 1.25rem;'>
        <div style='font-size: 1.125rem; line-height: 1.75rem; font-weight: 700; margin-bottom: 0.75rem'>Master Thesis</div>
        <div>Master Thesis planned for {{ $thesisStart }} to {{ $thesisEnd }}</div>
        <div>Broad Subject Area</div>
        <ul style='list-style-type: disc; list-style-position: inside;'>
            @foreach($thesis AS $value)
                <li>{{ $value->name }}</li>
            @endforeach
        </ul>
        <br />
        @isset($thesisFurtherDetails)
            <div style='font-size: 1.125rem; line-height: 1.75rem; font-weight: 700; margin-bottom: 0.75rem'>Further Details on Thesis (Optional)</div>
            <div>{{$thesisFurtherDetails}}</div>
        @endisset
    </div>
    <br />
    @isset($additionalComments)
        <div style='margin-bottom: 1.25rem;'>
            <div style='font-size: 1.125rem; line-height: 1.75rem; font-weight: 700; margin-bottom: 0.75rem'>Additional Comments</div>
            <div>{{ $additionalComments}}</div>
        </div>
        <br />
    @endisset
    <div>
        <div style='font-size: 1.125rem; line-height: 1.75rem; font-weight: 700; margin-bottom: 0.75rem'>Summary Statistics</div>
        <div>Number of Specialization Modules: {{ $statistics['specialization'] }}</div>
        <div>Number of Cluster-specific Modules: {{ $statistics['cluster'] }} </div>
        <div>Number Core Competence Modules: {{ $statistics['core'] }}</div>
        <div>Total number of ECTS: {{ $statistics['ects'] }} (50 required)</div>
    </div>
    <br />
    <div>Please note that the module offer and the timing of the modules may change in the future.</div>
    <br />
    <div >
        <div style='float: left; margin-right: 2.5rem;'>
            <div style='border-bottom-width: 1px; padding-bottom: 2.5rem;'>Agreed, Date</div>
        </div>
        <div style='float: left; margin-right: 2.5rem;'>
            <div style='border-bottom-width: 1px; padding-bottom: 2.5rem;'>Signature Student</div>
        </div>
        <div style='float: left; margin-right: 2.5rem;'>
            <div style='border-bottom-width: 1px; padding-bottom: 2.5rem;'>Signature Director of Study Programme</div>
        </div>
    </div>
</div>
</body>
</html>
