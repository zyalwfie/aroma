<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="UTF-8">
		<meta content="width=device-width, initial-scale=1.0" name="viewport">
		<title>Aroma Shop</title>
		<link href="{{ asset("assets/vendors/bootstrap/bootstrap.min.css") }}" rel="stylesheet">
		<link href="{{ asset("assets/vendors/fontawesome/css/all.min.css") }}" rel="stylesheet">
		<link href="{{ asset("assets/vendors/themify-icons/themify-icons.css") }}" rel="stylesheet">
		<link href="{{ asset("assets/vendors/nice-select/nice-select.css") }}" rel="stylesheet">
		<link href="{{ asset("assets/vendors/owl-carousel/owl.theme.default.min.css") }}" rel="stylesheet">
		<link href="{{ asset("assets/vendors/owl-carousel/owl.carousel.min.css") }}" rel="stylesheet">
		<link href="{{ asset("assets/css/style.css") }}" rel="stylesheet">
		<link href="{{ asset("assets/vendors/nouislider/nouislider.min.css") }}" rel="stylesheet">

		<style>
			.btn.active {
				background-color: #007bff;
				color: white;
			}
		</style>
	</head>

	<body>
		@include("frontend.layouts.header")

		<main>
			@yield("content")
		</main>

		@include("frontend.layouts.footer")

		<script src="{{ asset("assets/vendors/jquery/jquery-3.2.1.min.js") }}"></script>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>
		<script src="{{ asset("assets/vendors/nouislider/nouislider.min.js") }}"></script>
		<script src="{{ asset("assets/vendors/bootstrap/bootstrap.bundle.min.js") }}"></script>
		<script src="{{ asset("assets/vendors/skrollr.min.js") }}"></script>
		<script src="{{ asset("assets/vendors/owl-carousel/owl.carousel.min.js") }}"></script>
		<script src="{{ asset("assets/vendors/nice-select/jquery.nice-select.min.js") }}"></script>
		<script src="{{ asset("assets/vendors/jquery.ajaxchimp.min.js") }}"></script>
		<script src="{{ asset("assets/vendors/mail-script.js") }}"></script>
		<script type="module" src="{{ asset("assets/js/main.js") }}"></script>

		@stack("scripts")
	</body>

</html>
