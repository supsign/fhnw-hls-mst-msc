<div>
	<strong>Start of MSc Thesis:</strong> {{ $startOfThesis['long_name'] }}
	<br/>
	<x-base.select
			wire:model="overwriteStartOfThesis"
			{{-- label="later Start" --}}
			:options="$availibleStarts"
			optionKey="name"
			placeholder="-- Choose later thesis --"

	/>
	<x-base.select label="Broad Subject Area" :options="$theses" optionKey="name" multiple wire:model="selectedTheses"/>
	<br/>
	<div>
		<div><b>Further Details on MSc Topic (optional)</b></div>
		<textarea class="input__field" name="furtherDetails" wire:model="furtherDetails"></textarea>
	</div>
</div>
