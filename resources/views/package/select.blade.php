<div class="widget-content">
	<form class="form-horizontal row-border" action="{{ url('package') }}" method="GET">
		<div class="form-group" style="border-top: 0px;">
			<div class="col-md-2">
				<select class="select2-select-00 col-md-12 full-width-fix" name="name">
					@foreach(App\PackageName::all() as $pkg)
						<option value="{{ $pkg->name }}" {{ $name == $pkg->name ? 'selected' : '' }}>
							{{ $pkg->name }}
						</option>
					@endforeach
				</select>
			</div>
			<button class="btn btn-sm btn-info">
				查看软件版本
			</button>
		</div>

	</form>
</div>
