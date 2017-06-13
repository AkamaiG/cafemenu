@extends('welcome')

@section('content')
    <center><h1>Cafe panel (Reviews list)</h1>
        <table class="table">
            <thead>
            <tr>
                <th>Review ID</th>
                <th>Description</th>
                <th>Review</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($reviewsinfo as $rewi)
                <tr>
                    <td><span class="label label-info">{{ $rewi->id }}</span></td>
                    <td>{{ $rewi->desc }}</td>
                    <td><span class="label label-info">{{ $rewi->review }}</span></td>
                    <td><span class="label label-info">{{ $rewi->created_at }}</span></td>
                    <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#updateModal">Delete</button></td>
                </tr>
            @endforeach
            </tbody>
        </table></center>

@endsection