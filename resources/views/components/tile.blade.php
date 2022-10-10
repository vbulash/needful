
<div class="col-md-6 col-xl-4 mb-4">
	<a class="block block-rounded block-transparent block-link-pop {{ $active ? 'bg-gd-sea' : 'bg-gray no-link' }} h-100 mb-0"
	   href="{{ $link }}">
		<div
			class="block-content block-content-full d-flex align-items-center justify-content-between">
			<div>
				<p class="fs-lg fw-semibold mb-0 text-white">{{ $title }}</p>
				@if ($subtitle)
					<p class="text-white-75 mb-0">{{ $subtitle }}</p>
				@endif
			</div>
			<div class="ms-3 item">
				<i class="{{ $icon }} text-white-50"></i>
			</div>
		</div>
	</a>
</div>
