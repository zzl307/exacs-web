@extends('common.layouts')

@section('menu')
	标签管理
@stop

@section('content')

	<div class="row">
		<div class="col-md-12">

			<div class="widget box">
				<div class="widget-header">
					<h4>
						<i class="icon-reorder">
						</i>
						标签管理
					</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-refresh">
								<i class="icon-refresh">
								</i>
								刷新
							</span>
							<a href="javascript:;" onclick="AddTag();" class="btn btn-xs">
								<i class="icon-plus">
								</i>
								新增
							</a>
						</div>
					</div>
				</div>

				@include('common.message')

				<div class="widget-content no-padding">
					<table class="table table-hover table-striped table-bordered table-highlight-head">
						<thead>
							<tr>
								<th>
									标签
								</th>
								<th>
									邮件通知
								</th>
								<th>
									显示状态
								</th>
								<th>
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach(\App\DeviceTagAttrs::all() as $vo)
								<tr>
									<td>
									   {{$vo->name}}
									</td>
									<td>
										{{$vo->dist}}
									</td>
									<td>
										{{$vo->status == 0 ? '否':'是'}}
									</td>
									<td>
										<a href="javascript:;" onclick="EditTag('{{ $vo->id }}');">
											<button class="btn btn-xs">
												<i class="icon-edit">
												</i>
											</button>
										</a>
										<a href="{{ url('devices/tags/delete?tagid='.$vo->id) }}" onclick="if(confirm('确定删除?') == false) return false;">
											<button class="btn btn-xs">
												<i class="icon-trash">
												</i>
											</button>
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

	@include('tags.add')
	@include('tags.edit')
@stop

@section('javascript')
    <script type="text/javascript">
		function AddTag()
		{
			$('#TagAddModal').modal('show');
		}

		function EditTag(tagid)
		{
			$.getJSON('{{ url('devices/tags/detail') }}', {tagid: tagid}, function(data) {
				if (data != null)
				{
					$('#TagEditModal_tagid').val(data.id);
					$('#TagEditModal_tagname').val(data.name);
					$('#TagEditModal_dist').val(data.dist);
					if (data.status == 0)
						$('#TagEditModal_status_0').attr('checked', 'checked');
					else
						$('#TagEditModal_status_1').attr('checked', 'checked');
					$('#TagEditModal').modal('show');
				}
				else
				{
					alert('没有获取到标签'+tagname+'的信息！');
				}
			}).error(function () {
					alert('获取标签'+tagname+'的信息出错！');
			});
		}
    </script>
@stop
