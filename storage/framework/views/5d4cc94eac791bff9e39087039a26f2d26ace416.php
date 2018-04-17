<?php $__env->startSection('style'); ?>
	<style>
		.widget-content.no-padding .dataTables_header{
			border-top: 1px solid #ddd;
		}
	</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('menu'); ?>
	设备管理
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4>
						<i class="icon-reorder">
						</i>
						设备在线率状况
					</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-refresh">
								<i class="icon-refresh">
								</i>
								刷新
							</span>
						</div>
					</div>
				</div>

				<div class="widget-content no-padding">
					<table class="table table-hover table-striped table-bordered table-highlight-head">
						<thead>
							<tr>
								<th style="vertical-align: middle; text-align: center;">
									标签分类
								</th>
								<th style="vertical-align: middle; text-align: center;">
									场所总数
								</th>
								<th style="vertical-align: middle; text-align: center;">
									在线数
								</th>
								<th style="vertical-align: middle; text-align: center;">
									在线率
								</th>
							</tr>
						</thead>
						<tbody>
							<?php $__currentLoopData = $devicesByTag; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $vo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td>
										<a href="<?php echo e(url('devices/search?key=').$key); ?>">
											<?php echo e($key); ?>

										</a>
									</td>
									<td>
										<?php echo e($vo['devices']); ?>

									</td>
									<td>
										<?php echo e($vo['online']); ?>

									</td>
									<td>
										<?php if($vo['rate'] < 50): ?>
											<span class="label label-danger"><?php echo e($vo['rate']); ?>%</span>
										<?php elseif($vo['rate'] < 80): ?>
											<span class="label label-warning"><?php echo e($vo['rate']); ?>%</span>
										<?php elseif($vo['rate'] == 100): ?>
											<span class="label label-success"><?php echo e($vo['rate']); ?>%</span>
										<?php else: ?>
											<?php echo e($vo['rate']); ?>%
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<tfoot>
								<tr>
									<th>
										全部
									</th>
									<th>
										<?php echo e($total['devices']); ?>

									</th>
									<th>
										<?php echo e($total['online']); ?>

									</th>
									<th>
										<?php echo e($total['rate']); ?>%
									</th>
							</tfoot>
						</tbody>
					</table>
				</div>		
			</div>		
		</div>
	</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('common.layouts', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>