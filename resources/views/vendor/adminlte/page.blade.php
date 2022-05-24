@extends('adminlte::master')

@section('adminlte_css')
<link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/skins/skin-' . config('adminlte.skin', 'blue') . '.min.css')}}">
    @stack('css')
    @yield('css')
@stop

@section('body_class', 'skin-' . config('adminlte.skin', 'blue') . ' sidebar-mini ' . (config('adminlte.layout') ? [
    'boxed' => 'layout-boxed',
    'fixed' => 'fixed',
    'top-nav' => 'layout-top-nav'
][config('adminlte.layout')] : '') . (config('adminlte.collapse_sidebar') ? ' sidebar-collapse ' : ''))

@section('body')
    <div class="wrapper">
		<?php //$user = config('site.user'); ?>
        <!-- Main Header -->
        <header class="main-header">
            @if(config('adminlte.layout') == 'top-nav')
            <nav class="navbar navbar-static-top">
                <div class="container">
                    <div class="navbar-header">
                        <a href="{{ url(config('adminlte.dashboard_url', 'home')) }}" class="navbar-brand">
                            {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
                        </a>
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                        <ul class="nav navbar-nav">
                            @each('adminlte::partials.menu-item-top-nav', $adminlte->menu(), 'item')
                        </ul>
                    </div>
                    <!-- /.navbar-collapse -->
            @else
            <!-- Logo -->
            <a href="{{ url(config('adminlte.dashboard_url', 'home')) }}" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini">{!! config('adminlte.logo_mini', '<b>A</b>LT') !!}</span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg">{!! config('adminlte.logo', '<b>Admin</b>LTE') !!}</span>
				<!--
				<img class="logo-mini" src="{{ asset('css/images/icon.png') }}" width="30" height="30" alt="{!! config('adminlte.logo_mini', '<b>A</b>LT') !!}">
		        <img class="logo-lg" src="{{ asset('css/images/logo.png') }}" width="200" height="30" alt="{!! config('adminlte.logo', '<b>Admin</b>LTE') !!}">
				-->
            </a>

            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">{{ trans('adminlte::adminlte.toggle_navigation') }}</span>
                </a>
				<div class="navbar-left">
					<?php
					//$sekolah = config('site.sekolah');
					//$semester = config('site.semester');
					?>
					<a href="{{ url(config('adminlte.dashboard_url', 'home')) }}" class="logo" style="width:100%">{{($sekolah) ? $sekolah->nama : ''}} | {{($semester) ? $semester->nama : ''}}</a>
				</div>
            @endif
				
				<!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
						<li class="dropdown user user-menu"><a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<?php $img = ($user->photo!= '')  ? asset('storage/images/'.$user->photo) : asset('vendor/img/avatar3.png'); ?><img src="<?php echo $img;?>" class="user-image" alt="User Image" /><span class="hidden-xs"><?php echo $user->name; ?></span></a>
							<ul class="dropdown-menu">
								<li class="user-header">
									<img src="<?php echo $img;?>" class="user-circle" alt="User Image" />
									<p><?php echo $user->name; ?></p>
								</li>
								<li class="user-footer">
									<div class="pull-left">
										<a href="<?php echo route('user.profile'); ?>" class="btn btn-default btn-flat">Profil</a>
									</div>
									<div class="pull-right">
										@if(config('adminlte.logout_method') == 'GET' || !config('adminlte.logout_method') && version_compare(\Illuminate\Foundation\Application::VERSION, '5.3.0', '<'))
											<a href="{{ url(config('adminlte.logout_url', 'auth/logout')) }}">
												<i class="fa fa-fw fa-power-off"></i> Keluar
											</a>
										@else
											<a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-default btn-flat">
												<i class="fa fa-fw fa-power-off"></i> Keluar
											</a>
											<form id="logout-form" action="{{ url(config('adminlte.logout_url', 'auth/logout')) }}" method="POST" style="display: none;">
												@if(config('adminlte.logout_method'))
													{{ method_field(config('adminlte.logout_method')) }}
												@endif
												{{ csrf_field() }}
											</form>
										@endif
									</div>
								</li>
							</ul>
						</li>
                    </ul>
                </div>
                @if(config('adminlte.layout') == 'top-nav')
                </div>
                @endif
            </nav>
        </header>

        @if(config('adminlte.layout') != 'top-nav')
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">

            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
				<div class="user-panel">
					<div class="pull-left image">
						<img src="<?php echo $img;?>" class="img-circle" alt="User Image" />
					</div>
					<div class="pull-left info">
						<p>Selamat Datang<br /><?php echo $user->name; ?></p>
					</div>
				</div>
                <!-- Sidebar Menu -->
                <ul class="sidebar-menu" data-widget="tree">
                    @each('adminlte::partials.menu-item', $adminlte->menu(), 'item')
					<li class="active">
						<a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-fw fa-power-off text-red"></i> <span>Keluar dari Aplikasi</span></a>
					</li>
                </ul>
                <!-- /.sidebar-menu -->
            </section>
            <!-- /.sidebar -->
        </aside>
        @endif
		<div class="clearfix"></div>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @if(config('adminlte.layout') == 'top-nav')
            <div class="container">
            @endif

            <!-- Content Header (Page header) -->
            <section class="content-header">
				{{--@if (trim($__env->yieldContent('content_header_right')))--}}
				@yield('content_header_right')
				{{--@endif--}}
                @yield('content_header')
            </section>
            <!-- Main content -->
            <section class="content">
				<div class="box box-success">
					@if (trim($__env->yieldContent('box-title')))
					<div class="box-header with-border">
          				<h3 class="box-title">@yield('box-title')</h3>
					</div>
					@endif
					<div class="box-body">
                		@yield('content')
					</div><!--/box-body-->
					@if (trim($__env->yieldContent('box-footer')))
					<div class="box-footer">
						@yield('box-footer')
					</div><!--/box-footer-->
					@endif
				</div>
				@if(request()->route()->getName() == 'home')
				<h5>Aplikasi <strong>{{config('site.app_name')}}</strong> ini dibuat dan dikembangkan oleh Direktorat Pembinaan Sekolah Menengah Kejuruan</h5>
				<h5>Kementerian Pendidikan dan Kebudayaan Republik Indonesia</h5>
				@endif
            </section>
            <!-- /.content -->
            @if(config('adminlte.layout') == 'top-nav')
            </div>
            <!-- /.container -->
            @endif
        </div>
        <!-- /.content-wrapper -->
		<footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>{{config('site.app_name')}} Versi</b> {{config('global.app_version')}}
    </div>
    <strong>Hak Cipta &copy; <?php echo date('Y'); ?> <a href="https://psmk.kemdikbud.go.id/">Direktorat Pembinaan SMK</a></strong>.
  </footer>
    </div>
    <!-- ./wrapper -->
	<div id="spinner" style="position:fixed; top: 50%; left: 50%; margin-left: -50px; margin-top: -50px;z-index: 999999;display: none;">
		<img src="{{asset('vendor/img/ajax-loader.gif')}}" />
	</div>
	<div id="modal_content" class="modal fade"></div>
@stop

@section('adminlte_js')
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @stack('js')
    @yield('js')
	<script type="text/javascript">
		window.setTimeout(function() { $(".alert-dismissable").hide('slow'); }, 15000);
		$( document ).on( 'focus', ':input', function(){
			$( this ).attr( 'autocomplete', 'off' );
		});
		$('.select2').addClass('auto_width');
		$(document).bind("ajaxSend", function() {
			$("#spinner").show();
			$("#show").hide();
		}).bind("ajaxStop", function() {
			$("#spinner").hide();
			$("#show").show();
		}).bind("ajaxError", function() {
			$("#spinner").hide();
			$("#show").show();
		});
	</script>
@stop
