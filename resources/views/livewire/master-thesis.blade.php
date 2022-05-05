<div class='flex flex-col gap-5'>
	<div class='mb-5 text-lg'><b>Master Thesis</b></div>
	<div>Start of MSc Thesis: {{ $startOfThesis['long_name'] }}</div>
	<x-base.select
			wire:model="overwriteStartOfThesis"
			:options="$availibleStarts"
			optionKey="name"
			placeholder="-- Choose later thesis --"
		/>
	<x-base.select label="Broad Subject Area" :options="$theses" optionKey="name" multiple wire:model="selectedTheses" :size='count($theses)' />
	<div>
		<div>Further Details on MSc Topic (optional)</div>
		<textarea class="input__field" name="furtherDetails" wire:model="furtherDetails"></textarea>
	</div>
</div>
