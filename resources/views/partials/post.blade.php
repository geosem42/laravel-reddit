<div class="row">
    <div class="span8">
        <div class="row">
            <div class="col-md-12">
                <h4><strong><a href="#"></a></strong></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-1">
                <div class="upvote topic upvote-disabled" data-post="{{ $post->id }}">
                    <a id="up" class="upvote vote {{ $post->votes && $post->votes->contains('user_id', Auth::id()) ? ($post->votes->where('user_id', Auth::id())->first()->value > 0 ? 'upvote-on' : null) : null}}" data-value="1" data-post-id="{{ $post->id }}"></a>
                    <!-- Notice how we set the sum of the votes for this post here -->
                    <span class="count">{{ $post->votes->sum('value') }}</span>
                    <a id="down" class="downvote vote {{ $post->votes && $post->votes->contains('user_id', Auth::id()) ? ($post->votes->where('user_id', Auth::id())->first()->value < 0 ? 'downvote-on' : null) : null}}" data-value="-1" data-post-id="{{ $post->id }}"></a>
                </div>
            </div>
            <div class="col-md-1">
                <a href="#" class="thumbnail">
                    @if ($post->image == null)
                        <img src="{{ URL::to('/images/') }}/default.png" alt="">
                    @else
                        <img src="{{ URL::to('/images/') . '/' . $post->image }}" alt="">
                    @endif
                </a>
            </div>
            <div class="col-md-10">
                <p>
                    @if ($post->link == null)
                        <a href="{{ action('PostsController@show', [$post->id]) }}">{{ $post->title }}</a>
                    @else
                        <?php $parse = parse_url($post->link); ?>
                        <a href="{{ $post->link }}" target="_blank">{{ $post->title }}</a> <span class="label label-info">{{ $parse['host'] }}</span>
                    @endif
                </p>
                <p style="color: darkgrey; font-size: 12px;">
                    <i class="glyphicon glyphicon-user" style="padding-right: 5px;"></i>submitted by {!!  link_to_route('profile_path', $post->user->name, $post->user->name) !!}
                    <i class="glyphicon glyphicon-calendar" style="padding-left: 15px;"></i> {{ $post->created_at->diffForHumans() }}
                    <i class="glyphicon glyphicon-bullhorn" style="padding-left: 15px;"></i> <a href="{{ action('SubredditController@show', [$post->subreddit->id]) }}">{{ $post->subreddit->name }}</a>
                    <i class="glyphicon glyphicon-comment" style="padding-left: 15px;"></i> <a href="{{ action('PostsController@show', [$post->id]) }}">{{ $post->comments->count() }} Comments</a>
                    @can('update-post', [$post, $isModerator])
                        <i class="glyphicon glyphicon-pencil" style="padding-left: 15px;"></i> <a href="{{ action('PostsController@edit', $post->id) }}">Edit</a>
                    @endcan

                </p>
                @if(Request::is('posts/*'))
                    <p>{!!  $post->text !!}</p>
                @endif
            </div>
        </div>
    </div>
</div>
