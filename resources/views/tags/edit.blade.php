<div class="modal fade" id="TagEditModal">
	<div class="modal-dialog" style="width: 1000px;">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title">
					标签设置
				</h4>
			</div>

			<div class="modal-body">
				<form class="form-horizontal row-border" action="{{ url('devices/tags/edit') }}" method="post">

					{{ csrf_field() }}

					<input id="TagEditModal_tagid" class="form-control" name="tagid" type="hidden">

					<table class="table table-hover table-striped table-bordered table-highlight-head">
						<tr>
							<td class="col-md-2">
								<strong>标签</strong>
							</td>
							<td>
								<input id="TagEditModal_tagname" class="form-control" name="tagname" type="text" required autofocus>
							</td>
						</tr>
						<tr>
							<td class="col-md-2">
								<strong>邮件通知</strong>
							</td>
							<td>
								<input id="TagEditModal_dist" class="form-control" name="dist" type="text">
							</td>
						</tr>
						<tr>
							<td>
								<strong>显示状态</strong>
							</td>
							<td>
								<label class="radio-inline">
									<input id="TagEditModal_status_0" name="status" value="0" type="radio">
									否
								</label>
								<label class="radio-inline">
									<input id="TagEditModal_status_1" name="status" value="1" type="radio">
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

