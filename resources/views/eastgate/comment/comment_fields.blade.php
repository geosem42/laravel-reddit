<div class="comment-fields">
	<div class="row commenter-comment">
		<div class="form-group col-md-12">
			<textarea id="commenter_comment" name="commenter_comment" class="form-control comment-field" title="User's comment" placeholder="Comment Text"></textarea>
		</div>
	</div>

	<div class="row commenter-name-email">
		<input type="hidden" id="commenter_parent" name="commenter_parent" class="commenter-parent" value="0">
		<input type="hidden" id="commenter_post" name="commenter_post" class="commenter-post" value="{{ $post->id }}">
	</div>

	<div class="row commenter-captcha">
		<div class="col-md-3">
			<a href="javascript:void(0)" class="btn btn-success post-this-comment">Comment</a>
		</div>
	</div>
</div>