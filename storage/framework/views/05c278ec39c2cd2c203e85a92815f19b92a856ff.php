<!-- 成功提示框 -->
<?php if(Session::has('success')): ?>
	<div class="alert alert-success fade in">
		<i class="icon-remove close" data-dismiss="alert">
		</i>
		<strong>
			<?php echo e(Session::get('success')); ?>

		</strong>
	</div>
<?php endif; ?>

<!-- 失败提示框 -->
<?php if(Session::has('error')): ?>
	<div class="alert alert-danger fade in">
		<i class="icon-remove close" data-dismiss="alert">
		</i>
		<strong>
			<?php echo e(Session::get('error')); ?>

		</strong>
	</div>
<?php endif; ?>

<!-- 警告提示框 -->
<?php if(Session::has('warning')): ?>
	<div class="alert alert-warning fade in">
		<i class="icon-remove close" data-dismiss="alert">
		</i>
		<strong>
		    警告!
		</strong>
		<?php echo e(Session::get('warning')); ?>

	</div>
<?php endif; ?>
