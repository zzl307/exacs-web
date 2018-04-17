@extends('common.layouts')

@section('menu')
	设备管理
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
						设备日志
					</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-refresh">
								<i class="icon-refresh">
								</i>
								刷新
							</span>
							<a class="btn btn-xs" href="javascript:history.go(-1)">
								<i class="icon-chevron-left">
								</i>
								返回
							</a>
						</div>
					</div>
				</div>

				<div class="widget-content no-padding">
					<form class="form-horizontal row-border" action="" method="get">
						<div class="form-group" style="margin-top: 10px;">
							<div class="col-md-2">
								<input class="form-control datepicker" name="date" type="text" value="{{ isset($data) ? $data['date'] : date('Y-m-d', time()) }}" placeholder="日期" required>
							</div>
   							<div class="col-md-2" style="margin-left: -15px;">
								<input class="form-control" name="device_id" value="{{ isset($data) ? $data['device_id'] : '' }}" type="text" placeholder="设备号">
							</div>
							<div class="col-md-2" style="margin-left: -15px;">
								<select class="form-control col-md-12 full-width-fix" name="level" value="{{ isset($data['level']) ? $data['level'] : '' }}" style="width: 10%;">
									<option value="">全部</option>
									@for($i = 1; $i <= 7; $i++)
										<option value="{{ $i }}" @if(isset($data['level'])) {{ $data['level'] == $i ? 'selected' : '' }} @endif>
											{{ $i }}
										</option>
									@endfor
								</select>
							</div>
							<button class="btn btn-sm btn-info" style="padding: 5px 16px;">搜索</button>
						</div>
					</form>
				</div>

				@if(isset($stats))
					<div class="widget-content no-padding">
						<table class="table table-hover table-striped table-bordered table-highlight-head">
							<thead style="border-top: 1px solid #ddd;">
								<tr>
									<th>
										数据表
									</th>
									<th>
										数据量（{{ round($stats['total']/1024/1024) }}MB）
									</th>
									<th>
										记录数
									</th>
									@can('device_log_delete')
										<th>
										</th>
									@endcan
								</tr>
							</thead>
							<tbody>
								@foreach ($stats['tables'] as $vo)
									<tr>
										<td>
											{{ $vo['tbname'] }}
										</td>
										<td>
											{{ round($vo['bytes']/1024/1024) }}MB
										</td>
										<td>
											{{ $vo['records'] }}
										</td>
										@can('device_log_delete')
											<td>
												<a href="{{ url('devices/logs/delete?tbname='.$vo['tbname']) }}" onclick="if(confirm('{{"确定删除?"}}') == false) return false;" title="删除">
													<i class="icon-trash">
													</i>
												</a>
											</td>
										@endcan
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<div class="widget-content no-padding">
						<table class="table table-hover table-striped table-bordered table-highlight-head">
							<thead style="border-top: 1px solid #ddd;">
								<tr>
									<th class="col-md-2">
										时间
									</th>
									<th class="col-md-2">
										设备号
									</th>
									<th class="col-md-2">
										级别
									</th>
									<th>
										内容
									</th>
								</tr>
							</thead>
							<tbody>
								@if(isset($logs))
									@foreach ($logs as $vo)
										<tr>
											<td>
												{{ $vo->time }}
											</td>
											<td>
												<a href="{{ url('devices/search?key='.$vo->device_id) }}">
													{{ $vo->device_id }}
												</a>
											</td>
											<td>
												{{ $vo->level }}
											</td>
											<td>
												{{ $vo->log }}
											</td>
										</tr>
									@endforeach
								@endif
							</tbody>
						</table>
						<div class="row">
							<div class="table-footer">
								<div>
									@if(isset($logs))
										{{ $logs->appends(['date' => isset($data['date']) ? $data['date'] : '', 'device_id' => isset($data['device_id']) ? $data['device_id'] : '', 'level' => isset($data['level']) ? $data['level'] : ''])->links() }}
									@endif
								</div>
							</div>
						</div>
					</div>
				@endif

			</div>
		</div>
	</div>
@stop

@section('javascript')
	<script type="text/javascript" src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$(".datepicker").datepicker({
				inline: true,
				defaultDate: +7,
				showOtherMonths: true,
				autoSize: true,
				dateFormat: "yy-mm-dd"
			});
		});
	</script>
@stop
