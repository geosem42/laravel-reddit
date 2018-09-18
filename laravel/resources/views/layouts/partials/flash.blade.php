@foreach ((array) session('flash_notification') as $message)
    @php $message = (array)$message[0]; @endphp
    @if ($message['overlay'])
        @include('flash::modal', [
            'modalClass' => 'flash-modal',
            'title'      => $message['title'],
            'body'       => $message['message']
        ])
    @else
            <div style="margin-bottom: 0;" class="alert alert-{{ $message['level'] }}
            {{ $message['important'] ? 'alert-important' : '' }}"
                 role="alert"
            >
                <div class="container">
                @if ($message['important'])
                    <button type="button"
                            class="close"
                            data-dismiss="alert"
                            aria-hidden="true"
                    >&times;</button>
                @endif

                {!! $message['message'] !!}
                </div>
            </div>
    @endif
@endforeach

