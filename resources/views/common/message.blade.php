<!-- 成功提示框 -->
@if(Session::has('success'))
	<div class="alert alert-success fade in">
		<i class="icon-remove close" data-dismiss="alert">
		</i>
		<strong>
			{{Session::get('success')}}
		</strong>
	</div>
@endif

<!-- 失败提示框 -->
@if(Session::has('error'))
	<div class="alert alert-danger fade in">
		<i class="icon-remove close" data-dismiss="alert">
		</i>
		<strong>
			{{Session::get('error')}}
		</strong>
	</div>
@endif

<!-- 警告提示框 -->
@if(Session::has('warning'))
	<div class="alert alert-warning fade in">
		<i class="icon-remove close" data-dismiss="alert">
		</i>
		<strong>
		    警告!
		</strong>
		{{Session::get('warning')}}
	</div>
@endif
