<div>
    @dump($statistics)
    <div class="mb-5 text-lg"><b>Summary Statistics</b></div>
    <div>{{ $statistics['specialization'] ?? 0 }} of Specialization Modules</div>
    <div>{{ $statistics['cluster_specific'] ?? 0 }} of Cluster-specific Modules</div>
    <div>{{ $statistics['core_compentences'] ?? 0 }} Core Competence Modules</div>
</div>
