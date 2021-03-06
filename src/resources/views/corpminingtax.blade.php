@extends('web::layouts.grids.8-4')

@section('title', trans('corpminingtax::global.browser_title'))

@push('head')
    <link rel="stylesheet" type="text/css" href="{{ asset('web/css/corpminingtax.css') }}"/>
@endpush

@section('left')
<div class="card">
    <div class="card-header">
        <h3>Settings</h3>
    </div>
    <div class="card-body">
        <div id="overlay" style="border-radius: 5px">
            <div class="w-100 d-flex justify-content-center align-items-center">
                <div class="spinner">
                </div>
            </div>
        </div>
        <form action="{{ route('corpminingtax.data') }}" method="post" id="miningDate" name="miningDate">
            {{ csrf_field() }}
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="mining_month">Month</label>
                    <select class="custom-select mr-sm-2" name="mining_month" id="mining_month">
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5" selected>May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="mining_year">Year</label>
                    <select class="custom-select mr-sm-2" name="mining_year" id="mining_year">
                        <option value="2018">2018</option>
                        <option value="2019">2019</option>
                        <option value="2020">2020</option>
                        <option value="2021">2021</option>
                        <option value="2022" selected>2022</option>
                        <option value="2023">2023</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="corpId">Corporation</label>
                    <select class="groupSearch form-control input-xs" name="corpId" id="corpId"></select>
                </div>
            </div>
            <button class="btn btn-primary" onclick="on()" type="submit">Send</button>
        </form>
    </div>
</div>
@isset($miningData)
<div class="card">
    <div class="card-header">
        <h3>Tax Summary - {{ ($miningData->month < 10) ? "0" . $miningData->month : $miningData->month }}/{{ $miningData->year }}</h3>
    </div>
    <div class="card-body">
        <table class="table" id="mining">
            <thead>
            <tr>
                <th>CharacterName</th>
                <th>Mined Amount</th>
                <th>ISK to Pay</th>
                <th>Percentage</th>
            </tr>
            </thead>
            <tbody>
            @foreach($miningData->characterData as $character)
                <tr>
                    <td>{{ $character->characterName }}</td>
                    <td>{{ $character->priceSummary }}</td>
                    <td>{{ $character->tax }}</td>
                    <td>10%</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endisset
@stop

@push('javascript')
    @push('javascript')
        <script>
            function on() {
                document.getElementById("overlay").style.display = "flex";
            }
        </script>
    @endpush
    <script>
        table = $('#mining').DataTable({
        });

        $('#corpId').select2({
            placeholder: 'Corporation Name',
            ajax: {
                url: '/corpminingtax/getCorporations',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });

    </script>
@endpush