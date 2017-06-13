@extends('welcome')

@section('content')
    <center><h1>Cafe panel (Your ID:{{ session()->get('userid') }})</h1>
        @foreach ($userinfo as $uinf)
            <p>This is user {{ $uinf->id }}</p>
        @endforeach
    <table class="table">
        <thead>
        <tr>
            <th>Cafe ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Rating</th>
            <th>Work</th>
            <th>lat cord</th>
            <th>long cord</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($cafeinfo as $cfi)
        <tr>
            <td><span class="label label-info">{{ $cfi->id }}</span></td>
            <td>{{ $cfi->name }}</td>
            <td>{{ $cfi->desc }}</td>
            <td><span class="label label-info">{{ $cfi->rating }}</span></td>
            <td><span class="label label-info">{{ $cfi->work }}</span></td>
            <td><span class="label label-warning">{{ $cfi->lat }}</span></td>
            <td><span class="label label-warning">{{ $cfi->long }}</span></td>
            <td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updateModal">Update</button></td>
        </tr>
        @endforeach
        </tbody>
    </table></center>

    <!-- Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit cafe</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @foreach($cafeinfo as $cfi)
                    <div class="form-group">
                        <label class="control-label" for="focusedInput">Name</label>
                        <input class="form-control" id="focusedInput" type="text" value="{{ $cfi->name }}">
                    </div>

                        <div class="form-group">
                            <label class="control-label" for="focusedInput">Work</label>
                            <input class="form-control" id="focusedInput" type="text" value="{{ $cfi->desc }}">
                        </div>

                    <div class="form-group">
                        <label class="control-label" for="focusedInput">Work</label>
                        <input class="form-control" id="focusedInput" type="text" value="{{ $cfi->work }}">
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="focusedInput">Lat cord</label>
                        <input class="form-control" id="focusedInput" type="text" value="{{ $cfi->lat }}">
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="focusedInput">Long cord</label>
                        <input class="form-control" id="focusedInput" type="text" value="{{ $cfi->long }}">
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

@endsection