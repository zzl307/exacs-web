@extends('common.layouts')

@section('menu')
	启动程序
@stop

@section('style')
	<style>
		.widget-content.no-padding .dataTables_header{
			border-top: 1px solid #ddd;
		}
		code{
			display: block;
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
						{{ empty($release) ? "文件列表" : 'exa-launcher-'.$release->version }}
					</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-refresh">
								<i class="icon-refresh">
								</i>
								刷新
							</span>
							<a class="btn btn-xs" data-toggle="modal" href="#uploadModal">
								<i class="icon-upload-alt">
								</i>
								上传
							</a>
						</div>
					</div>
				</div>

				@if (empty($files))
					<div class="widget-content no-padding">
						<code>
							暂时没有文件！
						</code>
					</div>
				@else
					<div class="widget-content no-padding">
						<table class="table table-hover table-striped table-bordered table-highlight-head">
							<thead>
								<tr>
									<th>
										文件名
									</th>
									<th>
										校验值
									</th>
									<th>
										更新时间
									</th>
									<th>
										
									</th>
								</tr>
							</thead>
							<tbody>
								@foreach($files as $key => $vo)
									<tr>
										<td class="col-md-3">
											{{ $vo }}
											@if(Storage::disk('upload')->exists($vo))
												<a href="{{ route('upload.download', ['filename' => $vo]) }}" onclick="if(confirm('{{"下载文件 " . $vo . "?"}}') == false) return false;" class="bs-tooltip pull-right" title="下载">
													<i class="icon-download" title="下载">
													</i>
												</a>
											@else
												<span class="label label-danger" style="float: right">文件不存在</span>
											@endif
										</td>
										<td class="col-md-3">
											{{ md5(Storage::disk('upload')->get($vo)) }}
										</td>
										<td class="col-md-3">
											{{ date('Y-m-d H:i:s', Storage::disk('upload')->lastModified($vo)) }}
										</td>
										<td class="col-md-3">
											<a href="{{ route('upload.delete', ['filename' => $vo]) }}" onclick="if(confirm('确定删除?') == false) return false;" class="bs-tooltip" title="删除">
												<i class="icon-trash" style="color: #555;">
												</i>
											</a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>					
				@endif
				
			</div>
			<div class="modal-footer" style="border: 1px solid #e5e5e5;">
				<a href="javascript:history.back(-1)">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						返回
					</button>
				</a>
			</div>
		</div>
	</div>

	<div class="modal fade" id="uploadModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						上传文件
					</h4>
				</div>
				<form class="form-horizontal row-border" action="{{ route('upload.store') }}" method="post" enctype="multipart/form-data">
	
					{{ csrf_field() }}
	
					<div class="modal-body">
						<div class="form-group">
							<label for="file" class="col-md-3 control-label">
								请选择文件
							</label>
							<div class="col-md-9" style="margin-top: 3px;">
								<input id="file" class="form-control" name="source" type="file" required autofocus>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">
							取消
						</button>
						<button type="submit" class="btn btn-primary">
							上传
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@stop
