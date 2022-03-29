<x-layout.app>
        <x-slot name="title">
            Home
        </x-slot>
    <div class="container p-3 mx-auto">
        <div class="w-full sm:flex-grow">
            <x-app.card class="mb-4">
                <div class="flex">
                    <div class="pr-4">
                        <i class="far fa-lightbulb fa-2x" aria-hidden="true"></i>
                    </div>
                    <div>
                        Hello i'm a friendly Card

                        @dump(
                            $semesters,
                        )

                    </div>
                </div>
            </x-app.card>

        </div>
    </div>
</x-layout.app>
