<div>
    <div>{{$course['name']}}</div><div>{{$internalName}}</div>
    <livewire:radio-group :courseName="$course['name']" :values="$semesters"/>
</div>
