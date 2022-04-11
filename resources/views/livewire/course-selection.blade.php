<div>
    @foreach($coreCompetenceCourseGroup['courses'] as $course)
    <livewire:course :internalName="$coreCompetenceCourseGroup['internal_name']" :course="$course"/>
    @endforeach
  </div>
</div>

