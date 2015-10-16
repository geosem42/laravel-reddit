<div class="comment-list">
	<div class="row">
		<div class="col-xs-12">	
			<h2>{!! $total_comments !!} comment(s) </h2>

			@foreach($comments as $each_comment)
				<?php 
					$name_for_display = $each_comment->name?$each_comment->name:'Anonymous';
					$date_for_display = $each_comment->created_at->diffForHumans();
					$parent_name_for_display = '';
					if($each_comment->parent_id > 0){
						$parent_comment = $each_comment->parent();
						$parent_name_for_display = $parent_comment != null && $parent_comment->name
													? $parent_comment->name : 'Anonymous';
						$parent_name_for_display = '<span class="glyphicon glyphicon-share-alt" title="Reply to">&nbsp;</span>'.$parent_name_for_display;
					}
					$parents_count = substr_count($each_comment->parents, '.');
					$offset_length = $parents_count;
					$comment_length = 12 - $offset_length;
				?>
				<div class="col-xs-offset-{!! $offset_length !!} col-xs-{!! $comment_length !!}">
					<ul class="list-inline">
						<li class="comment-by">{!! $name_for_display !!}</li>
						@if($parents_count > 0)
							<li class="reply-to">{!! $parent_name_for_display !!}</li>
						@endif
						<li class="separator"></li>
						<li class="comment-on">{!! $date_for_display !!}</li>
					</ul>

					<p>{!! $each_comment->comment !!}</p>
					
					<a href="javascript:void(0)" class="reply comment{!! $each_comment->id !!}" title="Reply to above comment">Reply</a>
					
					<div class="reply-content reply{!! $each_comment->id !!}"></div>
					
					<hr>
				</div>
			@endforeach
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			{!! $comments->render() !!}
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			Show <input type="text" name="comments_per_page" class="comments_per_page" value="{!! $per_page !!}" size="2" title="Number of comments per page"> comments per page
		</div>
	</div>
</div>