<!-- Subreddit Search Well -->
<div class="well" style="margin-top: 30px;">
    <h4>Search {{ $post->subreddit->name }}</h4>
    {!! Form::open(['route' => ['search_post', $post]]) !!}
    <div id="custom-search-input">
        <div class="input-group col-md-12">
            <input type="text" name="search" class="search-query form-control" placeholder="Search" />
            <input type="hidden" id="subreddit_id" name="subreddit_id" class="post-subreddit" value="{{ $post->subreddit->id }}">
                <span class="input-group-btn">
                    <button class="btn btn-success" type="submit">
                        <span class=" glyphicon glyphicon-search"></span>
                    </button>
                </span>
        </div>
    </div>
    {!! Form::close() !!}
    <!-- /.input-group -->
</div>

<!-- Side Widget Well -->
<div class="well">
    <h4>About {{ $post->subreddit->name }}</h4>
    <p>{!! strip_tags($post->subreddit->description, '<b><a><img><i><u><p><br><ul><ol><li><h1><h2><h3><blockquote>') !!}</p>
</div>

<!-- Subreddit Moderators Well -->
<div class="well">
    <h4>Moderators Of {{ $post->subreddit->name }}</h4>
    <div class="row">
        <div class="col-lg-6">
            <ul class="list-unstyled">
                @foreach($modList as $mod)
                    <li>{!!  link_to_route('profile_path', $mod->user->name, $mod->user->name) !!}</li>
                @endforeach
            </ul>
        </div>
        <!-- /.col-lg-6 -->
        <div class="col-lg-6">
            <ul class="list-unstyled">
            </ul>
        </div>
        <!-- /.col-lg-6 -->
    </div>
    <!-- /.row -->
</div>
