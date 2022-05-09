<!DOCTYPE html>
<html lang="de">
<body>
    <form method="POST" action="{{ route('admin.post.configuration') }}" enctype="multipart/form-data"  class="flex flex-col justify-center gap-5">
        @csrf
        <input label="Password" type="password" name="password" />
        <input type="file" name="config_file">
        @error("config_file") <span class="text-red-500">{{ $message }}</span> @enderror
        <input type="submit" name="submit" class="button-primary">
    </form>
</body>
</html>
