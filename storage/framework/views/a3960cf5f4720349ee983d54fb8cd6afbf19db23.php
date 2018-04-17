<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
        <title>exands 设备配置平台 <?php echo $__env->yieldContent('title'); ?></title>
        <link href="<?php echo e(asset('bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet" type="text/css"/>
        <!--[if lt IE 9]>
            <link rel="stylesheet" type="text/css" href="<?php echo e(asset('plugins/jquery-ui/jquery.ui.1.10.2.ie.css')); ?>"/>
        <![endif]-->
        <link href="<?php echo e(asset('static/css/main.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('static/css/plugins.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('static/css/responsive.css')); ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo e(asset('static/css/icons.css')); ?>" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="<?php echo e(asset('static/css/fontawesome/font-awesome.min.css')); ?>">
        <link href="<?php echo e(asset('static/css/simditor.css')); ?>" rel="stylesheet" type="text/css"/>
        <!--[if IE 7]>
            <link rel="stylesheet" href="<?php echo e(asset('static/css/fontawesome/font-awesome-ie7.min.css')); ?>">
        <![endif]-->
        <!--[if IE 8]>
            <link href="<?php echo e(asset('static/css/ie8.css')); ?>" rel="stylesheet" type="text/css" />
        <![endif]-->
        <?php echo $__env->yieldContent('style'); ?>
    </head>
    
    <body>
        <?php $__env->startSection('header'); ?>
            <header class="header navbar navbar-fixed-top" role="banner">
                <div class="container">
                    <ul class="nav navbar-nav">
                        <li class="nav-toggle">
                            <a href="javascript:void(0);" title="">
                                <i class="icon-reorder">
                                </i>
                            </a>
                        </li>
                    </ul>
                    <a class="navbar-brand" href="<?php echo e(url('/home')); ?>">
                        exands 设备配置平台
                    </a>
                    <a href="#" class="toggle-sidebar bs-tooltip" data-placement="bottom" data-original-title="Toggle navigation">
                        <i class="icon-reorder">
                        </i>
                    </a>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown user">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-male">
                                </i>
                                <span class="username">
                                    <?php echo e(Auth::user()->name); ?>

                                </span>
                                <i class="icon-caret-down small">
                                </i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a data-toggle="modal" href="#resetPasswordModal">
                                        <i class="icon-user">
                                        </i>
                                        修改密码
                                    </a>
                                </li>
                                <li class="divider">
                                </li>
                                <li>
                                    <a href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                        <i class="icon-key">
                                        </i>
                                        退出
                                    </a>
                                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                        <?php echo e(csrf_field()); ?>

                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </header>
        <?php echo $__env->yieldSection(); ?>

        <div id="container">
            <div id="sidebar" class="sidebar-fixed">
                <div id="sidebar-content">

                    <!-- 左侧导航栏 -->
                    <?php echo $__env->make('common.leftNav', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                </div>
                <div id="divider" class="resizeable">
                </div>
            </div>

            <div id="content">
                <div class="container">
					<div style='height: 12px'></div>
                    <?php $__env->startSection('content'); ?>
                    <?php echo $__env->yieldSection(); ?>
                </div>
            </div>
        </div>
        
        <?php echo $__env->make('common.commonJs', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

		<?php echo $__env->make('user.resetPassword', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        
        <?php $__env->startSection('javascript'); ?>

        <?php echo $__env->yieldSection(); ?>    

    </body>
</html>
