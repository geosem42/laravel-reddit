@extends('layouts/default')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-10"><h1>{{ $user->name }}</h1></div>
        </div>
        <div class="row">
            <div class="col-sm-3"><!--left col-->

                <ul class="list-group">
                    <li class="list-group-item text-muted">Profile</li>
                    <li class="list-group-item text-right"><span class="pull-left"><strong>Joined</strong></span> {{ $user->created_at->diffForHumans() }}</li>
                    <li class="list-group-item text-right"><span class="pull-left"><strong>Link Karma</strong></span> {{ $linkKarma }}</li>
                    <li class="list-group-item text-right"><span class="pull-left"><strong>Comment Karma</strong></span> {{ $commentKarma }}</li>

                </ul>

            </div><!--/col-3-->
            <div class="col-sm-9">

                <ul class="nav nav-tabs" id="myTab">
                    <li class="active"><a href="#posts" data-toggle="tab">Posts</a></li>
                    <li><a href="#comments" data-toggle="tab">Comments</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="posts">
                        <div class="">
                            @foreach($user->posts as $post)
                                @include('partials/post')
                            @endforeach

                        </div><!--/table-resp-->

                        <hr>

                    </div><!--/tab-pane-->
                    <div class="tab-pane" id="comments">

                        @foreach($comments as $each_comment)
                            <?php
                            $name_for_display = $each_comment->user->name;
                            $date_for_display = $each_comment->created_at->diffForHumans();
                            $parent_name_for_display = '';
                            if($each_comment->parent_id > 0){
                                $parent_comment = $each_comment->parent();
                                $parent_name_for_display = $parent_comment != null && $each_comment->post_id
                                        ? $each_comment->post_id : $each_comment->post_id;
                                $parent_name_for_display = '<span class="glyphicon glyphicon-share-alt" title="Reply to">&nbsp;</span>'.$parent_name_for_display;
                            }
                            $parents_count = substr_count($each_comment->parents, '.');
                            $offset_length = $parents_count;
                            $comment_length = 12 - $offset_length;
                            ?>
                            <div class="col-xs-offset-0 col-xs-12">
                                <div class="col-md-1">
                                    <div class="upvote comment" data-comment="{{ $each_comment->id }}">
                                        <a class="upvote commentvote {{ $each_comment->commentvotes && $each_comment->commentvotes->contains('user_id', Auth::id()) ? ($each_comment->commentvotes->where('user_id', Auth::id())->first()->value > 0 ? 'upvote-on' : null) : null}}" data-value="1" data-comment-id="{{ $each_comment->id }}"></a>
                                        <!-- Notice how we set the sum of the votes for this post here -->
                                        <span class="count">{{ $each_comment->commentvotes->sum('value') }}</span>
                                        <a class="downvote commentvote {{ $each_comment->commentvotes && $each_comment->commentvotes->contains('user_id', Auth::id()) ? ($each_comment->commentvotes->where('user_id', Auth::id())->first()->value < 0 ? 'downvote-on' : null) : null}}" data-value="-1" data-comment-id="{{ $each_comment->id }}"></a>
                                    </div>
                                </div>
                                <div class="col-md-11">
                                    <input type="hidden" id="postid" name="postid" class="post-id" value="{{ $each_comment->post_id }}">
                                    <ul class="list-inline">
                                        <li class="comment-by">{!! $name_for_display !!}</li>
                                        <li class="reply-to"><span class="glyphicon glyphicon-share-alt" title="Reply to">&nbsp;</span> <a href="{{ action('PostsController@show', $each_comment->post_id) }}">{!! $each_comment->posts->title !!}</a></li>
                                        <li class="separator"></li>
                                        <li class="comment-on">{!! $date_for_display !!}</li>
                                    </ul>

                                    <p>{!! $each_comment->comment !!}</p>

                                    <div class="reply-content reply{!! $each_comment->id !!}"></div>

                                    <hr>
                                </div>
                            </div>
                        @endforeach

                    </div><!--/tab-pane-->

                </div><!--/tab-pane-->
            </div><!--/tab-content-->

        </div><!--/col-9-->
    </div><!--/row-->


@stop