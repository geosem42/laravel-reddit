@extends('layouts/default')

@section('content')
    <div class="row">
        <div class="col-md-8">
            @include('partials/post')

            <div class="comment-fields">
                <div class="row commenter-comment">
                    <div class="form-group col-md-12">
                        <textarea id="commenter_comment" name="commenter_comment" class="form-control comment-field" title="User's comment" placeholder="Comment Text"></textarea>

                    </div>
                </div>

                <div class="row commenter-name-email">
                    <input type="hidden" id="commenter_parent" name="commenter_parent" class="commenter-parent" value="0">
                </div>

                <div class="row commenter-captcha">
                    <div class="col-md-3 text-right">
                        <a href="javascript:void(0)" class="btn btn-info post-this-comment">Post</a>
                    </div>
                </div>

            </div>



            <div class="comment-list">
                <div class="row">
                    <div class="col-xs-12">
                        <h2>{!! $total_comments !!} comment(s) </h2>

                        @foreach($comments as $each_comment)
                            <?php
                            $name_for_display = $each_comment->name?$each_comment->name:'Anonymous';
                            $date_for_display = $each_comment->created_at->diffForHumans();
                            $parent_name_for_display = '';
                            if($each_comment->parent_id > 0){
                                $parent_comment = $each_comment->parent();
                                $parent_name_for_display = $parent_comment != null && $parent_comment->name
                                        ? $parent_comment->name : 'Anonymous';
                                $parent_name_for_display = '<span class="glyphicon glyphicon-share-alt" title="Reply to">&nbsp;</span>'.$parent_name_for_display;
                            }
                            $parents_count = substr_count($each_comment->parents, '.');
                            $offset_length = $parents_count;
                            $comment_length = 12 - $offset_length;
                            ?>
                            <div class="col-xs-offset-{!! $offset_length !!} col-xs-{!! $comment_length !!}">
                                <ul class="list-inline">
                                    <li class="comment-by">{!! $name_for_display !!}</li>
                                    @if($parents_count > 0)
                                        <li class="reply-to">{!! $parent_name_for_display !!}</li>
                                    @endif
                                    <li class="separator"></li>
                                    <li class="comment-on">{!! $date_for_display !!}</li>
                                </ul>

                                <p>{!! $each_comment->comment !!}</p>

                                <a href="javascript:void(0)" class="reply comment{!! $each_comment->id !!}" title="Reply to above comment">Reply</a>

                                <div class="reply-content reply{!! $each_comment->id !!}"></div>

                                <hr>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        {!! $comments->render() !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        Show <input type="text" name="comments_per_page" class="comments_per_page" value="{!! $per_page !!}" size="2" title="Number of comments per page"> comments per page
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            @include('partials/post_sidebar')
        </div>
    </div>
@stop