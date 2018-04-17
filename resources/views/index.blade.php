@extends('common.layouts')

@section('style')
	<style>
		.widget-content.no-padding .dataTables_header{
			border-top: 1px solid #ddd;
		}
	</style>
@stop

@section('menu')
	设备管理
@stop

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4>
						<i class="icon-reorder">
						</i>
						设备在线率状况
					</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-refresh">
								<i class="icon-refresh">
								</i>
								刷新
							</span>
						</div>
					</div>
				</div>

				<div class="widget-content no-padding">
					<table class="table table-hover table-striped table-bordered table-highlight-head">
						<thead>
							<tr>
								<th style="vertical-align: middle; text-align: center;">
									标签分类
								</th>
								<th style="vertical-align: middle; text-align: center;">
									场所总数
								</th>
								<th style="vertical-align: middle; text-align: center;">
									在线数
								</th>
								<th style="vertical-align: middle; text-align: center;">
									在线率
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($devicesByTag as $key => $vo)
								<tr>
									<td>
										<a href="{{ url('devices/search?key=').$key }}">
											{{ $key }}
										</a>
									</td>
									<td>
										{{ $vo['devices'] }}
									</td>
									<td>
										{{ $vo['online'] }}
									</td>
									<td>
										@if ($vo['rate'] < 50)
											<span class="label label-danger">{{ $vo['rate'] }}%</span>
										@elseif ($vo['rate'] < 80)
											<span class="label label-warning">{{ $vo['rate'] }}%</span>
										@elseif ($vo['rate'] == 100)
											<span class="label label-success">{{ $vo['rate'] }}%</span>
										@else
											{{ $vo['rate'] }}%
										@endif
									</td>
								</tr>
							@endforeach
							<tfoot>
								<tr>
									<th>
										全部
									</th>
									<th>
										{{ $total['devices'] }}
									</th>
									<th>
										{{ $total['online'] }}
									</th>
									<th>
										{{ $total['rate'] }}%
									</th>
							</tfoot>
						</tbody>
					</table>
				</div>		
			</div>		
		</div>
	</div>
@stop
