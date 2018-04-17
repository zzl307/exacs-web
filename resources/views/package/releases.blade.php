@extends('common.layouts')

@section('menu')
	应用软件
@stop

@section('content')
	<div class="row">
		<div class="col-md-12">

			@include('common.message')

			<div class="widget box">

			   	@include('package.select')

				<div class="widget-header" style="border-top: 1px solid #ddd;">
					<h4>
						<i class="icon-reorder">
						</i>
						{{ $name }}
					</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<a class="btn btn-xs" href="{{ url('package/file/list?name='.$name) }}">
								<i class="icon-list">
								</i>
								所有文件
							</a>
							<span class="btn btn-xs widget-refresh">
								<i class="icon-refresh">
								</i>
								刷新
							</span>
							@can('package_release')
								<a href="#addReleaseModal" data-toggle="modal">
									<span class="btn btn-xs">
										<i class="icon-plus">
										</i>
										新增
									</span>
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
								@can('package_release')
									<th>
									</th>
								@endcan
							</tr>
						</thead>
						<tbody>
							@foreach($releases as $vo)
								<tr>
									<td>
										<a href="{{ url('package/file/list?name='.$vo->pkgname.'&release='. $vo->id) }}">
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
									@can('package_release')
										<td>
											<a href="javascript:;" onclick="update('{{ $vo->id }}')" title="修改">
												<i class="icon-edit">
												</i>
											</a>
											&nbsp;
											<a href="{{ url('package/release/delete?id='.$vo->id) }}" title="删除" onclick="if(confirm('{{'确定删除?'}}') == false) return false;">
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
			</div>
		</div>
	</div>

@can('package_release')
	<div class="modal fade" id="addReleaseModal">
		<div class="modal-dialog" style="width: 1000px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						{{ $name }}新版本发布
					</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal row-border" action="{{ url('package/release/add?name='.$name) }}" method="post">
						{{ csrf_field() }}
						<table class="table table-hover table-striped table-bordered table-highlight-head">
							<tbody>
								<tr>
									<td class="col-md-3">
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
											<input type="radio" name="test" value="1" checked>
											是
										</label>
										<label class="radio-inline">
											<input type="radio" name="test" value="0">
											否
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

	<div class="modal fade" id="editReleaseModal">
		<div class="modal-dialog" style="width: 1000px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 id="editReleaseModal_title" class="modal-title">
					</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal row-border" action="{{ url('package/release/update') }}" method="post">
						{{ csrf_field() }}
						<input id="editReleaseModal_id" type="hidden" name="id">
						<table class="table table-hover table-striped table-bordered table-highlight-head" style="border: 1px solid #ddd;">
							<tbody>
								<tr>
									<td class="col-md-3">
										<strong>
											发布说明
										</strong>
									</td>
									<td>
										<textarea id="editReleaseModal_note" rows="10" class="form-control" name="note" requried></textarea>
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
   										 <input id="editReleaseModal_test_1" type="radio" name="test" value="1">
   										是
   									 </label>
   									 <label class="radio-inline">
   										 <input id="editReleaseModal_test_0" type="radio" name="test" value="0">
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
		@can('package_release')
			function update(id)
			{
				$('#editReleaseModal_id').val(id);

				$.getJSON('{{ url('package/release/info') }}', {id: id}, function(data){
					$('#editReleaseModal_title').html(data.pkgname+'-'+data.version);
					$('#editReleaseModal_note').val(data.note);
					if (data.test > 0)
						$('#editReleaseModal_test_1').attr('checked', 'checked');
					else
						$('#editReleaseModal_test_0').attr('checked', 'checked');
				});
				$('#editReleaseModal').modal();
			}
		@endcan
	</script>
@stop
