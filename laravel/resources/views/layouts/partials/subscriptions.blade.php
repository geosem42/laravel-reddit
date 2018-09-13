@if($subPlebbit)
<script>
    $('.subscribe').click(function() {
        subscriptions = $('.subscriptions');

        _this = $(this);
        var subscribed = _this.attr('data-subscribed');

        @if(Auth::check())
            data = {'api_token': '{{Auth::user()->api_token}}'};

            plebbit = '{{$subPlebbit->name}}';
            if (subscribed === 'no') {
                $.post( "/api/subscribe/" + plebbit, data, function( res ) {
                    _this.removeClass('notsubscribed').addClass('subscribed').attr('data-subscribed', 'yes').text('Unsubscribe');
                    subscriptions.append('<a href="/p/'+ res.sub_plebbit +'">'+ res.sub_plebbit +'</a>');
                });
            } else {
            $.post( "/api/unsubscribe/" + plebbit, data, function( res ) {
                    _this.removeClass('subscribed').addClass('notsubscribed').attr('data-subscribed', 'no').text('Subscribe');
                    $('.sub').each(function() {
                        if ($(this).text() === res.sub_plebbit) {
                            $(this).remove();
                        }
                    });
                });
            }
        @else
            $('#loginModal').modal('show');
            $('#loginModalMessage').html('to subscribe to <a href="/p/{{$subPlebbit->name}}">/p/{{$subPlebbit->name}}</a>');
        @endif
    });
</script>
@endif