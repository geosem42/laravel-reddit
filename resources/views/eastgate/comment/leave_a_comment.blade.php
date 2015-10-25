<div class="content">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<h1>Leave A Comment</h1>
	<div class="comment-content">
		@include('eastgate/comment/comment_fields')
	</div>
	@include('eastgate/comment/comment_list')
</div>
<style>
	.editable-pre-wrapped {
		white-space: inherit !important;
	}
</style>

<script type="text/javascript">
	$(document).ready(
			function() {
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});

				$('.com').editable({
					validate: function(value) {
						if($.trim(value) == '')
							return 'Value is required.';
					},
					type: 'wysihtml5',
					title: 'Edit Comment',
					placement: 'top',
					send:'always',
					ajaxOptions: {
						dataType: 'json',
						type: 'post'
					}
				});
			});
</script>
