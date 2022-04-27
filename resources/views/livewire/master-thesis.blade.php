<div>
	<x-base.select label="Theses:" :options="$theses" optionKey="name" multiple/>

	<br/>
	Start of MSc Thesis: {{ $startOfThesis['long_name'] }}
</div>
