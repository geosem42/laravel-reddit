<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Controllers;
use App\User;
use Input;
use DB;
use Illuminate\Http\Request;
use Response;
use Validator;
use Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Config;
use Auth;
use App\Post;

class CommentController extends Controller {

	public function __construct() {
		$this->middleware('auth', ['only' => ['post_this_comment', 'update'] ]);
	}

	protected function total_comments(Post $post){
		return DB::table('comments')->where('post_id', $post->id)->count();
	}

	private function include_replies_for($comments){
		$parent = array();
		foreach($comments as $each_comment){
			$parent[] = $each_comment;
			$children = Comment::child_comments($each_comment->id);
			if(count($children) > 0){
				$children_with_replies = $this->include_replies_for($children);
				$parent = array_merge($parent, $children_with_replies);
			}
		}
		return $parent;
	}

	private function paginate($items, $perPage, Request $request) {
		$page = Input::get('page', 1); // get current page or default to 1
		$offset = ($page * $perPage) - $perPage;
		return new LengthAwarePaginator(
			array_slice($items, $offset, $perPage, false),
			count($items),
			$perPage,
			$page,
			['path' => $request->url(), 'query' => $request->query()]);
	}

	protected function comment_list($per_page, Request $request, Post $post) {
		$root_comments = Comment::root_comments($post->id);
		$root_with_replies = $this->include_replies_for($root_comments, $per_page, $request, $post);
		//$paginated_comments = $this->paginate($root_with_replies, $per_page, $request, $post);
		//$paginated_comments->setPath('');
		return $root_with_replies;
	}

	public function index(Request $request, Post $post, Comment $comment) {
		$view_data = CommentController::view_data($request, $post->id);
		return view('eastgate.comment.leave_a_comment', $view_data);
	}

	public static function view_data(Request $request, Post $post) {
		$instance = new Self;
		$per_page = session('per_page')?session('per_page'):config('constants.per_page');
		$post = Post::with('user.votes')->with('subreddit.moderators')->with('comments')->where('id', $post->id)->first();
		$comment = $post->comments;
		$user = User::where('id', '=', Auth::id())->get();
		$check = $post->subreddit->moderators->where('user_id', Auth::id())->first();
		$isModerator = $check ? true:false;
		$result['per_page'] = $per_page;
		$result['comments'] = $instance->comment_list($per_page, $request, $post, $comment, $user, $isModerator);
		$result['total_comments'] = $instance->total_comments($post);
		return $result;
	}

	public function get_per_page(){
		return session('per_page')?session('per_page'):config('constants.per_page'); // default per page on opening the comment page
	}

	public function post_this_comment(Request $request, Post $post) {
		$validator = $this->validator($request->all());

        if ($validator->fails()) {  
        	$this->throwValidationException(
                $request, $validator);
        }
		$comment = new Comment;
		$comment->user_id = Auth::id();
		$comment->comment = Input::get('commenter_comment');
		$comment->post_id = Input::get('commenter_post');
		$comment->parent_id = Input::get('commenter_parent');
		if($comment->parent_id > 0){
			$my_parent = Comment::find($comment->parent_id);
			$comment->parents = $my_parent->parents.'.'.$comment->parent_id;
		}else{
			$comment->parents = '0';
		}
		$comment->save();
		$per_page = Input::get('per_page');
		$post = $post->find($request->commenter_post);
		$check = $post->subreddit->moderators->where('user_id', Auth::id())->first();
		$isModerator = $check ? true:false;
		$comment_list = view('eastgate.comment.comment_list')
							->with('comments', $this->comment_list($per_page, $request, $post))
							->with('total_comments', $this->total_comments($post))
							->with('per_page', $per_page)
							->with('isModerator', $isModerator)
							->render();
		$response = array(
			'status' => 'success',
			'msg' => 'Comment Saved!',
			'comment_list' => $comment_list
		);
		return Response::json($response);
	}

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'commenter_comment' => 'required|min:2',
        ]);
    }

    public function recaptcha(){
    	$response = array(
    		'status'	=> 'success',
    		'msg'		=> 'new captcha'
    	);
    	return Response::json($response);
    }

    public function reply_comment(){
    	$response = array(
    		'status' => 'success',
    		'msg'	=> 'reply comment',
    		'cancel_reply' => $this->show_cancel_reply()
    	);
    	return Response::json($response);
    }

    protected function show_cancel_reply(){
    	return view('eastgate.comment.cancel_reply')->render();
    }

    protected function show_comment_list(Request $request, Post $post) {
		$per_page = Input::get('per_page');
		session(['per_page' => $per_page]);
		$comment_list = view('eastgate.comment.comment_list')
							->with('comments', $this->comment_list($per_page, $request, $post))
							->with('total_comments', $this->total_comments())
							->with('per_page', $per_page)
							->render();
		return $comment_list;    	
    }

    public function per_page(Request $request, Post $post){
    	$response = array(
    		'status' => 'success',
    		'msg'	=> 'reply comment',
    		'comment_list' => $this->show_comment_list($request, $post)
    	);
    	return Response::json($response);    	
    }

	public function update(Request $request) {
		$pk = $request->input('pk');
		$comment = Input::get('value');
		$commentData = Comment::whereId($pk)->first();
		$commentData->comment = $comment;
		if($commentData->save())
			return Response::json(array('status'=>1));
		else
			return Response::json(array('status'=>0));
	}
}