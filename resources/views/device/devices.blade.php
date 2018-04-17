@extends('common.layouts')

@section('menu')
	设备管理
@stop

@section('style')
	<style>
		.widget-content.no-padding .dataTables_header{
			border-top: 1px solid #ddd;
		}
		code{
			display: block;
			float: left;
			padding: 0 8px;
			margin: 5px 5px 5px 5px;
			line-height: 23px;
			font-size: 11px;
			border: 0px;
			background: #fff;
		}
	</style>
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
						设备管理
					</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							@can('device_config')
								<a id="batchSetButton" class="btn btn-xs" href="#" data-toggle="dropdown" style="border-right: 0px;">
									<i class="icon-magic">
									</i>
									批量操作
									<i class="icon-caret-down small">
									</i>
								</a>
							@endcan
							<ul class="dropdown-menu">
								@can('launcher_upgrade')
									<li>
										<a href="javascript:;" onclick="batchSetLauncher();">
											设置启动程序
										</a>
									</li>
								@endcan
								@can('package_upgrade')
									<li>
										<a href="javascript:;" onclick="batchSetPackage();">
											设置应用软件
										</a>
									</li>
								@endcan
								@can('device_config')
									<li>
										<a href="javascript:;" onclick="deviceExecAdd();">
											运行脚本
										</a>
									</li>
								@endcan
								@can('device_config')
									<li>
										<a href="javascript:;" onclick="batchDeleteDevice();">
											删除设备
										</a>
									</li>
								@endcan
							</ul>
							@if (isset($deviceCpuAlarm))
								<a data-toggle="modal" href="#deviceCpuAlarmModal" style="float: right;">
									<span class="btn btn-xs btn-danger" style="border-right: 0px;">
										<i class="icon-bell">
										</i>
										报警 ({{ count($deviceCpuAlarm) }})
									</span>
								</a>
							@endif
							@if(isset($data['key']))
								<a href="{{ url('devices/DerviceExport?key=').$data['key'] }}" style="float: right;">
									<span class="btn btn-xs btn-success" style="border-left: 0px;">
										<i class="icon-file-text-alt">
										</i>
										导出
									</span>
								</a>
							@endif
							@can('device_config')
								<a data-toggle="modal" href="#addDeviceModal">
									<span class="btn btn-xs" style="border-left: 0px;">
										<i class="icon-plus">
										</i>
										新增
									</span>
								</a>
							@endcan
							<span class="btn btn-xs widget-refresh">
								<i class="icon-refresh">
								</i>
								刷新
							</span>
						</div>
					</div>
				</div>

				<div class="widget-content no-padding">
					<form class="form-horizontal" action="{{ url('devices/search') }}" method="get">
						<div class="form-group" style="margin-top: 10px;">
   							<div class="col-md-3">
								<input class="form-control" name="key" value="{{ isset($data) && collect(\App\DeviceType::all()->toArray())->contains('name', $data['key']) ? '' : isset($data['key']) ? $data['key'] : '' }}" type="text" placeholder="设备号／设备IP／软件名称／标签">
							</div>
   							<div class="col-md-2" style="margin-left: -15px;">
								<select class="select2-select-00 col-md-12 full-width-fix" title="设备类型" name="devtype">
									<option value="0">设备类型</option>
									@foreach($deviceType as $vo)
										<option value="{{ $vo->name }}" {{ isset($data['devtype']) ? $data['devtype'] == $vo->name ? 'selected' : '' : '' }}>{{ $vo->name }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-2" style="margin-left: -15px;">
								<select class="select2-select-00 col-md-12 full-width-fix" title="分页显示" name="list">
									<option value="15" {{ isset($data) && $data['list'] == 15 ? 'selected' : '' }}>15</option>
									<option value="25" {{ isset($data) && $data['list'] == 25 ? 'selected' : '' }}>25</option>
									<option value="50" {{ isset($data) && $data['list'] == 50 ? 'selected' : '' }}>50</option>
									<option value="100" {{ isset($data) && $data['list'] == 100 ? 'selected' : '' }}>100</option>
								</select>
							</div>
							<button class="btn btn-sm" style="padding: 5px 10px;">搜索设备</button>
						</div>
					</form>
				</div>

				@if(!isset($data))
					<div class="widget-content no-padding">
						<table class="table">
							<tbody>
								<tr>
									<td style="border: 0px;">
										@foreach($devs as $name => $count)
											<code>
												@if (empty($name))
													<a href="{{ url('devices/search?launcher=0') }}">
														{{ '未知('.$count.')' }}
													</a>
												@else
													<a href="{{ url('devices/search?key='.$name) }}">
														{{ $name.'('.$count.')' }}
													</a>
												@endif
											</code>
										@endforeach
									</td>
								</tr>
								<tr>
									<td style="border: 0px;">
										@foreach($pkgs as $name => $count)
											<code>
												<a href="{{ url('devices/search?key='.$name) }}">
													{{ $name.'('.$count.')' }}
												</a>
											</code>
										@endforeach
									</td>
								</tr>
								<tr>
									<td style="border: 0px;">
										@foreach($tags as $tag => $count)
											@if($count >= 5)
												<code>
													<a href="{{ url('devices/search?key='.$tag) }}">
														{{ $tag.'('.$count.')' }}
													</a>
												</code>
											@endif
										@endforeach
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				@elseif(empty($devices))
					<div class="widget-content no-padding">
						<code>
							没有找到适合条件的设备！
						</code>
					</div>
				@else
					<div class="widget-content no-padding">
						<table class="table table-hover table-striped table-bordered table-highlight-head table-checkable" style="border-top: 1px solid #ddd;">
							<thead>
								<tr>
									@can('device_config')
										<th class="checkbox-column">
											<input type="checkbox" class="uniform">
										</th>
									@endcan
									<th class="col-md-2">
										设备号
									</th>
									<th class="col-md-1">
										设备类型
									</th>
									<th class="col-md-1">
										设备IP
									</th>
									<th class="col-md-2">
										设备信息
									</th>
									<th class="col-md-1">
										启动程序
									</th>
									<th class="col-md-2">
										应用软件
									</th>
									<th class="col-md-2">
										设备标签
									</th>
									<th>
										状态
									</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($devices as $device_id => $device)
									<tr>
										@can('device_config')
											<td class="checkbox-column">
												<input type="checkbox" class="uniform" name="device_id" value="{{ $device_id }}">
												<input type="hidden" name="devtype" value="{{ $device['device_type'] }}">
											</td>
										@endcan
										<td>
											<a href="javascript:;" onclick="showDevice('{{ $device_id }}');" class="bs-tooltip" title="设备详情">
												{{ $device_id }}
											</a>
											<span style="float: right;">
												<a href="{{ url('devices/logs?device_id='.$device_id) }}" class="bs-tooltip" title="设备运行日志">
													<i class="icon-bar-chart" style="color: #555;"></i>
												</a>
												@can('device_config')
													<a href="javascript:;" onclick="editDevice('{{ $device_id }}', '{{ $device["device_type"]}}');" class="bs-tooltip" title="修改">
														<i class="icon-edit" style="color: #555;">
														</i>
													</a>
												@endcan
											</span>
										</td>
										<td>
											{{ $device['device_type'] }}
										</td>
										<td>
											{{ $device['ip_address'] }}
										</td>
										<td>
											{{ $device['device_info'] }}
										</td>
										<td>
											@if($device['launcher_immune'] > 0)
												免于更新
											@elseif($device['launcher_id'] == 0)
												自动
											@else
												{{ preg_replace(['/exa-launcher-/', '/\.'.$device['device_type'].'/'], ['', ''], $device['launcher']) }}
											@endif
										</td>
										<td>
											@foreach($device['packages'] as $devpkg)
												@if(!empty($devpkg['package']))
													<div>
														@can('device_config')
															<a href="javascript:;" onclick="editPackage('{{ $device_id }}', '{{ $device['device_type'] }}', '{{ $devpkg['id'] }}', '{{ $devpkg['package_name'] }}', '{{ $devpkg['package_type'] }}', '{{ $devpkg['package'] }}', '{{ $devpkg['id'] }}');" title="修改">
																{{ $devpkg['package'] }}
															</a>
														@else
															{{ $devpkg['package'] }}
														@endcan
													</div>
												@endif
											@endforeach
											@can('device_config')
												<div>
													<a href="javascript:;" class="bs-tooltip" title="添加应用软件" onclick="addPackage('{{ $device_id }}', '{{ $device['device_type'] }}');">
														<i class="icon-plus" style="color: #555;">
														</i>
													</a>
												</div>
											@endcan
										</td>
										<td>
											@foreach($device['tags'] as $tag)
												<a href="{{ url('devices/search?key='.$tag) }}" class="bs-tooltip" title="标签所属设备">
													{{ $tag }}
												</a>
											@endforeach
										</td>
										<td>
											@if(\App\Device::isDisabled($device))
												<span class="label label-warning">
													禁用
												</span>
											@elseif(\App\Device::isExpired($device))
												<span class="label label-danger">
													过期
												</span>
											@elseif(!\App\Device::isOnline($device))
												<span class="label label-danger">
													离线
												</span>
											@elseif($device['status_code'] == 0)
												<span class="label label-warning">
													未运行
												</span>
											@elseif($device['status_code'] == 200)
												<span class="label label-success">
													正常
												</span>
											@elseif($device['status_code'] == 206)
												<span class="label label-success">
													配置中
												</span>
											@elseif($device['status_code'] == 426)
												<span class="label label-warning">
													升级中
												</span>
											@elseif($device['status_code'] == 428)
												<span class="label label-warning">
													执行脚本
												</span>
											@else
												<span class="label label-warning">
													异常
												</span>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<div class="dataTables_footer clearfix" style="padding: 12px 0;border-top: 1px solid #ddd;">
						<div class="col-md-6">
							<div class="dataTables_info">
								<strong>总计：{{ $total }}</strong>
							</div>
						</div>
						<div class="col-md-6">
							<div class="dataTables_paginate paging_bootstrap">
								@if(isset($paginator))
									{{ $paginator->links() }}
								@endif
							</div>
						</div>
					</div>
				@endif
			</div>
		</div>
	</div>

	@can('device_config')
		<div class="modal fade" id="addDeviceModal">
			<div class="modal-dialog" style="width: 1000px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title">
							新增设备
						</h4>
					</div>
					<div class="modal-body">
						<form class="form-horizontal row-border" action="{{ url('devices/addDevice') }}" method="post">
							{{ csrf_field() }}
							<table class="table table-hover table-striped table-bordered table-highlight-head">
								<tbody>
									<tr>
										<td class="col-md-2">
											<strong>
												设备号
											</strong>
										</td>
										<td class="col-md-6">
											<input class="form-control" name="device_id" type="text" required autofocus>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												设备类型
											</strong>
										</td>
										<td>
											<select class="form-control" name="devtype">
												@foreach(App\DeviceType::all() as $vo)
													<option value="{{ $vo->name }}">
														{{ $vo->name }}
													</option>
												@endforeach
											</select>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												下载服务器
											</strong>
										</td>
										<td>
											<select class="form-control" name="dlserver_id">
												<option value="0">
													自动
												</option>
												@foreach(App\DownloadServer::all() as $vo)
													<option value="{{ $vo->id }}">
														{{ $vo->name }}
													</option>
												@endforeach
											</select>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												运行截止日期
											</strong>
										</td>
										<td>
											<input class="form-control datepicker" name="expire_time" type="text">
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												设备标签
											</strong>
										</td>
										<td>
											<input class="form-control" name="tags" type="text">
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
	@endcan

		<div class="modal fade" id="showDeviceModal">
			<div class="modal-dialog" style="width: 1270px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title">
							设备详情
						</h4>
					</div>
					<div class="modal-body">
						<table class="table table-hover table-striped table-bordered table-highlight-head">
							<tbody>
								<tr>
									<td class="col-md-2"><strong>基本信息</strong></td>
									<td style="margin: 0px; padding: 0px;">
										<table class="table table-hightlight-head">
											<thead>
												<tr>
													<td><strong>设备号</strong></td>
													<td><strong>设备类型</strong></td>
													<td><strong>设备信息</strong></td>
													<td><strong>创建时间</strong></td>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td class="col-md-3" id="showDeviceModal_device_id"></td>
													<td class="col-md-3" id="showDeviceModal_devtype"></td>
													<td class="col-md-3" id="showDeviceModal_devinfo"></td>
													<td class="col-md-3" id="showDeviceModal_createTime">
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td class="col-md-2"><strong>设备配置</strong></td>
									<td style="margin: 0px; padding: 0px;">
										<table class="table table-hightlight-head">
											<thead>
												<tr>
													<td><strong>禁用</strong></td>
													<td><strong>截止时间</strong></td>
													<td><strong>启动程序</strong></td>
													<td><strong>免于更新</strong></td>
													<td><strong>下载服务器</strong></td>
													<td><strong>配置时间</strong></td>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td class="col-md-1" id="showDeviceModal_disabled"></td>
													<td class="col-md-2" id="showDeviceModal_expireTime">
													<td class="col-md-3" id="showDeviceModal_launcher"></td>
													<td class="col-md-2" id="showDeviceModal_immune"></td>
													<td class="col-md-2" id="showDeviceModal_dlserver"></td>
													<td class="col-md-2" id="showDeviceModal_configTime">
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td class="col-md-2"><strong>运行状态</strong></td>
									<td style="margin: 0px; padding: 0px;">
										<table class="table table-hightlight-head">
											<thead>
												<tr>
													<td><strong>公网IP</strong></td>
													<td><strong>在线时间</strong></td>
													<td><strong>状态码</strong></td>
													<td><strong>再次时长</strong></td>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td class="col-md-3" id="showDeviceModal_ip"></td>
													<td class="col-md-3" id="showDeviceModal_updateTime">
													<td class="col-md-3" id="showDeviceModal_statusCode"></td>
													<td class="col-md-3" id="showDeviceModal_retryAfter"></td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td class="col-md-2">
										<strong>
											设备系统状态
										</strong>
									</td>
									<td style="margin: 0px; padding: 0px;">
										<table class="table table-hightlight-head">
											<thead>
												<tr>
													<td class="col-md-3"><strong>运行时长</strong></td>
													<td class="col-md-3"><strong>CPU使用</strong></td>
													<td class="col-md-3"><strong>总内存</strong></td>
													<td class="col-md-3"><strong>可用内存</strong></td>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td id="showDeviceModal_uptime"></td>
													<td id="showDeviceModal_cpuinfo"></td>
													<td id="showDeviceModal_memTotal"></td>
													<td id="showDeviceModal_memFree"></td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td><strong>设备标签</strong></td>
									<td id="showDeviceModal_tags"></td>
								</tr>
								<tr>
									<td><strong>VTUND地址</strong></td>
									<td id="showDeviceModal_vtund_id"></td>
								</tr>
								<tr>
									<td colspan="4">
										<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#package" style="text-decoration: none;">
											<strong>
												应用程序
											</strong>
											<i class="icon-caret-down">
											</i>
										</a>
										<div id="package" class="panel-collapse collapse">
											<div class="panel-body" id="showDeviceModal_packages">
											</div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="modal-footer table-bordered">
							@can('device_config')
								<a href="javascript:;" onclick="deleteDevice();" class="btn btn-danger">
									删除设备
								</a>
							@endcan
							<button type="button" class="btn btn-default" data-dismiss="modal" id="closeButton">
								关闭
							</button>
	                    </div>
					</div>
				</div>
			</div>
		</div>

	@can('device_config')
		<div class="modal fade" id="editDeviceModal">
			<div class="modal-dialog" style="width: 1000px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title">
							设备配置修改
						</h4>
					</div>
					<div class="modal-body">
						<form class="form-horizontal row-border" action="{{ url('devices/updateDevice') }}" method="post">
							{{ csrf_field() }}
							<table class="table table-hover table-striped table-bordered table-highlight-head">
								<tbody>
									<tr>
										<td class="col-md-2">
											<strong>
												设备号
											</strong>
										</td>
										<td class="col-md-6">
											<input id="editDeviceModal_device_id" class="form-control" name="device_id" type="text" readonly>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												设备类型
											</strong>
										</td>
										<td>
											<input id="editDeviceModal_devtype" class="form-control" name="devtype" type="text" readonly>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												禁用
											</strong>
										</td>
										<td>
											<label class='radio-inline'>
												<input id="editDeviceModal_disabled_0" type="radio" name='disabled' value='0'>
												否
											</label>
											<label class='radio-inline'>
												<input id="editDeviceModal_disabled_1" type="radio" name='disabled' value='1'>
												是
											</label>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												启动程序
											</strong>
										</td>
										<td>
											<select id="editDeviceModal_launchers" class="form-control" name="launcher_id">
											</select>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												启动程序免于更新
											</strong>
										</td>
										<td>
											<label class='radio-inline'>
												<input id="editDeviceModal_immune_0" type="radio" name='launcher_immune' value='0'>
												否
											</label>
											<label class='radio-inline'>
												<input id="editDeviceModal_immune_1" type="radio" name='launcher_immune' value='1'>
												是
											</label>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												下载服务器
											</strong>
										</td>
										<td>
											<select id="editDeviceModal_dlservers" class="form-control" name="dlserver_id">
												<option value="0">
													自动
												</option>
												@foreach(App\DownloadServer::all() as $vo)
													<option value="{{ $vo->id }}">
														{{ $vo->name }}
													</option>
												@endforeach
											</select>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												运行截止日期
											</strong>
										</td>
										<td>
											<input id="editDeviceModal_expire_time" class="form-control datepicker" name="expire_time" type="text">
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												设备标签
											</strong>
										</td>
										<td>
											<input id="editDeviceModal_tags" class="form-control" name="tags" type="text">
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												开启VTUN
											</strong>
											<span class="label label-info pull-right">
												设置后10分钟生效												
											</span>
										</td>
										<td>
											<label class='radio-inline'>
												<input id="editDeviceModal_vtun_disabled_0" type="radio" name='device_disabled' value='0'>
												否
											</label>
											<label class='radio-inline'>
												<input id="editDeviceModal_vtun_disabled_1" type="radio" name='device_disabled' value='1'>
												是
											</label>
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
	@endcan

	@can('device_config')
		<div class="modal fade" id="addPackageModal">
			<div class="modal-dialog" style="width: 1000px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title">
							设备新增软件配置
						</h4>
					</div>
					<div class="modal-body">
						<form class="form-horizontal row-border" action="{{ url('devices/addPackage') }}" method="post">
							{{ csrf_field() }}
							<table class="table table-hover table-striped table-bordered table-highlight-head">
								<tbody>
									<tr>
										<td class='col-md-3'>
											<strong>
												设备号
											</strong>
										</td>
										<td>
											<input id="addPackageModal_device_id" class='form-control' type='text' name="device_id" readonly>
											<input id="addPackageModal_devtype" class='form-control' type='hidden'>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												软件版本
											</strong>
										</td>
										<td>
											<div class='col-md-4' style='margin-left: -15px;'>
												<select id="addPackageModal_pkgname" class='form-control col-md-12 full-width-fix' name='pkgname' style='width: 10%;' onchange="addPackageModal_updatePackages()" required>
													<option value=''>请选择软件名称</option>
													@foreach (\App\PackageName::all() as $vo)
														<option value="{{ $vo->name }}">{{ $vo->name }}</option>
													@endforeach
												</select>
											</div>
											<div class='col-md-4' style='margin-left: -15px;'>
												<select id="addPackageModal_pkgtype" class='form-control col-md-12 full-width-fix' name='pkgtype' style='width: 10%;' onchange="addPackageModal_updatePackages()" required>
													<option value=''>请选择软件类型</option>
													@foreach (\App\PackageType::all() as $vo)
														<option value="{{ $vo->name }}">{{ $vo->name }}</option>
													@endforeach
												</select>
											</div>
											<div class='col-md-4' style='margin-left: -15px;width: 39%;'>
												<select id="addPackageModal_packages" class='form-control col-md-12 full-width-fix' name='pkgid' style='width: 10%;' required>
													<option value='' checked>请选择软件版本</option>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												运行配置
											</strong>
										</td>
										<td>
											<textarea rows='100' name='config' class='form-control' style='height: 160px;' required></textarea>
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
							修改应用软件配置
						</h4>
					</div>
					<div class="modal-body">
						<form class="form-horizontal row-border" action="{{ url('devices/editPackage') }}" method="post">
							{{ csrf_field() }}
							<table class="table table-hover table-striped table-bordered table-highlight-head">
								<tbody>
									<tr>
										<td class='col-md-3'>
											<strong>
												设备号
											</strong>
										</td>
										<td>
											<input id="editPackageModal_device_id" class='form-control' type='text' readonly>
											<input id="editPackageModal_devtype" class='form-control' type='hidden'>
											<input id="editPackageModal_devpkg_id" class='form-control' type='hidden' name="devpkg_id">
											<input id="showPackageModal_devpkg" type="hidden" name="devpkg" value="">
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												软件版本
											</strong>
										</td>
										<td>
											<div class='col-md-4' style='margin-left: -15px;'>
												<input id="editPackageModal_pkgname" class='form-control' type='text' readonly>
											</div>
											<div class='col-md-4' style='margin-left: -15px;'>
												<select id="editPackageModal_pkgtype" class='form-control col-md-12 full-width-fix' name='pkgtype' style='width: 10%;' onchange="editPackageModal_updatePackages()" required>
													@foreach (\App\PackageType::all() as $vo)
														<option value="{{ $vo->name }}">{{ $vo->name }}</option>
													@endforeach
												</select>
											</div>
											<div class='col-md-4' style='margin-left: -15px;width: 39%;'>
												<select id="editPackageModal_packages" class='form-control col-md-12 full-width-fix' name='pkgid' style='width: 10%;' required>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												运行配置
											</strong>
										</td>
										<td>
											<textarea id="editPackageModal_config" rows='100' name='config' class='form-control' style='height: 160px;' required></textarea>
										</td>
									</tr>
								</tbody>
							</table>
							<div class="modal-footer table-bordered">
								@can('device_config')
									<a href="javascript:;" onclick="deletePackage();" class="btn btn-danger">
										删除应用软件
									</a>
								@endcan
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

	@can('device_config')
		<div class="modal fade" id="addDeviceExec">
			<div class="modal-dialog" style="width: 1000px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title">
							设备脚本修改
						</h4>
					</div>
					<div class="modal-body">
						<form class="form-horizontal row-border" action="{{ url('devices/addDeviceExec') }}" method="post">
							{{ csrf_field() }}
							<table class="table table-hover table-striped table-bordered table-highlight-head">
								<tbody>
									<tr>
										<td class='col-md-3'>
											<strong>
												设备号
											</strong>
										</td>
										<td>
											<input id="addDeviceExec_device_id" class='form-control' type='text' name="device_id" readonly>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												运行脚本
											</strong>
										</td>
										<td>
											<div>
												<select class='form-control col-md-12 full-width-fix' name='exec' id="exec" required>
													
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												运行状态
											</strong>
										</td>
										<td>
											<label class='radio-inline'>
												<input type="radio" id="addDeviceExec_dosabled_0" name='disabled' value='0'>
												否
											</label>
											<label class='radio-inline'>
												<input type="radio" id="addDeviceExec_dosabled_1" name='disabled' value='1'>
												是
											</label>
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
	@endcan

	@can('launcher_upgrade')
		<div class="modal fade" id="batchEditLauncherModal">
			<div class="modal-dialog" style="width: 1000px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title">
							批量设置启动程序
						</h4>
					</div>
					<div class="modal-body">
						<form class="form-horizontal row-border" action="{{ url('devices/batchSetLauncher') }}" method="post">
							{{ csrf_field() }}
							<input id="batchEditLauncherModal_devices" type="hidden" name="devices">
							<table class="table table-hover table-striped table-bordered table-highlight-head">
								<tbody>
									<tr>
										<td class="col-md-3">
											<strong>
												版本
											</strong>
										</td>
										<td>
											<select id="batchEditLauncherModal_launchers" class="form-control" name="launcher_id" required>
											</select>
										</td>
									</tr>
								</tbody>
							</table>
							<div class="modal-footer table-bordered">
								<button type="button" class="btn btn-default" data-dismiss="modal">
									取消
								</button>
								<button class="btn btn-primary">
									确定
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	@endcan

	@can('package_upgrade')
		<div class="modal fade" id="batchEditPackageModal">
			<div class="modal-dialog" style="width: 1000px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title">
							批量设置应用软件
						</h4>
					</div>
					<div class="modal-body">
						<form class="form-horizontal row-border" action="{{ url('devices/batchSetPackage') }}" method="post">
							{{ csrf_field() }}
							<input id="batchEditPackageModal_devices" type="hidden" name="devices">
							<input id="batchEditPackageModal_devtype" type="hidden">

							<table class="table table-bordered">
								<tbody>
									<tr>
										<td class="col-md-3">
											<strong>
												软件配置
											</strong>
										</td>
										<td>
											<div class="col-md-4" style="margin-left: -15px;">
												<select class="form-control col-md-12 full-width-fix" name="pkgname" style="width: 10%;" id="batchEditPackageModal_pkgname" onchange="batchEditPackageModal_updatePackages()" required>
													<option value="">请选择软件名称</option>
													@foreach(App\PackageName::all() as $vo)
														<option value="{{ $vo->name }}">
															{{ $vo->name }}
														</option>
													@endforeach
												</select>
											</div>
											<div class="col-md-4" style="margin-left: -15px;">
												<select class="form-control col-md-12 full-width-fix" name="pkgtype" style="width: 10%;" id="batchEditPackageModal_pkgtype" onchange="batchEditPackageModal_updatePackages()" required>
													<option value="">请选择软件类型</option>
													@foreach(App\PackageType::all() as $vo)
														<option value="{{ $vo->name }}">
															{{ $vo->name }}
														</option>
													@endforeach
												</select>
											</div>
											<div class="col-md-4" style="margin-left: -15px;width: 39%;">
												<select class="form-control col-md-12 full-width-fix" name="pkgid" style="width: 10%;" id="batchEditPackageModal_pkgid" required>
													<option value="">请选择软件版本</option>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<strong>
												运行配置
											</strong>
											<input type="checkbox" style="float: right;" name="config_set">
										</td>
										<td>
											<textarea id="batchEditPackageModal_config" rows="100" name="config" class="form-control" style="height: 160px;"></textarea>
										</td>
									</tr>
								</tbody>
							</table>
							<div class="modal-footer table-bordered">
								<button type="button" class="btn btn-default" data-dismiss="modal">
									取消
								</button>
								<button class="btn btn-primary">
									确定
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	@endcan

	@include('device.deviceCpuAlarm', ['deviceCpuAlarm' => $deviceCpuAlarm])

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

		function showDevice(device_id)
		{
			$.getJSON('{{ url('devices/getDeviceInfo') }}', {device_id: device_id}, function(data){
				if(data)
				{
					$('#showDeviceModal_device_id').html(data.device_id);
					if (data.disabled)
						$('#showDeviceModal_disabled').html('<span class="label label-warning">是</span>');
					else
						$('#showDeviceModal_disabled').html('否');
					$('#showDeviceModal_devtype').html(data.device_type);
					$('#showDeviceModal_devinfo').html(data.device_info);
					$('#showDeviceModal_ip').html(data.ip_address);
					if (data.launcher_id == 0)
						$('#showDeviceModal_launcher').html('自动');
					else if (data.launcher.length == 0)
						$('#showDeviceModal_launcher').html('<span class="label label-warning">错误</span>');
					else
						$('#showDeviceModal_launcher').html(data.launcher);
					if (data.launcher_immune)
						$('#showDeviceModal_immune').html('是');
					else
						$('#showDeviceModal_immune').html('否');
					if (data.dlserver_id == 0)
						$('#showDeviceModal_dlserver').html('自动');
					else if (data.dlserver.length == 0)
						$('#showDeviceModal_dlserver').html('<span class="label label-warning">错误</span>');
					else
						$('#showDeviceModal_dlserver').html(data.dlserver);
					$('#showDeviceModal_createTime').html(data.create_time);
					$('#showDeviceModal_configTime').html(data.config_time);
					$('#showDeviceModal_expireTime').html(data.expire_time);
					$('#showDeviceModal_updateTime').html(data.update_time);
					if (data.status_code >= 500)
						$('#showDeviceModal_statusCode').html('<span class="label label-danger">'+data.status_code+'</span');
					else if (data.status_code >= 400)
						$('#showDeviceModal_statusCode').html('<span class="label label-warning">'+data.status_code+'</span');
					else
						$('#showDeviceModal_statusCode').html(data.status_code);
					$('#showDeviceModal_retryAfter').html(data.retry_after);
					$('#showDeviceModal_tags').html(data.tags.join(', '));
					if(data.vtun_disabled == 1){
						$('#showDeviceModal_vtund_id').html(data.vtund_id+' <span class="label label-success">开启</span>');
					}else{
						$('#showDeviceModal_vtund_id').html(data.vtund_id+' <span class="label label-danger">关闭</span>');
					}
					var packages = '';
					for (var i=0; i<data.packages.length; i++)
					{
						packages += "<div>"+data.packages[i].package+"</div>";
						packages += "<div><pre style='font-family: sans-serif; border: none; background: none'>"+data.packages[i].config+"</pre></div>";
					}
					$('#showDeviceModal_packages').html(packages);
					
					if(data.uptimeInfo != ''){
						$('#showDeviceModal_uptime').html(SecondToDate(data.uptimeInfo.uptime));
						$('#showDeviceModal_cpuinfo').html(data.uptimeInfo.cpu + '%');
						$('#showDeviceModal_memTotal').html(data.uptimeInfo.mem_total);
						$('#showDeviceModal_memFree').html(data.uptimeInfo.mem_free);
					}else{
						$('#showDeviceModal_uptime').html('没有数据');
						$('#showDeviceModal_cpuinfo').html('没有数据');
						$('#showDeviceModal_memTotal').html('没有数据');
						$('#showDeviceModal_memFree').html('没有数据');
					}
					$('#showDeviceModal_memTotal').html(data.uptimeInfo.mem_total);
					$('#showDeviceModal_memFree').html(data.uptimeInfo.mem_free);

					$('#showDeviceModal').modal();
				}
			});
		}

		@can('device_config')
			function editDevice(device_id, devtype)
			{
				if (devtype.length > 0)
				{
					$.getJSON('{{ url('launcher/getLauncherList') }}', {devtype: devtype}, function(data) {
						var launchers = '<option value="0">自动</option>';
						if(data)
						{
							for (var i=0; i<data.length; i++)
								launchers += "<option value="+data[i].id+">"+data[i].filename+"</option>";
						}
						$('#editDeviceModal_launchers').html(launchers);
					});
				}
				else
				{
					var launchers = '<option value="0">自动</option>';
					$('#editDeviceModal_launchers').html(launchers);
				}

				$.getJSON('{{ url('devices/getDeviceInfo') }}', {device_id: device_id}, function(data){
					if(data){
						$('#editDeviceModal_device_id').val(data.device_id);
						$('#editDeviceModal_devtype').val(data.device_type);
						if(data.disabled){
							$('#editDeviceModal_disabled_1').attr('checked', 'checked');
						}else{
							$('#editDeviceModal_disabled_0').attr('checked', 'checked');
						}
						$('#editDeviceModal_launchers').val(data.launcher_id);
						if(data.launcher_immune){
							$('#editDeviceModal_immune_1').attr('checked', 'checked');
						}else{
							$('#editDeviceModal_immune_0').attr('checked', 'checked');
						}
						$('#editDeviceModal_dlservers').val(data.dlserver_id);
						$('#editDeviceModal_expire_time').val(data.expire_time);
						$('#editDeviceModal_tags').val(data.tags.join(', '));
						if(data.vtun_disabled){
							$('#editDeviceModal_vtun_disabled_1').attr('checked', 'checked');
						}else{
							$('#editDeviceModal_vtun_disabled_0').attr('checked', 'checked');
						}
						$('#editDeviceModal').modal();
					}
				});
			}

			// 设备删除
			function deleteDevice(){
				var device_id = $('#showDeviceModal_device_id').html();
				if(confirm('确定删除设备'+device_id+'?') == false){
					return false;
				}
				
				window.location.href = '{{ url('devices/deleteDevice?device_id=') }}'+device_id+'';
			}
		@endcan

		@can('device_config')
			function addPackage(device_id, devtype)
			{
				$('#addPackageModal_device_id').val(device_id);
				$('#addPackageModal_devtype').val(devtype);
				$('#addPackageModal').modal();
			}

			function addPackageModal_updatePackages()
			{
				var devtype = $('#addPackageModal_devtype').val();
				var pkgname = $('#addPackageModal_pkgname').val();
				var pkgtype = $('#addPackageModal_pkgtype').val();

				if (pkgname == '' || pkgtype == '')
				{
					$('#addPackageModal_packages').html('<option>请选择软件版本</option>');
				}
				else if (devtype == '')
				{
					$('#addPackageModal_packages').html('<option value="0">自动</option>');
				}
				else
				{
					$.getJSON('{{ url('package/getPackageList') }}', {pkgname: pkgname, pkgtype: pkgtype, devtype: devtype}, function(data) {
						var options = '<option value="0">自动</option>';
						if (data.length > 0)
						{
							for (var i=0; i<data.length; i++)
								options+="<option value="+data[i].id+">"+data[i].filename+"</option>";
						}
						$('#addPackageModal_packages').html(options);
					});
				}
			}

			function editPackage(device_id, devtype, devpkg_id, pkgname, pkgtype, package, devpkg)
			{
				$('#editPackageModal_device_id').val(device_id);
				$('#editPackageModal_devtype').val(devtype);
				$('#editPackageModal_devpkg_id').val(devpkg_id);
				$('#editPackageModal_pkgname').val(pkgname);
				$('#editPackageModal_pkgtype').val(pkgtype);
				$('#showPackageModal_devpkg').val(devpkg);

				$.getJSON('{{ url('devices/getDevicePackageInfo') }}', {devpkg_id: devpkg_id}, function(data) {
					if (data)
					{
						$('#editPackageModal_config').val(data.config);
					}
				});
				$.getJSON('{{ url('package/getPackageList') }}', {pkgname: pkgname, pkgtype: pkgtype, devtype: devtype, package: package}, function(data) {
					var options = '<option value="0">自动</option>';
					if (data.length > 0)
					{
						for (var i=0; i<data.length; i++)
							if(data[i].filename == package){
								options += "<option value="+data[i].id+" selected='selected'>"+data[i].filename+"</option>";
							}else{
								options += "<option value="+data[i].id+">"+data[i].filename+"</option>";
							}
					}
					$('#editPackageModal_packages').html(options);
				});
				$('#editPackageModal_pacakges').val(devpkg_id);
				$('#editPackageModal').modal();
			}

			function editPackageModal_updatePackages()
			{
				var devtype = $('#editPackageModal_devtype').val();
				var pkgname = $('#editPackageModal_pkgname').val();
				var pkgtype = $('#editPackageModal_pkgtype').val();

				if (devtype == '')
				{
					$('#editPackageModal_packages').html('<option value="0">自动</option>');
				}
				else
				{
					$.getJSON('{{ url('package/getPackageList') }}', {pkgname: pkgname, pkgtype: pkgtype, devtype: devtype}, function(data) {
						var options = '<option value="0">自动</option>';
						if (data.length > 0)
						{
							for (var i=0; i<data.length; i++)
								options+="<option value="+data[i].id+">"+data[i].filename+"</option>";
						}
						$('#editPackageModal_packages').html(options);
					});
				}
			}

			// 删除应用软件
			function deletePackage(){
				var id = $('#showPackageModal_devpkg').val();
				var pkgname = $('#editPackageModal_pkgname').val();
				if(confirm('确定停止运行'+pkgname+'?') == false) return false;
				window.location.href = '{{ url('devices/deletePackage?id=') }}'+id+'';
			}
		@endcan

		@can('launcher_upgrade')
			function batchSetLauncher()
			{
				var deviceIdList = $("input[name='device_id']");
				var deviceTypeList = $("input[name='devtype']");

				var devices = "";
				var devtype = "";
				var singleTypeDevices = true;

				for(var i=0; i<deviceIdList.length; i++)
				{
					if(deviceIdList[i].checked == true)
					{
						if (devices != "")
							devices = devices + ",";
						devices = devices + deviceIdList[i].value;  

						if (deviceTypeList[i].value == "")
							singleTypeDevices = false;
						else if (devtype == "")
							devtype = deviceTypeList[i].value;
						else if (devtype != deviceTypeList[i].value)
							singleTypeDevices = false;
					}
				}
				if (devices == "")
				{
					alert ('请选择要修改的设备');
					return;
				}

				if (singleTypeDevices == false)
				{
					$('#batchEditLauncherModal_launchers').html('<option value="0">自动</option>');
				}
				else
				{
					$.getJSON('{{ url('launcher/getLauncherList') }}', {devtype: devtype}, function(data) {
						var options = '';
						options += '<option value="0">自动</option>';
						for(var i=0; i<data.length; i++)
						{
							if (data[i].test == 0)
								options+="<option value="+data[i].id+">"+data[i].filename+"</option>";
						}
						$('#batchEditLauncherModal_launchers').html(options);
					});
				}

				$('#batchEditLauncherModal_devices').val(devices);
				$('#batchEditLauncherModal').modal();
			}
		@endcan

		@can('package_upgrade')
			function batchSetPackage()
			{
				var deviceIdList = $("input[name='device_id']");
				var deviceTypeList = $("input[name='devtype']");

				var devices = "";
				var devtype = "";
				var singleTypeDevices = true;

				for(var i=0; i<deviceIdList.length; i++)
				{
					if(deviceIdList[i].checked == true)
					{
						if (devices != "")
							devices = devices + ",";
						devices = devices + deviceIdList[i].value;  

						if (deviceTypeList[i].value == "")
							singleTypeDevices = false;
						else if (devtype == "")
							devtype = deviceTypeList[i].value;
						else if (devtype != deviceTypeList[i].value)
							singleTypeDevices = false;
					}
				}
				if (devices == "")
				{
					alert ('请选择要修改的设备');
					return;
				}

				if (singleTypeDevices == false)
					devtype = '';

				$('#batchEditPackageModal_devices').val(devices);
				$('#batchEditPackageModal_devtype').val(devtype);
				$('#batchEditPackageModal').modal();
			}

			function batchEditPackageModal_updatePackages()
			{
				pkgname = $('#batchEditPackageModal_pkgname').val();
				pkgtype = $('#batchEditPackageModal_pkgtype').val();
				devtype = $('#batchEditPackageModal_devtype').val();

				if (pkgname == '' || pkgtype == '')
				{
					$('#batchEditPackageModal_pkgid').html('<option value="">请选择软件版本</option>');
				}
				else if (devtype == '')
				{
					$('#batchEditPackageModal_pkgid').html('<option value="0">自动</option>');
				}
				else
				{
					$.getJSON('{{ url('package/getPackageList') }}', {pkgname: pkgname, pkgtype: pkgtype, devtype: devtype}, function(data) {
						var options = '<option value="0">自动</option>';
						if (data.length > 0)
						{
							for (var i=0; i<data.length; i++)
							{
								if (data[i].test == 0)
									options+="<option value="+data[i].id+">"+data[i].filename+"</option>";
							}
						}
						$('#batchEditPackageModal_pkgid').html(options);
					});
				}
			}
		@endcan

		@can('device_config')
			function deviceExecAdd()
			{
				var deviceIdList = $("input[name='device_id']");

				var devices = "";

				for(var i=0; i<deviceIdList.length; i++)
				{
					if(deviceIdList[i].checked == true)
					{
						if (devices != "")
							devices = devices + ",";
						devices = devices + deviceIdList[i].value;  
					}
				}
				if (devices == "")
				{
					alert ('请选择要修改的设备');
					return;
				}

				$.getJSON('{{ url('devices/addDeviceExec') }}', {device_id: devices}, function(data) {
					if(data){
						$('#exec').html(data.exec);
						if(data.deviceExec == ''){
							$('#addDeviceExec_dosabled_0').attr('checked', 'checked');
						} else {
							if(data.deviceExec[0].status == 1){
								$('#addDeviceExec_dosabled_1').attr('checked', 'checked');
							}else{
								$('#addDeviceExec_dosabled_0').attr('checked', 'checked');
							}
						}
						
						$('#addDeviceExec_device_id').val(devices);
						$('#addDeviceExec').modal();
					}
				});
			}
		@endcan

		// 秒转换天小时分钟秒
		function SecondToDate(seconds) {
            var time = seconds;
            if (null != time && "" != time) {
                if (time > 60 && time < 60 * 60) {
                    time = parseInt(time / 60.0) + "分钟 " + parseInt((parseFloat(time / 60.0) -
                        parseInt(time / 60.0)) * 60) + "秒 ";
                }
                else if (time >= 60 * 60 && time < 60 * 60 * 24) {
                    time = parseInt(time / 3600.0) + "小时 " + parseInt((parseFloat(time / 3600.0) -
                        parseInt(time / 3600.0)) * 60) + "分钟 " +
                        parseInt((parseFloat((parseFloat(time / 3600.0) - parseInt(time / 3600.0)) * 60) -
                        parseInt((parseFloat(time / 3600.0) - parseInt(time / 3600.0)) * 60)) * 60) + "秒 ";
                } else if (time >= 60 * 60 * 24) {
                    time = parseInt(time / 3600.0/24) + "天 " +parseInt((parseFloat(time / 3600.0/24)-
                        parseInt(time / 3600.0/24))*24) + "小时 " + parseInt((parseFloat(time / 3600.0) -
                        parseInt(time / 3600.0)) * 60) + "分钟 " +
                        parseInt((parseFloat((parseFloat(time / 3600.0) - parseInt(time / 3600.0)) * 60) -
                        parseInt((parseFloat(time / 3600.0) - parseInt(time / 3600.0)) * 60)) * 60) + "秒 ";
                }
                else {
                    time = parseInt(time) + "秒 ";
                }
            }
            return time;
        }

        // 设备批量删除
        function batchDeleteDevice(){
        	var deviceIdList = $("input[name='device_id']");

			var devices = "";

			for(var i=0; i<deviceIdList.length; i++)
			{
				if(deviceIdList[i].checked == true)
				{
					if (devices != "")
						devices = devices + ",";
					devices = devices + deviceIdList[i].value;  
				}
			}
			if (devices == "")
			{
				alert ('请选择要修改的设备');
				return;
			}
			if(confirm('确定删除?') == false) return false;

	        window.location.href = '{{ url('devices/batchDeleteDevice?device_id=') }}'+devices+'';
        }
	</script>
@stop
