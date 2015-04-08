@extends('app')

@section('content')
<script src="{{ asset('/admin_panel/js/socialParser.js') }}"></script>
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading panel-error">List of parsed news</div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>URI</th>
                                <th>Viewed</th>
                                <th>Archived</th>
                                <th>Dates</th>
                                <th colspan="2">Author</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($news as $topic)
                            <tr>
                                <th scope="row">{{ $topic->id }}</th>
                                <td>{{ $topic->title }}</td>
                                <td>{{ $topic->description }}</td>
                                <td>{{ $topic->uri }}</td>
                                <td>{{ $topic->is_viewed }}</td>
                                <td>{{ $topic->is_archived }}</td>
                                <td>
                                    <nobr>Created at: {{ $topic->created_at }}</nobr>
                                    <nobr>Updated at: {{ $topic->updated_at }}</nobr>
                                    <nobr>Viewed at: {{ $topic->viewed_at }}</nobr>
                                </td>
                                <td>{{ $topic->user->name }}</td>
                                <td>
                                    <button type="button" class="btn btn-success btn-xs" data-toggle="modal"
                                        data-target="#newsModal" data-news-id="{{ $topic->id }}">Read</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection