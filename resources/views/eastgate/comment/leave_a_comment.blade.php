<div class="content">
	<meta name="csrf-token" content="{{ csrf_token() }}">	

	<link rel="stylesheet" href="{{ URL::asset('eastgate/comment/css/comment.css') }}">
	<h1>Leave A Comment</h1>
	<div class="comment-content">
		@include('eastgate/comment/comment_fields')
	</div>
	@include('eastgate/comment/comment_list')
</div>

<script src="http://localhost/r2/public/jquery/1.11.1/jquery.min.js"></script>
<script src="http://localhost/r2/public/eastgate/comment/js/comment.js"></script>
<script type="text/javascript">
	$(document).ready(
		function(){
			$.ajaxSetup({
		        headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        }
			});
		}
	);
</script>
