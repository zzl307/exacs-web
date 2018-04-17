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
						启动程序版本列表
					</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<a class="btn btn-xs" href="{{ url('launcher/files') }}">
								<i class="icon-list">
								</i>
								所有文件
							</a>
							<span class="btn btn-xs widget-refresh">
								<i class="icon-refresh">
								</i>
								刷新
							</span>
							@can('launcher_release')
								<a class="btn btn-xs" href="#addLauncherRelease" data-toggle="modal">
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
								<th class="col-md-2">
									版本
								</th>
								<th>
									发布时间
								</th>
								<th class="col-md-6">
									发布说明
								</th>
								@can('launcher_release')
									<th>
									</th>
								@endcan
							</tr>
						</thead>
						<tbody>
							@foreach($releases as $vo)
								<tr>
									<td>
										<a href="{{ url('launcher/files?version='.$vo->version) }}">
											{{ $vo->version }}
										</a>
										{{ $vo->test ? ' (测试版本)' : '' }}
									</td>
									<td>
										{{ $vo->time }}
									</td>
									<td>
										<?php echo @nl2br($vo->note); ?>
									</td>
									@can('launcher_release')
										<td>
											<a href="javascript:;" onclick="update('{{ $vo->version}}')">
												<i class="icon-edit" title="修改">
												</i>
											</a>
											&nbsp;
											<a href="{{ url('launcher/release/delete?version='.$vo->version) }}" onclick="if(confirm('{{"确定删除?"}}') == false) return false;">
												<i class="icon-trash" title="删除">
												</i>
											</a>
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

@can('launcher_release')
	<div class="modal fade" id="addLauncherRelease">
		<div class="modal-dialog" style="width: 1000px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						发布启动程序新版本
					</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal row-border" action="{{ url('launcher/release/add') }}" method="post">
						{{ csrf_field() }}
						<table class="table table-hover table-striped table-bordered table-highlight-head">
							<tbody>
								<tr>
									<td class="col-md-2">
										<strong>
											版本
										</strong>
									</td>
									<td>
										<input class="form-control" type="text" name="version" required autofocus>
									</td>
								</tr>
								<tr>
									<td>
										<strong>
											发布说明
										</strong>
									</td>
									<td>
										<textarea rows="10" class="form-control" name="note" requried></textarea>
									</td>
								</tr>
								<tr>
									<td>
										<strong>
											测试版本
										</strong>
									</td>
									<td>
	                                    <label class="radio-inline">
	                                        <input type="radio" class="uniform" name="test" value="1" checked>
	                                        是
	                                    </label>
	                                    <label class="radio-inline">
	                                        <input type="radio" class="uniform" name="test" value="0">
	                                        否
	                                    </label>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="modal-footer" style="border: 1px solid #e5e5e5;">
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

	<div class="modal fade" id="editLauncherReleaseModal">
		<div class="modal-dialog" style="width: 1000px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						启动程序版本修改
					</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal row-border" action="{{ url('launcher/release/update') }}" method="post">
						{{ csrf_field() }}
						<table class="table table-hover table-striped table-bordered table-highlight-head" style="border: 1px solid #ddd;">
							<tbody>
								<tr>
									<td class="col-md-2">
										<strong>
											版本
										</strong>
									</td>
									<td>
										<input id="editLauncherReleaseModal_version" class="form-control" type="text" name="version" readonly>
									</td>
								</tr>
	
								<tr>
									<td>
										<strong>
											发布说明
										</strong>
									</td>
									<td>
										<textarea id="editLauncherReleaseModal_note" rows="10" class="form-control" name="note"></textarea>
									</td>
								</tr>
	
								<tr>
									<td>
										<strong>
											测试版本
										</strong>
									</td>
									<td>
	                                    <label class="radio-inline">
	                                        <input id="editLauncherReleaseModal_test_1" type="radio" name="test" value="1">
	                                        是
	                                    </label>
	                                    <label class="radio-inline">
	                                        <input id="editLauncherReleaseModal_test_0" type="radio" name="test" value="0">
	                                        否
	                                    </label>
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
		function update(version)
		{
			$('#editLauncherReleaseModal_version').val(version);

			$.getJSON('{{ url('launcher/release/info') }}', {version: version}, function(data){
				$('#editLauncherReleaseModal_note').val(data.note);
				if (data.test > 0)
					$('#editLauncherReleaseModal_test_1').attr('checked', 'checked');
				else
					$('#editLauncherReleaseModal_test_0').attr('checked', 'checked');
			});
			$('#editLauncherReleaseModal').modal();
		}
@endcan
	</script>
@stop
