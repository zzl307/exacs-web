@extends('common.layouts')

@section('menu')
	应用软件
@stop

@section('content')
	<div class="row">
		<div class="col-md-12">

			@include('common.message')

			<div class="widget box">

				@if(!empty($name))
					@include('package.select')
				@endif

				<div class="widget-header" style="border-top: 1px solid #ddd;">
					<h4>
						<i class="icon-reorder">
						</i>
						@if(!empty($release))
							{{ $release->pkgname.'-'.$release->version }}
						@elseif(!empty($name))
							{{ $name }}文件列表
						@else
							文件列表
						@endif
					</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-refresh">
								<i class="icon-refresh">
								</i>
								刷新
							</span>
							@if(!empty($release))
								@if($release->test)
									@can('package_test')
										<a class="btn btn-xs" data-toggle="modal" href="#uploadModalTest">
											<i class="icon-upload-alt">
											</i>
											上传
										</a>
									@endcan
								@else
									@can('package_upload')
										<a class="btn btn-xs" data-toggle="modal" href="#uploadModal">
											<i class="icon-upload-alt">
											</i>
											上传
										</a>
									@endcan
								@endif
							@else
								<a class="btn btn-xs" href="javascript:history.go(-1)">
									<i class="icon-chevron-left">
									</i>
									返回
								</a>
							@endif
						</div>
					</div>
				</div>

				<div class="widget-content no-padding">
					<table class="table table-hover table-striped table-bordered table-highlight-head">
						<thead>
							<tr>
								<th>
									文件名
								</th>
								<th>
									校验值
								</th>
								<th>
									别名
								</th>
								<th>
									更新时间
								</th>
								<th>
									状态
								</th>
								<th>
									设备数
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($packages as $vo)
								<tr>
									<td>
										{{$vo->filename}}
										@if(Storage::disk('uploads')->exists($vo->fuzzyname) == 1)
											<a href="{{ url('download', ['package' => $vo->fuzzyname]) }}" title="下载" onclick="if(confirm('{{"下载文件 " . $vo->filename . "?"}}') == false) return false;">
												<i class="icon-download" style="float: right">
												</i>
											</a>
										@else
											<span class="label label-danger" style="float: right">文件不存在</span>
										@endif
									</td>
									<td>
										{{$vo->cksum}}
									</td>
									<td>
										{{$vo->fuzzyname}}
									</td>
									<td>
										{{$vo->time}}
									</td>
									<td>
										@if($vo->test)
											测试中
											@if (isset($release) || $release->test == 1)
												@can('package_validate')
													<a href="{{ url('package/file/check?id='.$vo->id) }}" title="完成上线测试" style="float: right;">
														<i class="icon-ok" style="float: right">
														</i>
													</a>
												@endcan
											@endif
										@else
											上线
											@if (isset($release))
												@can('package_validate')
													<a href="{{ url('package/file/uncheck?id='.$vo->id) }}" title="下线测试" style="float: right">
														<i class="icon-undo" style="float: right">
														</i>
													</a>
												@endcan
											@endif
										@endif
									</td>
									<td>
										@if (array_key_exists($vo->id, $devices))
											<a href="{{ url('devices/search?key=package:'.$vo->id) }}" title="显示设备列表">
												{{ $devices[$vo->id] }}
											</a>
											@can('package_upgrade')
												<a href="javascript:;" onclick="upgrade('{{ $vo->id }}', '{{ $vo->filename }}', '{{ $vo->pkgname}}', '{{ $vo->pkgtype }}', '{{ $vo->devtype }}');" title="升级版本">
													<i class="icon-signout" style="float: right">
													</i>
												</a>
											@endcan
										@else
											0
											@can('package_delete')
												<a href="{{ url('package/file/delete?id='.$vo->id) }}" title="删除" onclick="if(confirm('确定删除?') == false) return false;">
													<i class="icon-trash" style="float: right">
													</i>
												</a>
											@endcan
										@endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

@if(!empty($release))
	<div class="modal fade" id="uploadModalTest">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						上传应用软件
					</h4>
				</div>
				<form class="form-horizontal row-border" action="{{ url('package/uploadTest?id='.$release->id) }}" method="post" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="modal-body">
						<div class="form-group">
							<label for="file" class="col-md-3 control-label">
								请选择文件
							</label>
							<div class="col-md-9" style="margin-top: 3px;">
								<input class="form-control" name="source" type="file" required autofocus>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">
							取消
						</button>
						<button type="submit" class="btn btn-primary">
							上传
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endif

@if(!empty($release))
	<div class="modal fade" id="uploadModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						上传应用软件
					</h4>
				</div>
				<form class="form-horizontal row-border" action="{{ url('package/upload?id='.$release->id) }}" method="post" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="modal-body">
						<div class="form-group">
							<label for="file" class="col-md-3 control-label">
								请选择文件
							</label>
							<div class="col-md-9" style="margin-top: 3px;">
								<input class="form-control" name="source" type="file" required autofocus>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">
							取消
						</button>
						<button type="submit" class="btn btn-primary">
							上传
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endif

@can('package_upgrade')
	<div class="modal fade" id="upgradeModal">
		<div class="modal-dialog" style="width: 1000px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						升级应用程序
					</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal row-border" action="{{ url('package/upgrade') }}" method="post">
						{{ csrf_field() }}
						<table class="table table-hover table-striped table-bordered table-highlight-head">
							<input id="upgradeModal_package_id" class="form-control" type="hidden" name="id">
							<tbody>
								<tr>
									<td class="col-md-3">
										<strong>
											版本
										</strong>
									</td>
									<td>
										<input id="upgradeModal_package" class="form-control" type="text" readonly>
									</td>
								</tr>
	
								<tr>
									<td>
										<strong>
											升级到
										</strong>
									</td>
									<td>
										<select id="upgradeModal_packages" class="form-control" name="to" required>
										</select>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="modal-footer">
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
		function upgrade(pkgid, name, pkgname, pkgtype, devtype)
		{
			$('#upgradeModal_package_id').val(pkgid);
			$('#upgradeModal_package').val(name);

			$.getJSON('{{ url('package/getPackageList') }}', {pkgname: pkgname, pkgtype: pkgtype, devtype: devtype}, function(data) {
				var options = '';
				options += '<option>请选择</option>';
				for (var i=0; i<data.length; i++)
				{
					if (data[i].test == 0)
						options += "<option value="+data[i].id+">"+data[i].filename+"</option>";
				}
				$('#upgradeModal_packages').html(options);
			});

			$('#upgradeModal').modal();
		}
	</script>
@stop
