<div>
  @dump($coreCompetenceCourseGroup)
    @foreach($coreCompetenceCourseGroup['courses'] as $course)
  <div>
    <div>{{$course['name']}}
    </div>
  </div>
    @endforeach
  </div>

</div>
<livewire:checkbox name="test"/>
