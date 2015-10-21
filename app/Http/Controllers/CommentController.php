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

	/**
	 * Calculates number of rows in the 'comment' table
	 * 
	 * @return integer
	 */
	protected function total_comments(Post $post){
		return DB::table('comments')->where('post_id', $post->id)->count();
	}

	/**
	 * Makes an array such a way that child comments placed next to parent comment
	 * to show tree like structure on view
	 * 
	 * @param  array $comments - parent comment only
	 * @return array - comments with child comments
	 */
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

	/**
	 * Manually build pagination on the array of $items.
	 * 
	 * @param  array  $items
	 * @param  int  $perPage 
	 * @param  Request $request 
	 * @return LengthAwarePaginator
	 */
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

	/**
	 * Builds Paginated Comment List to be shown on a view
	 * 
	 * @param  int  $per_page
	 * @param  Request $request
	 * @return LengthAwarePaginator
	 */
	protected function comment_list($per_page, Request $request, Post $post) {
		$root_comments = Comment::root_comments($post->id);
		$root_with_replies = $this->include_replies_for($root_comments);
		$paginated_comments = $this->paginate($root_with_replies, $per_page, $request, $post);
		return $paginated_comments;
	}

	/**
	 * Get Home page view
	 * 
	 * @param  Request $request
	 * @return view - home page
	 */
	public function index(Request $request, Post $post){
		$view_data = CommentController::view_data($request, $post->id);
		return view('eastgate.comment.leave_a_comment', $view_data);
/*		$per_page = session('per_page')?session('per_page'):config('constants.per_page'); // default per page on opening the comment page
		return view('eastgate.comment.leave_a_comment')
				->with('comments', $this->comment_list($per_page, $request))
				->with('per_page', $per_page)
				->with('total_comments', $this->total_comments())
				->with('captcha_builder', $this->captcha_builder());
*/
	}

	/**
	 * Get data required for comment view
	 * @param  Request $request [description]
	 * @return Array
	 */
	public static function view_data(Request $request, Post $post) {

		$instance = new Self;
		$per_page = session('per_page')?session('per_page'):config('constants.per_page'); // default per page on opening the comment page
		$result['per_page'] = $per_page;
		$result['comments'] = $instance->comment_list($per_page, $request, $post);
		$result['total_comments'] = $instance->total_comments($post);
		return $result;
	}

	public function get_per_page(){
		return session('per_page')?session('per_page'):config('constants.per_page'); // default per page on opening the comment page
	}

	/**
	 * Post user submitted data, validates and saves as a new comment
	 * 
	 * @param  Request $request
	 * @return Response with a json object
	 */
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
		$comment_list = view('eastgate.comment.comment_list')
							->with('comments', $this->comment_list($per_page, $request, $post))
							->with('total_comments', $this->total_comments($post))
							->with('per_page', $per_page)
							->render();
		$response = array(
			'status' => 'success',
			'msg' => 'Comment Saved!',
			'comment_list' => $comment_list
		);
		return Response::json($response);
	}

    /**
     * Get a validator for an incoming post_this_comment request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'commenter_comment' => 'required|min:2',
        ]);
    }

    /**
     * Get a recaptcha builder
     * 
     * @return Response with a json object containing recaptcha builder
     */
    public function recaptcha(){
    	$response = array(
    		'status'	=> 'success',
    		'msg'		=> 'new captcha'
    	);
    	return Response::json($response);
    }

    /**
     * Get a Cancel link to display in the Reply view
     * 
     * @return Response with json object
     */
    public function reply_comment(){
    	$response = array(
    		'status' => 'success',
    		'msg'	=> 'reply comment',
    		'cancel_reply' => $this->show_cancel_reply()
    	);
    	return Response::json($response);
    }

    /**
     * builds view for cancel link
     * 
     * @return view on cancel link
     */
    protected function show_cancel_reply(){
    	return view('eastgate.comment.cancel_reply')->render();
    }

    /**
     * builds view for comment list
     * 
     * @param  Request $request
     * @return view on comment list
     */
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

    /**
     * Post Per Page data, in turn, Get updated comment list based on new Per Page
     * 
     * @param  Request $request
     * @return Response with json object
     */
    public function per_page(Request $request, Post $post){
    	$response = array(
    		'status' => 'success',
    		'msg'	=> 'reply comment',
    		'comment_list' => $this->show_comment_list($request, $post)
    	);
    	return Response::json($response);    	
    }
}