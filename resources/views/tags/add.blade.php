<div class="modal fade" id="TagAddModal">
	<div class="modal-dialog" style="width: 1000px;">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title">
					增加标签设置
				</h4>
			</div>

			<div class="modal-body">
				<form class="form-horizontal row-border" action="{{ url('devices/tags/add') }}" method="post">

					{{ csrf_field() }}

					<table class="table table-hover table-striped table-bordered table-highlight-head">
						<tr>
							<td class="col-md-2">
								<strong>标签</strong>
							</td>
							<td>
								<input class="form-control" name="tagname" type="text" required autofocus>
							</td>
						</tr>
						<tr>
							<td>
								<strong>邮件通知</strong>
							</td>
							<td>
								<input class="form-control" name="dist" type="text">
							</td>
						</tr>
						<tr>
							<td>
								<strong>显示状态</strong>
							</td>
							<td>
								<label class="radio-inline">
									<input name="status" value="0" type="radio" checked="">
									否
								</label>
								<label class="radio-inline">
									<input name="status" value="1" type="radio">
									是
								</label>
							</td>
						</tr>
					</table>

					<div class="modal-footer">
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
