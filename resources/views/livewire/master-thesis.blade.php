<div>
	<x-base.select label="Theses:" :options="$theses" optionKey="name" multiple/>

	<br/>
	<strong>Start of MSc Thesis:</strong> {{ $startOfThesis['long_name'] }}

	<br/>
	<x-base.select
		wire:model="overwriteStartOfThesis"
		{{-- label="later Start" --}}
		:options="$availibleStarts"
		optionKey="name"
		placeholder="-- Choose later thesis --"
	/>
</div>
