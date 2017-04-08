<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Loan CRUD Mini Application</title>

	<link rel="stylesheet" type="text/css" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
	{{-- <link rel="stylesheet" type="text/css" href="{{ asset('bower_components/datepicker/css/datepicker.css') }}"> --}}
	<link rel="stylesheet" type="text/css" href="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css">

</head>
<body>
		
	<div class="row">
		<div class="container">
			@include('layout.flash_message')

			@yield('content')
		</div>
	</div>

	<script type="text/javascript" src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
	{{-- <script type="text/javascript" src="{{ asset('bower_components/datepicker/js/bootstrap-datepicker.js') }}"></script> --}}
	<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>

	{{-- custom script --}}
	<script type="text/javascript" src="{{ asset('custom.js') }}"></script>

</body>
</html>