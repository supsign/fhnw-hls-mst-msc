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

            <x-base.card>
                <form method="POST" action="{{ route('admin.config.post') }}" enctype="multipart/form-data"  class="flex flex-col justify-center gap-5">
                    @csrf
                    <x-base.input label="Surname" type="text" name="surname"/>
                    <x-base.input label="Given Name" type="text" name="given_name" />
                    <livewire:select label="Start" :options="$semesters" wire:model="test"/>
                    <x-base.select label="Study Mode" :options="$studyModes"></x-base.select>
                    <livewire:select label="Specialization" :options="$specializations" optionKey="name" wire:model="specialization"/>
                    <input type="submit" name="submit" class="button-primary">
                </form>
            </x-base.card>
        </div>
    </div>
</x-layout.app>
