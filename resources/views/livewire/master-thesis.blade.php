<div class='flex flex-col gap-5'>
	<div><div><strong>Start of MSc Thesis:</strong> {{ $startOfThesis['long_name'] }}</div>
	<br/>
	<x-base.select
			wire:model="overwriteStartOfThesis"
			{{-- label="later Start" --}}
			:options="$availibleStarts"
			optionKey="name"
			placeholder="-- Choose later thesis --"

	/></div>
	<x-base.select label="Broad Subject Area" :options="$theses" optionKey="name" multiple wire:model="selectedTheses" bold/>
	<br/>
	<div>
		<div><b>Further Details on MSc Topic (optional)</b></div>
		<textarea class="input__field" name="furtherDetails" wire:model="furtherDetails"></textarea>
	</div>
</div>
