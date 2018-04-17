@extends('common.layouts')

@section('menu')
	脚本配置
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
						脚本配置
					</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-refresh">
								<i class="icon-refresh">
								</i>
								刷新
							</span>
							<a href="javascript:;" onclick="deviceExecAdd();" class="btn btn-xs">
								<i class="icon-plus">
								</i>
								新增
							</a>
						</div>
					</div>
				</div>

				<div class="widget-content no-padding">
					<table class="table table-hover table-striped table-bordered table-highlight-head">
						<thead>
							<tr>
								<th>
									脚本名称
								</th>
								<th>
									添加时间
								</th>
								<th>
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($deviceExec as $vo)
								<tr>
									<td>
										<a href="{{ url('devices/deviceExecShow', ['id' => $vo->id]) }}" class="bs-tooltip" title="脚本详情">
											{{ $vo->name }}
										</a>
									   
									</td>
									<td>
										{{ $vo->time }}
									</td>
									<td>
										<a href="javascript:;" onclick="deviceExecEdit('{{ $vo->id }}');" class="bs-tooltip" title="脚本修改">
											<i class="icon-edit" style="color: #555;">
											</i>
										</a>
										&nbsp;
										<a href="{{ url('devices/execDelete?id='.$vo->id) }}" class="bs-tooltip" title="脚本删除" onclick="if(confirm('{{"确定删除设备".$vo->name."?"}}') == false) return false;">
											<i class="icon-trash" style="color: #555;">
											</i>
										</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="devcieExecAddModal">
		<div class="modal-dialog" style="width: 1000px;">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						新增设备脚本配置
					</h4>
				</div>

				<div class="modal-body">
					<form class="form-horizontal row-border" action="" method="post">

						{{ csrf_field() }}

						<table class="table table-hover table-striped table-bordered table-highlight-head">
							<tr>
								<td class="col-md-2">
									<strong>
										脚本名称
									</strong>
								</td>
								<td>
									<input type="text" class="form-control" name="name" required autofocus>
								</td>
							</tr>
							<tr>
								<td>
									<strong>
										脚本内容
									</strong>
								</td>
								<td>
									<textarea rows="100" name="content" class="form-control" style="height: 160px;"></textarea>
								</td>
							</tr>
						</table>

						<div class="modal-footer table-bordered">
							<button type="button" class="btn btn-default" data-dismiss="modal">
								取消
							</button>
							<button type="submit" class="btn btn-primary">
								确认
							</button>
						</div>
					</form>
				</div>

			</div>
		</div>
	</div>

	<div class="modal fade" id="deviceExecEditModal">
		<div class="modal-dialog" style="width: 1000px;">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						新增设备脚本配置
					</h4>
				</div>

				<div class="modal-body">
					<form class="form-horizontal row-border" action="{{ url('devices/deviceExecEdit') }}" method="post">

						{{ csrf_field() }}
						
						<input type="hidden" name="id" id="id">
						<table class="table table-hover table-striped table-bordered table-highlight-head">
							<tr>
								<td class="col-md-2">
									<strong>
										脚本名称
									</strong>
								</td>
								<td>
									<input type="text" class="form-control" name="name" id="exec_name" value="" required autofocus>
								</td>
							</tr>
							<tr>
								<td>
									<strong>
										脚本内容
									</strong>
								</td>
								<td>
									<textarea rows="100" name="content" id="exec_content" value="" class="form-control" style="height: 160px;"></textarea>
								</td>
							</tr>
						</table>

						<div class="modal-footer table-bordered">
							<button type="button" class="btn btn-default" data-dismiss="modal">
								取消
							</button>
							<button type="submit" class="btn btn-primary">
								确认
							</button>
						</div>
					</form>
				</div>

			</div>
		</div>
	</div>
@stop

@section('javascript')
    <script type="text/javascript">
		function deviceExecAdd(){
			$('#devcieExecAddModal').modal();
		}

		function deviceExecEdit(id){
			$('#id').val(id);
			$.getJSON('{{ url('devices/deviceExecEdit') }}', {id: id}, function(data){
				$('#exec_name').val(data.name);
				$('#exec_content').val(data.content);

				$('#deviceExecEditModal').modal();
			});
		}
    </script>
@stop
