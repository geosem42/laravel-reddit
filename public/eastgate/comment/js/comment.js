$(document).ready(
	hide_comment_fields
);
$(document).on('focus', '.commenter-comment', show_comment_fields);

function hide_comment_fields(){
	$('.commenter-name-email').hide();
	$('.commenter-captcha').hide();	
}
function show_comment_fields(){
	$('.commenter-name-email').show();
	$('.commenter-captcha').show();
}

// Post a Comment
function commenter_fields(){
	return [
		'commenter_parent',
		'commenter_comment',
		'commenter_post'
	];
} 

$(document).on('click', 'a.post-this-comment', function(){
	var form_data = {
		'per_page': $('.comments_per_page').val(),
		'commenter_parent': $('#commenter_parent').val(),
		'commenter_post': $('#commenter_post').val(),
		'commenter_comment': $('#commenter_comment').val(),
	};

	var arr = [
		'commenter_parent',
		'commenter_post',
		'commenter_comment'
	];

	for (var i in arr, i < arr.length, i++) {
		var elem = arr[i];
		form_data[elem] = $('#' + elem).val();
	}

// console.log(form_data); // something like => Object {per_page: "some_value", commenter_parent: "some_value", commenter_user_id: "some_value", commenter_comment: "some_value"}

	var request = $.ajax({
		type: 'POST',
		url: 'post_this_comment',
		data: form_data,
		dataType: 'json'
	});

	request.done(comment_done_handler);
	request.fail(comment_fail_handler);
});

function comment_done_handler(data){
	console.log(data); // data is sent from server
	$('.comment-content').append($('.reply-content .comment-fields'));
	$('.comment-list').html(data.comment_list); // put new list
	$('#captcha-image').attr('src', data.captcha); // put new captchas
	$('.comment').upvote();
	$('.commentvote').on('click', function (e) {
		e.preventDefault();
		var $button = $(this);
		var commentId = $button.data('comment-id');
		var value = $button.data('value');
		$.post('http://localhost/r2/public/commentvotes', {commentId:commentId, value:value}, function(data) {
			if (data.status == 'success')
			{
				// Do something if you want..
			}
		}, 'json');
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
	clear_input_fields();
	remove_error_messages(data);
	hide_comment_fields();
}
function clear_input_fields()
{
	$('.comment-field').val('');
}

function remove_error_messages(data){	
	var arrayelem = commenter_fields();
	for(var i=0, size = arrayelem.length; i<size; i++){
		remove_validation_styles(arrayelem[i]);
	}
}
function add_validation_styles(fieldName, responseJSON){
	var closestDiv = $('#'+fieldName).closest('div');
	closestDiv.addClass('has-error');
	closestDiv.append('<label class="control-label error-msg">'+responseJSON[fieldName]+'</label>');
}

function remove_validation_styles(fieldName){
	var closestDiv = $('#'+fieldName).closest('div');
	closestDiv.removeClass('has-error');
	closestDiv.find('.error-msg').remove();
}

function comment_fail_handler(data)
{
	remove_error_messages(data); // remove existing messages and styles
	if(data.status == 422) {
		var arrayelem = commenter_fields();
		var elem;
		for(var i=0, size = arrayelem.length; i<size; i++){
			elem = arrayelem[i];
			if(data.responseJSON[elem])
				add_validation_styles(elem, data.responseJSON);
		}
	} else {
		//open a new window note:this is a popup so it may be blocked by your browser
		var newWindow = window.open("", "new window", "width=200, height=100");

   		//write the data to the document of the newWindow
		newWindow.document.write(data.responseText);
	}
}

$(document).on('click', 'a.recaptcha', function(){
	var request = $.ajax({ // push question data to server
		type 		: 'GET', // define the type of HTTP verb we want to use (POST for our form)
		url			: 'recaptcha', // the url where we want to POST
		data 		: [], 
		dataType	: 'json',
		processData	: false,
		contentType	: false
	});
	request.done(recaptcha_done_handler);	
	request.fail(recaptcha_fail_handler); // fail promise callback
});
function recaptcha_done_handler(data){
	$('#captcha-image').attr('src', data.captcha);
	remove_error_messages();
}
function recaptcha_fail_handler(data){
	alert('recaptcha failed: '+data.responseText);
}

$(document).on('click', 'a.reply', function(){
	var request = $.ajax({ // push question data to server
		type 		: 'GET', // define the type of HTTP verb we want to use (POST for our form)
		url			: 'reply_comment', // the url where we want to POST
		data 		: [], 
		dataType	: 'json',
		processData	: false,
		contentType	: false
	});
	var parent_id = extract_parent_id($(this).attr('class'), 'comment');
	request.done(
		function(data){
			reply_comment_done_handler(data, parent_id);
		}
	);	
	request.fail(reply_comment_fail_handler); // fail promise callback
});
function reply_comment_done_handler(data, parent_id){
	$('.reply'+parent_id).append($('.comment-fields'));
	$('.reply'+parent_id).prepend(data.cancel_reply);
	$('.commenter-parent').val(parent_id); // set parent id
}
function reply_comment_fail_handler(data){
	alert('reply comment failed');
}

function extract_parent_id(classes_attr, prefix){ // example: extract id 12 from class such as comment12

	var classes = classes_attr.split(' ');

	for(var i=0; i < classes.length; i++)
		if(classes[i].indexOf(prefix) > -1){
			var result = classes[i].substr(prefix.length);			
			return result;
		}
	return '';  // no id found, return empty string
}

$(document).on('click', 'a.cancel-reply', function(){
	$('.comment-content').append($('.comment-fields'));
	$('.cancel-reply-field').remove();
});

$(document).on('change', 'input.comments_per_page', function(){
	var formData = new FormData();
	formData.append('per_page', $('.comments_per_page').val());
	var request = $.ajax({ // push question data to server
		type 		: 'POST', // define the type of HTTP verb we want to use (POST for our form)
		url			: 'per_page', // the url where we want to POST
		data 		: formData, 
		dataType	: 'json',
		processData	: false,
		contentType	: false
	});
	request.done(per_page_done_handler);
	request.fail(per_page_fail_handler); // fail promise callback	
});
function per_page_done_handler(data){
	console.log('Per page successful');
	$('.comment-list').html(data.comment_list); // put new list
}
function per_page_fail_handler(data){
	console.log('Per page failed');
}