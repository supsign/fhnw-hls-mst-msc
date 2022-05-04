<div>
    <div class="mb-5 text-lg"><b>Summary Statistics</b></div>
    <div>{{ $statistics['specialization'] ?? 0 }} of Specialization Modules</div>
    <div>{{ $statistics['cluster_specific'] ?? 0 }} of Cluster-specific Modules</div>
    <div>{{ $statistics['core_compentences'] ?? 0 }} Core Competence Modules</div>
    @if($semestersWithEcts)
    <div class='flex flex-col my-2'>
        <div class='flex'>
            <div class='border-t border-l p-1 w-20'>Semster</div>
            <div class='border-t border-x p-1 w-12 text-right'>ECTS</div>
        </div>
        @foreach($semestersWithEcts AS $key => $value)
            <div class='flex'>
                <div class='border-t border-l p-1 w-20'>{{$key}}</div>
                <div class='border-t border-x p-1 w-12 text-right'>{{$value}}</div>
            </div>
        @endforeach
        <div class='flex'>
            <div class='border-y border-l p-1 w-20'>Total</div>
            <div class='border-y border-x p-1 w-12 text-right'>{{$ects}}</div>
        </div>
    </div>
    @endif
</div>
