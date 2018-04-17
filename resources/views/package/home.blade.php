@extends('common.layouts')

@section('menu')
	应用软件
@stop

@section('content')
	<div class="row">
		<div class="col-md-12">

			@include('common.message')

			<div class="widget box">
				<div class="widget-header">
					<h4>
						<i class="icon-reorder">
						</i>
						应用程序列表
					</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<a class="btn btn-xs" href="{{ url('package/file/list') }}">
								<i class="icon-list">
								</i>
								所有文件
							</a>
							<span class="btn btn-xs widget-refresh">
								<i class="icon-refresh">
								</i>
								刷新
							</span>
							@can('package_release')
								<a class="btn btn-xs" href="#addPackageModal" data-toggle="modal">
									<i class="icon-plus">
									</i>
									新增
								</a>
							@endcan
						</div>
					</div>
				</div>

				<div class="widget-content no-padding">
					<table class="table table-hover table-striped table-bordered table-highlight-head">
						<thead>
							<tr>
								<th>
									软件名称
								</th>
								<th>
									描述
								</th>
								<th>
									邮件通知列表
								</th>
								@can('package_release')
									<th>
									</th>
								@endcan
							</tr>
						</thead>
						<tbody>
							@foreach($packages as $vo)
								<tr>
									<td>
										<a href="{{ url('package?name=' . $vo->name) }}">
											{{ $vo->name }}
										</a>
									</td>
									<td>
										{{ $vo->note }}
									</td>
									<td>
										{{ $vo->dist }}
									</td>
									@can('package_release')
										<td>
											<a href="javascript:;" onclick="update('{{ $vo->name }}')">
												<i class="icon-edit">
												</i>
											</a>
											&nbsp;
											@can('package_delete')
												<a href="{{ url('package/delete?name='.$vo->name) }}" title="删除" onclick="if(confirm('确定删除{{$vo->name}}?') == false) return false;">
													<i class="icon-trash">
													</i>
												</a>
											@endcan
										</td>
									@endcan
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>

@can('package_release')
	<div class="modal fade" id="addPackageModal">
		<div class="modal-dialog" style="width: 1000px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						增加新应用程序
					</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal row-border" action="{{ url('package/add') }}" method="post">
						{{ csrf_field() }}
						<table class="table table-hover table-striped table-bordered table-highlight-head">
							<tbody>
								<tr>
									<td class="col-md-3">
										<strong>
											软件名称
										</strong>
									</td>
									<td>
										<input class="form-control" name="Data[name]" type="text" required autofocus>
									</td>
								</tr>
								<tr>
									<td class="col-md-3">
										<strong>
											软件描述
										</strong>
									</td>
									<td>
										<input class="form-control" name="Data[note]" type="text" required>
									</td>
								</tr>
								<tr>
									<td>
										<strong>
											邮件通知列表
										</strong>
									</td>
									<td>
										<input class="form-control" name="Data[dist]" type="text">
									</td>
								</tr>
							</tbody>
						</table>
	
						<div class="modal-footer table-bordered">
							<button type="button" class="btn btn-default" data-dismiss="modal">
								取消
							</button>
							<button type="submit" class="btn btn-primary">
								确定
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="editPackageModal">
		<div class="modal-dialog" style="width: 1000px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						修改应用程序
					</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal row-border" action="{{ url('package/update') }}" method="post">
						{{ csrf_field() }}
						<table class="table table-hover table-striped table-bordered table-highlight-head" style="border: 1px solid #ddd;">
							<tbody>
								<tr>
									<td class="col-md-3">
										<strong>
											软件名称
										</strong>
									</td>
									<td>
										<input id="editPackageModal_name" class="form-control" name="name" type="text" readonly>
									</td>
								</tr>
	
								<tr>
									<td class="col-md-3">
										<strong>
											软件描述
										</strong>
									</td>
									<td>
										<input id="editPackageModal_note" class="form-control" name="note" type="text" required>
									</td>
								</tr>
								<tr>
									<td>
										<strong>
											邮件通知列表
										</strong>
									</td>
									<td>
										<input id="editPackageModal_dist" class="form-control" name="dist" type="text">
									</td>
								</tr>
							</tbody>
						</table>
						<div class="modal-footer" style="border: 1px solid #ddd;">
							<button type="button" class="btn btn-default" data-dismiss="modal">
								取消
							</button>
							<button type="submit" class="btn btn-primary">
								确定
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endcan

@stop

@section('javascript')
	<script type="text/javascript">
@can('launcher_release')
		function update(name, note, dist)
		{
			$('#editPackageModal_name').val(name);

			$.getJSON('{{ url('package/info') }}', {name: name}, function(data){
				$('#editPackageModal_note').val(data.note);
				$('#editPackageModal_dist').val(data.dist);
			});
			$('#editPackageModal').modal();
		}
@endcan
	</script>
@stop
