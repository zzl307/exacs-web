<div class="modal fade" id="deviceCpuAlarmModal">
	<div class="modal-dialog" style="width: 1000px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title">
					设备报警详情
				</h4>
			</div>
			<div class="modal-body">
				<table class="table table-hover table-striped table-bordered table-highlight-head datatable">
					<thead>
						<th>
							设备号
						</th>
						<th>
							运行时长
						</th>
						<th>
							CPU使用
						</th>
						<th>
							内存使用
						</th>
						<th>
							总内存
						</th>
						<th>
							可用内存
						</th>
					</thead>
					<tbody>
						@foreach ($deviceCpuAlarm as $vo)
							<tr>
								<td class="col-md-2">
									{{ $vo['device_id'] }}
								</td>
								<td>
									{{ \App\Device::secToTime($vo['uptime']) }}
								</td>
								<td>
									<span class="label label-{{ $vo['cpu'] > 70 ? 'danger' : 'success' }}">
										{{ $vo['cpu'] }}%
									</span>
								</td>
								<td>
									<span class="label label-{{ $vo['mem'] > 70 ? 'danger' : 'success' }}">
										{{ $vo['mem'] }}%
									</span>
								</td>
								<td>
									{{ $vo['mem_total'] }}
								</td>
								<td>
									{{ $vo['mem_free'] }}
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="modal-footer table-bordered">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						关闭
					</button>
				</div>
			</div>
		</div>
	</div>
</div>