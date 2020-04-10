<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>@yield('title_prefix', config('adminlte.title_prefix', '')) @yield('title_postfix', config('adminlte.title_postfix', '')) @yield('title', config('adminlte.title', 'e-Rapor SMK'))
	</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.7 -->
	<link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/bootstrap/dist/css/bootstrap.min.css') }}">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/font-awesome/css/font-awesome.min.css') }}">
	<!-- Ionicons -->
	<link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/Ionicons/css/ionicons.min.css') }}">
	@if(config('adminlte.plugins.datatables'))
	<!-- DataTables with bootstrap 3 style -->
	<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables/datatables.min.css') }}">
	@endif
	@if(config('adminlte.plugins.datepicker'))
	<!--datepicker-->
	<link  rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datepicker/datepicker3.css') }}">
	@endif
	@if(config('adminlte.plugins.select2'))
	<!-- Select2 -->
	<link  rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/select2/css/select2.min.css') }}">
    @endif
	<!-- Theme style -->
	<link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/AdminLTE.min.css') }}">
@yield('adminlte_css')
    <style>
	.auto_width{width:100% !important;}
	</style>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Google Font -->
    <!--link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic"-->
	<link rel="stylesheet" href="{{ asset('vendor/adminlte/css/google-fonts.css') }}">
	<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
</head>
<body class="hold-transition @yield('body_class')">

@yield('body')

<script src="{{ asset('vendor/adminlte/vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/vendor/jquery/dist/jquery-ui.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/vendor/jquery/dist/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/sweetalert/sweetalert.min.js') }}"></script>
<!--script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script-->
@if(config('adminlte.plugins.select2'))
    <!-- Select2 -->
    <!--script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script-->
	<!--script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full.js"></script-->
	<script src="{{ asset('vendor/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
@endif

@if(config('adminlte.plugins.datatables'))
    <!-- DataTables with bootstrap 3 renderer -->
	<script src="{{ asset('vendor/adminlte/plugins/datatables/datatables.min.js') }}"></script>
@endif

@if(config('adminlte.plugins.chartjs'))
    <!-- ChartJS >
    <script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js"></script-->
@endif
@if(config('adminlte.plugins.datepicker'))
	<!--datepicker-->
	<script src="{{ asset('vendor/adminlte/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
@endif
@yield('adminlte_js')

</body>
</html>
