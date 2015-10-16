<div class="container">
    <div class="post-comments">

        {!! Form::open(['route' => ['comment', $post]]) !!}
            <div class="form-group">
                <label for="comment">Your Comment</label>
                <textarea name="comment" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-default">Send</button>
        {!! Form::close() !!}

        <div class="comments-nav">
            <ul class="nav nav-pills">
                <li role="presentation" class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        there are {{ count($comments) }} comments <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Best</a></li>
                        <li><a href="#">Hot</a></li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="row">

            <div class="media">
                <!-- first comment -->
                @foreach($comments as $comment)
                <div class="media-heading">
                    <button class="btn btn-default btn-xs" type="button" data-toggle="collapse" data-target="#{{ $comment->id }}" aria-expanded="false" aria-controls="collapseExample"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button> <span class="label label-info">12314</span> {{ $comment->user->name }} 12 hours ago
                </div>

                <div class="panel-collapse collapse in" id="{{ $comment->id }}">

                    <div class="media-left">
                        <div class="vote-wrap">
                            <div class="vote up">
                                <i class="glyphicon glyphicon-menu-up"></i>
                            </div>
                            <div class="vote inactive">
                                <i class="glyphicon glyphicon-menu-down"></i>
                            </div>
                        </div>
                        <!-- vote-wrap -->
                    </div>
                    <!-- media-left -->


                    <div class="media-body">
                        <p>{{ $comment->body }}</p>
                        <div class="comment-meta">
                            <span><a href="#">delete</a></span>
                            <span><a href="#">report</a></span>
                            <span><a href="#">hide</a></span>
              <span>
                        <a class="" role="button" data-toggle="collapse" href="#replyComment-{{ $comment->id }}" aria-expanded="false" aria-controls="collapseExample">reply</a>
                      </span>
                            <div class="collapse" id="replyComment-{{ $comment->id }}">

                                {!! Form::open(['route' => ['child-comment', $comment]]) !!}

                                <input type="hidden" name="parent_id" value="{{ $comment->id }}"/>

                                <div class="form-group">

                                    <label for="comment">Your Comment</label>

                                    <textarea name="child-comment" class="form-control" rows="3"></textarea>

                                </div>

                                <button type="submit" class="btn btn-default">Send</button>

                                {!! Form::close() !!}

                            </div>
                        </div>
                        <!-- comment-meta -->

                        <div class="media">
                            <!-- answer to the first comment -->

                            <div class="media-heading">
                                <button class="btn btn-default btn-collapse btn-xs" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseExample"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button> <span class="label label-info">12314</span> vertu 12 sat once yazmis
                            </div>

                            <div class="panel-collapse collapse in" id="collapseTwo">

                                <div class="media-left">
                                    <div class="vote-wrap">
                                        <div class="vote up">
                                            <i class="glyphicon glyphicon-menu-up"></i>
                                        </div>
                                        <div class="vote inactive">
                                            <i class="glyphicon glyphicon-menu-down"></i>
                                        </div>
                                    </div>
                                    <!-- vote-wrap -->
                                </div>
                                <!-- media-left -->


                                <div class="media-body">
                                    @if($comment->parent_id)
                                        @foreach($comment->parent_id as $child)
                                            <p>child comment here</p>
                                        @endforeach
                                    @else
                                        <p>nothing here</p>
                                    @endif
                                    <div class="comment-meta">
                                        <span><a href="#">delete</a></span>
                                        <span><a href="#">report</a></span>
                                        <span><a href="#">hide</a></span>
                            <span>
                              <a class="" role="button" data-toggle="collapse" href="#replyCommentThree" aria-expanded="false" aria-controls="collapseExample">reply</a>
                            </span>
                                        <div class="collapse" id="replyCommentThree">
                                            <form>
                                                <div class="form-group">
                                                    <label for="comment">Your Comment</label>
                                                    <textarea name="comment" class="form-control" rows="3"></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-default">Send</button>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- comment-meta -->
                                </div>
                            </div>
                            <!-- comments -->

                        </div>
                        <!-- answer to the first comment -->

                    </div>
                </div>
                <!-- comments -->
                @endforeach
            </div>
            <!-- first comment -->

        </div>

    </div>
    <!-- post-comments -->
</div>