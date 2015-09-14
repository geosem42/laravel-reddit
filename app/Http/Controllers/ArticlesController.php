<?php

namespace App\Http\Controllers;

use App\Article;
use App\User;
use App\Tag;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Requests\ArticleRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use App\Logic\Image\ImageRepository;
use Illuminate\Support\Facades\Input;

class ArticlesController extends Controller
{

    protected $image;

    public function __construct(ImageRepository $imageRepository) {
        $this->middleware('auth', ['only' => ['create', 'edit'] ]);
        $this->image = $imageRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $articles = Article::latest('published_at')->published()->get();

        return view('blog/index')->with('articles', $articles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $tags = Tag::lists('name', 'id');
        return view('blog/create')->with('tags', $tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ArticleRequest|BlogRequest|Request $request
     * @return Response
     */
    public function store(Requests\ArticleRequest $request)
    {
        // Old Submitting Method
        //$input = Request::all();
        //$input['published_at'] = Carbon::now();

        $this->createArticle($request);

        /* AJAX JSON RESPONSE
        $response = array(
            'status' => 'success',
            'msg' => 'Article has been posted. Redirecting now.',
        );
        return \Response::json($response);
        */

        // Old Method
        //Article::create($request->all());

        return redirect('articles');
    }

    /**
     * Display the specified resource.
     *
     * @param Article $article
     * @return Response
     * @internal param Article|Blog $blog
     * @internal param int $id
     */
    public function show(Article $article)
    {
        //$article = Article::findOrFail($id);

        /* if(is_null($article)) {
            abort(404);
        } */

        return view('blog/show')->with('article', $article);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Article $article
     * @return Response
     * @internal param Article $blog
     * @internal param int $id
     */


    public function edit(Article $article) {

        $user = Auth::id();

        if ($article->user->id != $user) {
            return view('home')->withErrors(['This is not your article']);
        }

        $tags = Tag::lists('name', 'id');

        return view('blog/edit')->with(compact('article', 'tags'));
    }



    /**
     * Update the specified resource in storage.
     *
     * @param ArticleRequest|BlogRequest|Request $request
     * @param Article $article
     * @return Response
     * @internal param Article|Blog $blog
     * @internal param int $id
     */
    public function update(ArticleRequest $request, Article $article)
    {
        //$article = Article::findOrFail($id);

        $article->update($request->all());

        $this->syncTags($article, $request->input('tag_list'));

        return redirect('articles');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param Article $article
     * @param array $tags
     * @internal param Article|Blog $blog
     * @internal param ArticleRequest $request
     */
    public function syncTags(Article $article, array $tags) {
        $article->tags()->sync($tags);
    }

    /**
     * @param ArticleRequest|BlogRequest $request
     * @return mixed
     */
    public function createArticle(Requests\ArticleRequest $request) {

        $article = Auth::user()->articles()->create($request->all());

        $this->syncTags($article, $request->input('tag_list'));

        return $article;
    }

    public function postUpload() {
        $photo = Input::all();
        $response = $this->image->upload($photo);
        return $response;

    }

    public function deleteUpload() {

        $filename = Input::get('id');

        if(!$filename)
        {
            return 0;
        }

        $response = $this->image->delete( $filename );

        return $response;
    }
}
