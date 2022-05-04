<div>
	<div class='mb-5 text-lg'><b>Master Thesis</b></div>
	<div>Start of MSc Thesis: {{ $startOfThesis['long_name'] }}</div>
	<x-base.select
			wire:model="overwriteStartOfThesis"
			{{-- label="later Start" --}}
			:options="$availibleStarts"
			optionKey="name"
			placeholder="-- Choose later thesis --"

	/>
	<br />
	<x-base.select label="Broad Subject Area" :options="$theses" optionKey="name" multiple wire:model="selectedTheses" size='10' />
	<br/>
</div>
