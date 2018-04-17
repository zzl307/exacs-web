@extends('common.layouts')

@section('menu')
	下载服务器
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
						下载服务器
					</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-refresh">
								<i class="icon-refresh">
								</i>
								刷新
							</span>
							@can('system_config')
								<a class="btn btn-xs" href="#AddDownloadServerModal" data-toggle="modal">
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
									名称
								</th>
								<th class="col-md-4">
									下载地址
								</th>
								<th class="col-md-1">
									运行中
								</th>
								<th class="col-md-1">
									特用
								</th>
								<th class="col-md-1">
									同步
								</th>
								<th class="col-md-2">
									同步时间
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($dlservers as $vo)
								<tr>
									<td>
										{{ $vo->name }}
										@can('system_config')
											<span style="float: right;">
												<a href="{{ url('config/dlserver/delete?id='.$vo->id) }}" class="bs-tooltip" title="删除" onclick="if(confirm('{{"确定删除?"}}') == false) return false;">
													<i class="icon-trash" style="color: #555;">
													</i>
												</a>
											</span>
										@endcan											
									</td>
									<td>
										{{ $vo->server }}
									</td>
									<td>
										{{ $vo->in_service ? '是' : '否' }}
									</td>
									<td>
										{{ $vo->exclude ? '是' : '否' }}
									</td>
									<td>
										@if ($vo->sync_status)
											<span class="label label-success">
												成功
											</span>
										@else
											<span class="label label-danger">
												失败
											</span>
										@endif
									</td>
									<td>
										{{ $vo->sync_time }}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
@stop
