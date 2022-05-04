<div class='mt-5'>
    <div class="mb-5 text-lg"><b>{{ $additionalCommentsTitle }}</b></div>
    <textarea class="input__field" name="additionalComments" {{ $attributes->only('wire:model') }}></textarea>
</div>
