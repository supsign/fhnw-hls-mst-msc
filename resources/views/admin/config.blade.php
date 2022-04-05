<x-layout.app>
    <x-slot name="title">
      Config
    </x-slot>
    <div class="flex flex-col justify-center min-h-screen py-12 bg-gray-50 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center">
            <div class="flex flex-col justify-around">
                <div class="space-y-6">
                    <x-base.card>
                        <form method="POST" action="{{ route('admin.config.post') }}" enctype="multipart/form-data"  class="flex flex-col justify-center gap-5">
                            @csrf
                            <x-base.input label="Password" type="password" name="password" />
                            <input type="file" name="config_file">
                            @error("config_file") <span class="text-red-500">{{ $message }}</span> @enderror
                            <input type="submit" name="submit" class="button-primary">
                        </form>
                    </x-base.card>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
