@extends('common.layouts')

@section('menu')
	启动程序
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
						{{ empty($release) ? "文件列表" : 'exa-launcher-'.$release->version }}
					</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-refresh">
								<i class="icon-refresh">
								</i>
								刷新
							</span>
							@if (!empty($release))
								@if($release->test)
									@can('launcher_test')
										<a class="btn btn-xs" data-toggle="modal" href="#uploadLauncherModalTest">
											<i class="icon-upload-alt">
											</i>
											上传
										</a>
									@endcan
								@else
									@can('launcher_upload')
										<a class="btn btn-xs" data-toggle="modal" href="#uploadLauncherModal">
											<i class="icon-upload-alt">
											</i>
											上传
										</a>
									@endcan
								@endif
							@endif
						</div>
					</div>
				</div>

				<div class="widget-content no-padding">
					<table class="table table-hover table-striped table-bordered table-highlight-head">
						<thead>
							<tr>
								<th>
									ID
								</th>
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
							@foreach($launchers as $vo)
								<tr>
									<td>
										{{$vo->id}}
									</td>
									<td>
										{{$vo->filename}}
										@if(Storage::disk('uploads')->exists($vo->fuzzyname) == 1)
											<a href="{{ url('download', ['package' => $vo->fuzzyname]) }}" onclick="if(confirm('{{"下载文件 " . $vo->filename . "?"}}') == false) return false;">
												<i class="icon-download" style="float: right" title="下载">
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
												@can('launcher_validate')
													<a href="{{ url('launcher/check?id='.$vo->id) }}">
														<i class="icon-ok" style="float: right" title="完成上线测试">
														</i>
													</a>
												@endcan
											@endif
										@else
											上线
											@if (isset($release))
												@can('launcher_validate')
													<a href="{{ url('launcher/uncheck?id='.$vo->id) }}">
														<i class="icon-undo" style="float: right" title="下线测试">
														</i>
													</a>
												@endcan
											@endif
										@endif
									</td>
									<td>
										@if (array_key_exists($vo->id, $devices))
											<a href="{{ url('devices/search?key=launcher:'.$vo->id) }}" title="显示设备列表">
												{{ $devices[$vo->id] }}
											</a>
											@can('launcher_upgrade')
												<a href="javascript:;" onclick="upgrade('{{ $vo->id }}', '{{ $vo->filename }}', '{{ $vo->devtype }} ');">
													<i class="icon-signout" style="float: right" title="升级设备到其它版本">
													</i>
												</a>
											@endcan
										@else
											0
											@can('launcher_delete')
												<a href="{{ url('launcher/delete?id='.$vo->id) }}" onclick="if(confirm('确定删除?') == false) return false;">
													<i class="icon-trash" style="float: right" title="删除">
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
			<div class="modal-footer" style="border: 1px solid #e5e5e5;">
				<a href="javascript:history.back(-1)">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						返回
					</button>
				</a>
			</div>
		</div>
	</div>

@if (!empty($release))
	<div class="modal fade" id="uploadLauncherModalTest">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						上传文件
					</h4>
				</div>
				<form class="form-horizontal row-border" action="{{ url('launcher/uploadTest') }}" method="post" enctype="multipart/form-data">
	
					{{ csrf_field() }}
	
					<div class="modal-body">
						<input class="form-control" type="hidden" name="version" value="{{ $release->version }}">
						<div class="form-group">
							<label for="file" class="col-md-3 control-label">
								请选择文件
							</label>
							<div class="col-md-9" style="margin-top: 3px;">
								<input id="file" class="form-control" name="source" type="file" required autofocus>
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

@if (!empty($release))
	<div class="modal fade" id="uploadLauncherModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						上传文件
					</h4>
				</div>
				<form class="form-horizontal row-border" action="{{ url('launcher/upload') }}" method="post" enctype="multipart/form-data">
	
					{{ csrf_field() }}
	
					<div class="modal-body">
						<input class="form-control" type="hidden" name="version" value="{{ $release->version }}">
						<div class="form-group">
							<label for="file" class="col-md-3 control-label">
								请选择文件
							</label>
							<div class="col-md-9" style="margin-top: 3px;">
								<input id="file" class="form-control" name="source" type="file" required autofocus>
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
	@can('launcher_upgrade')
		<div class="modal fade" id="upgradeLauncherModal">
			<div class="modal-dialog" style="width: 1000px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title">
							升级启动程序
						</h4>
					</div>
					<div class="modal-body">
						<form class="form-horizontal row-border" action="{{ url('launcher/upgrade') }}" method="post">
							{{ csrf_field() }}
							<table class="table table-hover table-striped table-bordered table-highlight-head">
								<input id="upgradeLauncherModal_launcher_id" class="form-control" type="hidden" name="id">
								<tbody>
									<tr>
										<td class="col-md-2">
											<strong>
												版本
											</strong>
										</td>
										<td>
											<input id="upgradeLauncherModal_launcher" class="form-control" type="text" readonly>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												升级到
											</strong>
										</td>
										<td>
											<select id="upgradeLauncherModal_launchers" class="form-control" name="to" required>
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
		function upgrade(lchid, lchname, devtype)
		{
			$('#upgradeLauncherModal_launcher_id').val(lchid);
			$('#upgradeLauncherModal_launcher').val(lchname);

			$.getJSON('{{ url('launcher/getLauncherList') }}', {devtype: devtype}, function(data){
				var options = '';
				options += '<option>请选择</option>';
				for(var i=0; i<data.length; i++)
				{
					if (data[i].test == 0)
						options+="<option value="+data[i].id+">"+data[i].filename+"</option>";
				}
				$('#upgradeLauncherModal_launchers').html(options);
			});

			$('#upgradeLauncherModal').modal();
		}
	</script>
@stop
