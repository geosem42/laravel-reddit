@if($subLolhow)
<script>
    $('.subscribe').click(function() {
        subscriptions = $('.subscriptions');

        _this = $(this);
        var subscribed = _this.attr('data-subscribed');

        @if(Auth::check())
            data = {'api_token': '{{Auth::user()->api_token}}'};

            lolhow = '{{$subLolhow->name}}';
            if (subscribed === 'no') {
                $.post( "/api/subscribe/" + lolhow, data, function( res ) {
                    _this.removeClass('notsubscribed').addClass('subscribed').attr('data-subscribed', 'yes').text('Unsubscribe');
                    subscriptions.append('<a href="/p/'+ res.sub_lolhow +'">'+ res.sub_lolhow +'</a>');
                });
            } else {
            $.post( "/api/unsubscribe/" + lolhow, data, function( res ) {
                    _this.removeClass('subscribed').addClass('notsubscribed').attr('data-subscribed', 'no').text('Subscribe');
                    $('.sub').each(function() {
                        if ($(this).text() === res.sub_lolhow) {
                            $(this).remove();
                        }
                    });
                });
            }
        @else
            $('#loginModal').modal('show');
            $('#loginModalMessage').html('to subscribe to <a href="/p/{{$subLolhow->name}}">/p/{{$subLolhow->name}}</a>');
        @endif
    });
</script>
@endif