<style>
	#sidebar{
		width: 206px;
	}
	#sidebar ul#nav ul.sub-menu a{
		padding: 12px 15px 12px 46px;
	}
	#content{
        margin-left: 206px;
    }
    .table thead>tr>th, .table tbody>tr>th, .table tfoot>tr>th, .table thead>tr>td, .table tbody>tr>td, .table tfoot>tr>td{
        vertical-align: middle;
    }
</style>
<!-- 左侧导航栏 -->
<ul id="nav">
	@can('system_view')
		<li class="@if(Request::segment(1) == 'config') current @endif">
			<a href="javascript:void(0);">
				<i class="icon-cogs">
				</i>
				系统设置
			</a>
			<ul class="sub-menu">
				<li class="@if(Request::segment(2) == 'dlserver') current @endif">
					<a href="{{ url('config/dlserver') }}">
						<i class="icon-caret-right">
						</i>
						下载服务器
					</a>
				</li>
			</ul>
		</li>
	@endcan
	
	<li class="@if(Request::segment(1) == 'launcher' || Request::segment(1) == 'package' || Request::segment(1) == 'upload') current @endif">
		<a href="javascript:void(0);">
			<i class="icon-desktop">
			</i>
			软件发布 
		</a>
		<ul class="sub-menu">
			<li class="@if(Request::segment(1) == 'launcher') current @endif">
				<a href="{{ url('launcher') }}">
					<i class="icon-caret-right">
					</i>
					启动程序
				</a>
			</li>
			<li class="@if(Request::segment(1) == 'package') current @endif">
				<a href="{{ url('package') }}">
					<i class="icon-caret-right">
					</i>
					应用软件
				</a>
			</li>
			<li class="@if(Route::currentRouteName('upload.index')) current @endif">
				<a href="{{ route('upload.index') }}">
					<i class="icon-caret-right">
					</i>
					文件上传
				</a>
			</li>
		</ul>
	</li>
	
	<li class="@if(Request::segment(1) == 'devices') current @endif">
		<a href="javascript:void(0);">
			<i class="icon-list-alt">
			</i>
			设备管理
		</a>
		<ul class="sub-menu">
			<li class="@if(Request::segment(2) == 'home' || Request::segment(2) == 'search') current @endif">
				<a href="{{ url('devices/home') }}">
					<i class="icon-caret-right">
					</i>
					设备配置
				</a>
			</li>
			<li class="@if(Request::segment(2) == 'logs') current @endif">
				<a href="{{ url('devices/logs') }}">
					<i class="icon-caret-right">
					</i>
					设备日志
				</a>
			</li>
			@can('device_tag_config')
				<li class="@if(Request::segment(2) == 'tags') current @endif">
					<a href="{{ url('devices/tags') }}">
						<i class="icon-caret-right">
						</i>
						标签管理
					</a>
				</li>
			@endcan
			<li class="@if(Request::segment(2) == 'deviceExec' or Request::segment(2) == 'deviceExecShow') current @endif">
				<a href="{{ url('devices/deviceExec') }}">
					<i class="icon-caret-right">
					</i>
					脚本管理
				</a>
			</li>
		</ul>
	</li>

	@can('user_config')
		<li class="{{Request::segment(1) == 'user' ? 'current' : ''}}">
			<a href="javascript:void(0);">
				<i class="icon-group">
				</i>
				用户管理
			</a>
			<ul class="sub-menu">
				<li class="{{Request::segment(2) == 'list' ? 'current' : ''}}">
					<a href="{{ url('user/list') }}">
						<i class="icon-caret-right">
						</i>
						用户列表
					</a>
				</li>
				<li class="{{Request::segment(2) == 'roles' ? 'current' : ''}}">
					<a href="{{url('user/roles')}}">
						<i class="icon-caret-right">
						</i>
						角色设置
					</a>
				</li>
				<li class="{{Request::segment(2) == 'permission' ? 'current' : ''}}">
					<a href="{{url('user/permission')}}">
						<i class="icon-caret-right">
						</i>
						权限设置
					</a>
				</li>
			</ul>
		</li>
	@endcan
</ul>

<!--
<div class="sidebar-title">
	<span>
		报警
	</span>
</div>
<ul class="notifications demo-slide-in">
			<li>
				<div class="col-left">
					<span class="label label-danger" style="width: auto;">
						<i class="icon-warning-sign">
						</i>
					</span>
				</div>
				<div class="col-right with-margin">
					<span class="message">
						42
						<strong>
							#512
						</strong>
						crashed.
					</span>
					<span class="time">
						几分钟之前
					</span>
				</div>
			</li>
</ul>
-->
<!-- 样式 -->
<!--
<div class="sidebar-widget align-center">
	<div class="btn-group" data-toggle="buttons" id="theme-switcher">
		<label class="btn active">
			<input type="radio" name="theme-switcher" data-theme="bright">
			<i class="icon-sun">
			</i>
			白天
		</label>
		<label class="btn">
			<input type="radio" name="theme-switcher" data-theme="dark">
			<i class="icon-moon">
			</i>
			夜晚
		</label>
	</div>
</div>
-->

<script type="text/javascript">
	function police(){
		$.ajax({
			url: '/police',
			type: 'post',
			dataType: 'json',
			data: {'_token':'{{ csrf_token() }}'},
			success: function(data){
				var str = '';  
				for(var i=0;i<data.length;i++){
					console.log(data[i].id);
					str += '<li>'+  
							'<div class="col-left"><span class="label label-danger" style="width: auto;"><i class="icon-warning-sign"></i></span></div>'+  
							'<div class="col-right with-margin"><span class="message">'+data[i].id+'</span></div>'+
							'</li>'  
				}
				$(".demo-slide-in").html(str);
			}
		})
		
		
	}
	// setInterval('police()', 1000);
</script>
