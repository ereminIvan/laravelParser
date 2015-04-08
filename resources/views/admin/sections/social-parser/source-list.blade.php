@extends('app')

@section('content')
<script src="{{ asset('/admin_panel/js/socialParser.js') }}"></script>
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading panel-error">
                    Parsing Source List
                </div>
                <!-- Modal -->
                <div class="modal fade" id="sourceModal" tabindex="-1" role="dialog"
                     aria-labelledby="addSourceModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                    aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="addSourceModalLabel">Source</h4>
                        </div>
                        <div class="modal-body">
                            <form class="form-horizontal">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <input type="hidden" name="sourceId" id="sourceId" />
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Type</label>
                                    <div class="col-sm-10">
                                        <div class="radio radio-inline">
                                            <label>
                                                <input type="radio" name="sourceType"
                                                       id="sourceType1" value="facebook" checked>
                                                Facebook
                                            </label>
                                        </div>
                                        <div class="radio radio-inline">
                                            <label>
                                                <input type="radio" name="sourceType" id="sourceType2" value="twitter">
                                                Twitter
                                            </label>
                                        </div>
                                        <div class="radio radio-inline">
                                            <label>
                                                <input type="radio" name="sourceType" id="sourceType3" value="rss">
                                                RSS
                                            </label>
                                        </div>
                                        <input type="hidden" id="sourceType" value="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="sourceUri" class="col-sm-2 control-label">URI</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="sourceUri" class="form-control"
                                               id="sourceUri" placeholder="Source Uri">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="sourceKeywords" class="col-sm-2 control-label">Keywords</label>
                                    <div class="col-sm-10">
                                        <textarea name="sourceKeywords" class="form-control"
                                                  id="sourceKeywords" placeholder="Source Keywords"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="sourceActive"> Set active
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="button" id="saveSource"
                                                class="btn btn-primary">Save changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                        </div>
                    </div>
                </div>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>URI</th>
                                <th>Keywords</th>
                                <th>Active</th>
                                <th>Created At</th>
                                <th>Executed At</th>
                                <th>Author</th>
                                <th><button type="button" class="addSource btn btn-success btn-xs"
                                            data-toggle="modal" data-source-id=""
                                            data-target="#sourceModal">Add</button></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($sources as $source)
                            <tr>
                                <th scope="row">{{ $source->id }}</th>
                                <td>{{ $source->type }}</td>
                                <td>{{ $source->uri }}</td>
                                <td>{{ $source->keywords }}</td>
                                <td>{{ $source->active }}</td>
                                <td>{{ $source->created_at }}</td>
                                <td>{{ $source->executed_at }}</td>
                                <td>{{ $source->user->name }}</td>
                                <td>
                                    <button type="button" class="editSource btn btn-success btn-xs"
                                            data-toggle="modal" data-source-id="{{ $source->id }}"
                                            data-target="#sourceModal">Edit</button>
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