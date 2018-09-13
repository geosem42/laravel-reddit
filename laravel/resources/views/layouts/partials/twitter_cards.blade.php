@section('meta')
    @if(isset($thread) && $thread->media_type == 'image')
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="@if(isset($thread)){{$thread->link}}@endif" />
    @else
        <meta name="twitter:card" content="summary" />
        <meta name="twitter:image" content="{{url('/')}}/images/logo.png">
    @endif
    <meta property="twitter:site" content="@plebbit">
    <meta name="twitter:description" content="@if(isset($subPlebbit->description_social) && !empty($subPlebbit->description_social)){{$subPlebbit->description_social}}@else The only place where stealing memes is legal @endif" />
    <meta name="description" content="@if(isset($subPlebbit->description_social) && !empty($subPlebbit->description_social)){{$subPlebbit->description_social}}@else Plebbit is the #1 platform controlled by the users. Which also makes it the best freedom of speech platform! So Be warned redditors. Plebbit is on the rise!@endif" />

    @if(isset($thread->title) && !empty($thread->title))
        <meta property="twitter:title" content="@php echo substr($thread->title, 0, 47); @endphp @if(strlen($thread->title > 47))...@endif • /p/@if(isset($subPlebbit)){{$subPlebbit->name}}@endif">
    @elseif(isset($subPlebbit->name) && !empty($subPlebbit->name))
        <meta property="twitter:title" content="Plebbit • /p/@if(isset($subPlebbit)){{$subPlebbit->name}}@endif">
    @elseif(isset($twitter_title) && !empty($twitter_title))
        <meta property="twitter:title" content="Plebbit • {{$twitter_title}}">
    @else
        <meta property="twitter:title" content="Plebbit • Post your stolen memes here">
    @endif
@endsection