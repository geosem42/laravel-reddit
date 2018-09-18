<script>
    $('.vote').click(function() {
        _this = $(this);
        var vote = _this.attr('data-vote');
        var code = _this.attr('data-thread');


        @if(Auth::check())

        data = {'vote': vote, 'type': 'thread', 'api_token': '{{Auth::user()->api_token}}'};

        if (_this.attr('data-voted') === 'yes') {
            return; // maybe at remove vote later
        }

        $.post( "{{url('/')}}/api/vote/" + code, data, function( res ) {
            if (vote === 'up') {
                $('#' + res.post + '_up').css('color', '#4CAF50').attr('data-voted', 'yes');
                $('#' + res.post + '_down').css('color', '#636b6f').attr('data-voted', 'no');
                $('#' + res.post + '_counter').text(res.votes);
            } else {
                $('#' + res.post + '_up').css('color', '#636b6f').attr('data-voted', 'no');
                $('#' + res.post + '_down').css('color', '#F44336').attr('data-voted', 'yes');
                $('#' + res.post + '_counter').text(res.votes);
            }
        });
        @else
        $('#loginModal').modal('show');
        if (vote === 'down') {
            $('#loginModalMessage').text('to downvote');
        } else {
            $('#loginModalMessage').text('to upvote');
        }
        @endif
    });

    @if(Auth::check() && isset($userVotes) && $userVotes)
        @foreach($userVotes as $vote)
            @if($vote->vote == 1)
            $('#{{$vote->thread_id}}_up').css('color', '#4CAF50').attr('data-voted', 'yes');
            @else
            $('#{{$vote->thread_id}}_down').css('color', '#F44336').attr('data-voted', 'yes');
            @endif
        @endforeach
    @endif

    function votepost(id, type) {
        _this = $('#'+ id +'_' + type + '_post');
        var vote = _this.attr('data-vote');

        if (_this.attr('data-voted') === 'yes') {
            return; // maybe at remove vote later
        }

        @if(Auth::check())

            data = {'vote': vote, 'type': 'post', 'api_token': '{{Auth::user()->api_token}}'};

            $.post( "{{url('/')}}/api/vote/" + id, data, function( res ) {
                if (vote === 'up') {
                    $('#' + res.post + '_up_post').css('color', '#4CAF50').attr('data-voted', 'yes');
                    $('#' + res.post + '_down_post').css('color', '#636b6f').attr('data-voted', 'no');
                    $('#' + res.post + '_counter_post').text(res.votes);
                } else {
                    $('#' + res.post + '_up_post').css('color', '#636b6f').attr('data-voted', 'no');
                    $('#' + res.post + '_down_post').css('color', '#F44336').attr('data-voted', 'yes');
                    $('#' + res.post + '_counter_post').text(res.votes);
                }
            });
        @else
            $('#loginModal').modal('show');
            if (vote === 'down') {
                $('#loginModalMessage').text('to downvote');
            } else {
                $('#loginModalMessage').text('to upvote');
            }
        @endif
    }
</script>