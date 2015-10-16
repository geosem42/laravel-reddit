<div class="comment-fields">
	<div class="row commenter-comment">
		<div class="form-group col-md-12">
			<textarea id="commenter_comment" name="commenter_comment" class="form-control comment-field" title="User's comment" placeholder="Comment Text"></textarea>
		</div>
	</div>

	<div class="row commenter-name-email">
		<input type="hidden" id="commenter_parent" name="commenter_parent" class="commenter-parent" value="0">
		<div class="form-group col-md-6">
			<input type="text" id="commenter_name" name="commenter_name" class="form-control comment-field" title="User's name" placeholder="Name (optional)">
		</div>
		<div class="form-group col-md-6">
			<input type="email" id="commenter_email" name="commenter_email" class="form-control comment-field" title="User's email" placeholder="Email Id (optional)">
		</div>
	</div>

	<div class="row commenter-captcha">
		<div class="col-md-6">		
			<img id="captcha-image" src="{{ $captcha_builder->inline() }}" />
			<a href="javascript:void(0)" title="Re-Captcha" class="recaptcha"><span class="glyphicon glyphicon-refresh"></span></a><br>
		</div>
		<div class="form-group col-md-3">
			<input type="text" id="commenter_captcha" name="commenter_captcha" class="form-control comment-field" title="Captcha letters" placeholder="Captcha">
		</div>
		<div class="col-md-3 text-right">
			<a href="javascript:void(0)" class="btn btn-info post-this-comment">Post</a>
		</div>
	</div>
</div>