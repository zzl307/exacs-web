@extends('common.layouts')

@section('menu')
	脚本详情
@stop

@section('content')

	<div class="row">
		<div class="col-md-12">
			
			@include('common.message')

			<div class="widget box">
				<div class="widget-header" style="border-bottom: 0px;">
					<h4>
						<i class="icon-reorder">
						</i>
						脚本运行设备
					</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-refresh">
								<i class="icon-refresh">
								</i>
								刷新
							</span>
							<a class="btn btn-xs" href="{{ url('devices/deviceExec') }}">
								<i class="icon-chevron-left">
								</i>
								返回
							</a>
						</div>
					</div>
				</div>

				<div class="widget-content no-padding">
					<table class="table table-hover table-striped table-bordered table-highlight-head">
						<tbody>
							<tr>
								<td style="margin: 0px;padding: 0px;">
									<table class="table table-hover table-striped table-bordered table-highlight-head datatable">
										<thead>
											<tr>
												<th>
													设备
												</th>
												<th>
													运行状态
												</th>
												<th>
													
												</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($device as $vo)
												<tr>
													<td class="col-md-5">
														{{ $vo->device_id }}
													</td>
													@if ($vo->status == 1)
														<td class="col-md-5">
															<span class="label label-success">
																正常
															</span>
														</td>
													@else
														<td class="col-md-5">
															<span class="label label-warning">
																异常
															</span>
														</td>
													@endif
													<td class="col-md-2">
														<a href="javascript:;" onclick="deviceJobExecDelete('{{ $vo->id }}');" class="bs-tooltip" title="删除">
															<i class="icon-trash" style="color: #555;">
																
															</i>
														</a>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="4">
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#package" style="text-decoration: none;">
										<strong>
											脚本内容
										</strong>
										<i class="icon-caret-down">
										</i>
									</a>
									<div id="package" class="panel-collapse collapse">
										<div class="panel-body">
											<pre style="border: none; background: none;">
												{{ $device_exec_content[0]->content }}
											</pre>
										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@stop

@section('javascript')
	<script type="text/javascript">
		function deviceJobExecDelete(id){
			if(confirm('确定删除设备运行脚本?') == false){
				return false;
			}

			window.location.href = '{{ url('devices/deviceExecDelete?id=') }}'+id+'';
		}
	</script>
@endsection
