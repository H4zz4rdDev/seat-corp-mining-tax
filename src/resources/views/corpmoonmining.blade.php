@extends('web::layouts.grids.8-4')

@section('title', 'Moonmining')

@section('left')
    @isset($data)
        <div class="card">
            <div class="card-header">
                <h3>Corporation Moon Mining</h3>
            </div>
            <div class="card-body">
                <table class="table" id="mining">
                    <thead>
                    <tr>
                        <th>CharacterName</th>
                        <th>Corporation</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $d)
                        <tr>
                            <td>{{ $d }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endisset
@stop