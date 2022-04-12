<div class="flex border gap-5">
    <div class="w-96 border-r">{{$course['name']}}</div><div class="w-6 border-r">{{$internalName}}</div>
    <livewire:radio-group :courseName="$course['name']" :nextSemesters="$nextSemesters"/>
</div>
