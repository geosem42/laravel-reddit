<?php

namespace App\Http\Controllers;

use App\Post;
use App\Subreddit;
use Embed\Embed;
use Image;
use File;
use App\User;
use App\Moderator;
use App\Http\Requests;
use App\Http\Requests\PostRequest;
use App\Http\Requests\EditPostRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Comment;
use Illuminate\Pagination\LengthAwarePaginator;
use Gregwar\Captcha\CaptchaBuilder;
use DB;
use Validator;
use Log;
use App\Config;

class PostsController extends Controller
{
    public function __construct() {
        $this->middleware('auth', ['only' => ['create', 'edit'] ]);
    }

    public function create()
    {
        $subreddits = Subreddit::lists('name', 'id')->toArray();

        return view('post/create')->with('subreddits', $subreddits);
    }

    public function getSubreddits($query = '') {
        $q = Subreddit::select('id', 'name');
        if ($query) {
            $q->where('name', 'LIKE', '%' . $query . '%');
        }
        return Response::json($q->get());
    }

    public function store(PostRequest $request)
    {
        if (Input::has('link')) {
            $input['link'] = Input::get('link');
            $info = Embed::create($input['link']);

            if ($info->image == null) {
                $embed_data = ['text' => $info->description];
            } else if ($info->description == null) {
                $embed_data = ['text' => ''];
            } else {
                $extension = pathinfo($info->image, PATHINFO_EXTENSION);

                $newName = public_path() . '/images/' . str_random(8) . ".{$extension}";

                if (File::exists($newName)) {
                    $imageToken = substr(sha1(mt_rand()), 0, 5);
                    $newName = public_path() . '/images/' . str_random(8) . '-' . $imageToken . ".{$extension}";
                }

                $image = Image::make($info->image)->fit(70, 70)->save($newName);
                $embed_data = ['text' => $info->description, 'image' => basename($newName)];
            }

            Auth::user()->posts()->create(array_merge($request->all(), $embed_data));

            return redirect('/subreddit');
        }
        Auth::user()->posts()->create($request->all());

        return redirect('/subreddit');
    }

    public function show(Post $post, User $user, Request $request)
    {
        $post = Post::with('user.votes')->with('subreddit.moderators')->findOrFail($post->id);
        $ids = $post->subreddit;
        $isModerator = $ids->moderators()->where('user_id', Auth::id())->exists();
        $modList = Moderator::where('subreddit_id', '=', $post->subreddit->id)->get();
        $view_data = self::view_data($request);

        return view('post/show', $view_data)->with('post', $post)
                                ->with('modList', $modList)
                                ->with('isModerator', $isModerator);
    }

    public function edit(Post $post)
    {
        $post = Post::with('user.votes')->with('subreddit.moderators')->findOrFail($post->id);
        $ids = $post->subreddit;
        $isModerator = $ids->moderators()->where('user_id', Auth::id())->exists();
        if (Gate::denies('update-post', [$post, $isModerator])) {
            return redirect('subreddit')->withErrors('You cannot edit this post.');
        } else {
            return view('post/edit')->with('post', $post)->with('isModerator', $isModerator);
        }
    }

    public function update(EditPostRequest $request, Post $post)
    {
        $post = Post::with('user.votes')->with('subreddit.moderators')->findOrFail($post->id);
        $ids = $post->subreddit;
        $isModerator = $ids->moderators()->where('user_id', Auth::id())->exists();
        if (Gate::denies('update-post', [$post, $isModerator])) {
            return redirect('subreddit')->withErrors('You cannot edit this post.');
        } else {
            $post->update($request->all());
            return redirect('/subreddit');
        }
    }

    public function destroy($id)
    {
        //
    }

    public function captcha_builder(){
        $captcha = new CaptchaBuilder;
        $builder = $captcha->build();
        session(['phrase' => $builder->getPhrase()]);
        return $builder;
    }

    protected function total_comments(){
        return DB::table('comments')->count();
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

    private function paginate($items, $perPage, Request $request){
        $page = Input::get('page', 1); // get current page or default to 1
        $offset = ($page * $perPage) - $perPage;
        return new LengthAwarePaginator(
            array_slice($items, $offset, $perPage, false),
            count($items),
            $perPage,
            $page,
            ['path'=> $request->url(), 'query'=> $request->query()]);
    }

    protected function comment_list($per_page, Request $request){
        $root_comments = Comment::root_comments();
        $root_with_replies = $this->include_replies_for($root_comments);
        $paginated_comments = $this->paginate($root_with_replies, $per_page, $request);
        return $paginated_comments;
    }

    public static function view_data(Request $request){
        $instance = new Self;
        $per_page = session('per_page')?session('per_page'):config('constants.per_page'); // default per page on opening the comment page
        $result['per_page'] = $per_page;
        $result['comments'] = $instance->comment_list($per_page, $request);
        $result['total_comments'] = $instance->total_comments();
        return $result;
    }

    public function get_per_page(){
        return session('per_page')?session('per_page'):config('constants.per_page'); // default per page on opening the comment page
    }

    public function post_this_comment(Request $request){

        $comment = new Comment;
        $comment->user_id = Auth::id();
        $comment->comment = $request->input('commenter_comment');
        dd($request->input('commenter_comment'));
        $comment->parent_id = Input::get('commenter_parent');
        if($comment->parent_id > 0){
            $my_parent = Comment::find($comment->parent_id);
            $comment->parents = $my_parent->parents.'.'.$comment->parent_id;
        }else{
            $comment->parents = '0';
        }
        $comment->save();
        $per_page = Input::get('per_page');
        $comment_list = view('eastgate.comment.comment_list')
            ->with('comments', $this->comment_list($per_page, $request))
            ->with('total_comments', $this->total_comments())
            ->with('per_page', $per_page)
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
            'msg'		=> 'new captcha',
            'captcha'	=> $this->captcha_builder()->inline()
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

    protected function show_comment_list($request){
        $per_page = Input::get('per_page');
        session(['per_page' => $per_page]);
        $comment_list = view('eastgate.comment.comment_list')
            ->with('comments', $this->comment_list($per_page, $request))
            ->with('total_comments', $this->total_comments())
            ->with('per_page', $per_page)
            ->render();
        return $comment_list;
    }

    public function per_page(Request $request){
        $response = array(
            'status' => 'success',
            'msg'	=> 'reply comment',
            'comment_list' => $this->show_comment_list($request)
        );
        return Response::json($response);
    }
}
