<?php $__env->startSection('menu'); ?>
	应用软件
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
	<div class="row">
		<div class="col-md-12">

			<?php echo $__env->make('common.message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

			<div class="widget box">
				<div class="widget-header">
					<h4>
						<i class="icon-reorder">
						</i>
						应用程序列表
					</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<a class="btn btn-xs" href="<?php echo e(url('package/file/list')); ?>">
								<i class="icon-list">
								</i>
								所有文件
							</a>
							<span class="btn btn-xs widget-refresh">
								<i class="icon-refresh">
								</i>
								刷新
							</span>
							<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('package_release')): ?>
								<a class="btn btn-xs" href="#addPackageModal" data-toggle="modal">
									<i class="icon-plus">
									</i>
									新增
								</a>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<div class="widget-content no-padding">
					<table class="table table-hover table-striped table-bordered table-highlight-head">
						<thead>
							<tr>
								<th>
									软件名称
								</th>
								<th>
									描述
								</th>
								<th>
									邮件通知列表
								</th>
								<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('package_release')): ?>
									<th>
									</th>
								<?php endif; ?>
							</tr>
						</thead>
						<tbody>
							<?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td>
										<a href="<?php echo e(url('package?name=' . $vo->name)); ?>">
											<?php echo e($vo->name); ?>

										</a>
									</td>
									<td>
										<?php echo e($vo->note); ?>

									</td>
									<td>
										<?php echo e($vo->dist); ?>

									</td>
									<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('package_release')): ?>
										<td>
											<a href="javascript:;" onclick="update('<?php echo e($vo->name); ?>')">
												<i class="icon-edit">
												</i>
											</a>
											&nbsp;
											<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('package_delete')): ?>
												<a href="<?php echo e(url('package/delete?name='.$vo->name)); ?>" title="删除" onclick="if(confirm('确定删除<?php echo e($vo->name); ?>?') == false) return false;">
													<i class="icon-trash">
													</i>
												</a>
											<?php endif; ?>
										</td>
									<?php endif; ?>
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('package_release')): ?>
	<div class="modal fade" id="addPackageModal">
		<div class="modal-dialog" style="width: 1000px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						增加新应用程序
					</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal row-border" action="<?php echo e(url('package/add')); ?>" method="post">
						<?php echo e(csrf_field()); ?>

						<table class="table table-hover table-striped table-bordered table-highlight-head">
							<tbody>
								<tr>
									<td class="col-md-3">
										<strong>
											软件名称
										</strong>
									</td>
									<td>
										<input class="form-control" name="Data[name]" type="text" required autofocus>
									</td>
								</tr>
								<tr>
									<td class="col-md-3">
										<strong>
											软件描述
										</strong>
									</td>
									<td>
										<input class="form-control" name="Data[note]" type="text" required>
									</td>
								</tr>
								<tr>
									<td>
										<strong>
											邮件通知列表
										</strong>
									</td>
									<td>
										<input class="form-control" name="Data[dist]" type="text">
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
						修改应用程序
					</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal row-border" action="<?php echo e(url('package/update')); ?>" method="post">
						<?php echo e(csrf_field()); ?>

						<table class="table table-hover table-striped table-bordered table-highlight-head" style="border: 1px solid #ddd;">
							<tbody>
								<tr>
									<td class="col-md-3">
										<strong>
											软件名称
										</strong>
									</td>
									<td>
										<input id="editPackageModal_name" class="form-control" name="name" type="text" readonly>
									</td>
								</tr>
	
								<tr>
									<td class="col-md-3">
										<strong>
											软件描述
										</strong>
									</td>
									<td>
										<input id="editPackageModal_note" class="form-control" name="note" type="text" required>
									</td>
								</tr>
								<tr>
									<td>
										<strong>
											邮件通知列表
										</strong>
									</td>
									<td>
										<input id="editPackageModal_dist" class="form-control" name="dist" type="text">
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
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('javascript'); ?>
	<script type="text/javascript">
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('launcher_release')): ?>
		function update(name, note, dist)
		{
			$('#editPackageModal_name').val(name);

			$.getJSON('<?php echo e(url('package/info')); ?>', {name: name}, function(data){
				$('#editPackageModal_note').val(data.note);
				$('#editPackageModal_dist').val(data.dist);
			});
			$('#editPackageModal').modal();
		}
<?php endif; ?>
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('common.layouts', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>