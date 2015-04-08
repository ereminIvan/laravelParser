@extends('app')

@section('content')
    @if($news)
        <dl class="dl-horizontal">
            <dt>Title</dt>
            <dd>{{ $news->title }}</dd>
            <dt>Description</dt>
            <dd>{{ $news->description }}</dd>
            <dt>Text</dt>
            <dd>{{ $news->text }}</dd>
            <dt>URI</dt>
            <dd>{{ $news->uri }}</dd>
            <dt>Viewed</dt>
            <dd>{{ $news->is_viewed }}</dd>
            <dt>Archived</dt>
            <dd>{{ $news->is_archived }}</dd>
            <dt>Created at</dt>
            <dd>{{ $news->created_at }}</dd>
            <dt>Updated at</dt>
            <dd>{{ $news->updated_at }}</dd>
            <dt>Updated at</dt>
            <dd>{{ $news->updated_at }}</dd>
            <dt>Viewed at</dt>
            <dd>{{ $news->updated_at }}</dd>
            <dt>Viewed by</dt>
            <dd>{{ $news->user->name }}</dd>
        </dl>
    @else
        News not found
    @endif
@endsection