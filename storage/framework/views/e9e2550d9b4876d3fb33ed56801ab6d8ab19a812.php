<?php $__env->startSection('content'); ?>
    <div class="box">
        <div class="content">
            <form class="form-vertical login-form" action="<?php echo e(route('login')); ?>" method="post">
                <?php echo e(csrf_field()); ?>


                <h3 class="form-title">
                    登录
                </h3>
                <div class="alert fade in alert-danger" style="display: none;">
                    <i class="icon-remove close" data-dismiss="alert">
                    </i>
                    输入任何用户名密码进入.
                </div>
                <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                    <div class="input-icon">
                        <i class="icon-user">
                        </i>
                        <input id="email" type="text" name="email" class="form-control" placeholder="用户名" value="<?php echo e(old('email')); ?>" required autofocus>

                        <?php if($errors->has('email')): ?>
                            <span class="help-block">
                                <strong><?php echo e($errors->first('email')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                    <div class="input-icon">
                        <i class="icon-lock">
                        </i>
                        <input id="password" type="password" name="password" class="form-control" placeholder="密码" required>

                        <?php if($errors->has('password')): ?>
                            <span class="help-block">
                                <strong><?php echo e($errors->first('password')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-actions">
                    <label class="checkbox pull-left">
                        <input type="checkbox" class="uniform" name="remember" { old('remember') ? 'checked' : '' }}>
                        记住密码
                    </label>
                    <button type="submit" class="submit btn btn-primary pull-right">
                        登录
                        <i class="icon-angle-right">
                        </i>
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('common.login', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>