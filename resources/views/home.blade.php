<x-layout.app>
    <x-slot name="title">
        Home
    </x-slot>
    <div class="container p-3 mx-auto">
        <div class="w-full sm:flex-grow">
            <x-base.card class="mb-4">
                <div class="text-2xl">{{$introTitle}}</div>
                <div class="flex flex-col">
                    <div class="whitespace-pre-line">
                        {{$introContent}}</div>
                        <x-base.link href="{{$introLink}}">{{$introLink}}</x-base.link>
                </div>
            </x-base.card>
            <livewire:study-form :semesters="$semesters" :specializations="$specializations" :studyModes="$studyModes"/>
        </div>
    </div>
</x-layout.app>
