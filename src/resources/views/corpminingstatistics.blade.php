@extends('web::layouts.grids.12')

@section('title', trans('corpminingtax::global.browser_title'))

@push('head')
    <link rel="stylesheet" type="text/css" href="{{ asset('web/css/corpminingtax.css') }}"/>
@endpush

@section('full')
    <div class="row">
        <div class="col-md-4 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-yellow elevation-1"><i class="fa fa-dice-d20"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Mined Quantity</span>
                    <span class="info-box-number">
                        {{ number_format($total_quantity) }} <small>units</small>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-red elevation-1"><i class="fa fa-gem"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Mined Volume</span>
                    <span class="info-box-number">
                        {{ number_format($total_volume) }} <small>m³</small>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-green elevation-1"><i class="fa fa-coins"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Mined ISK</span>
                    <span class="info-box-number">
                        {{ number_format($total_price) }} <small>ISK</small>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-yellow elevation-1"><i class="fa fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Incoming Tax ISK</span>
                    <span class="info-box-number">
                        {{ number_format($total_tax) }} <small>ISK</small>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-blue elevation-1"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Mining Members</span>
                    <span class="info-box-number">
                        {{ count($total_members) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-green elevation-1"><i class="fa fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Incoming from Event's</span>
                    <span class="info-box-number">
                        {{ number_format($total_event_price) }} <small>ISK</small>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Corporation Moon Minings<small>(last 12 month's)</small></h3>
                </div>
                <div class="card-body">
                    <div style="height: 350px">
                        <canvas id="moon_mining_chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top 5 Miner's <small>(last month)</small></h3>
                </div>
                <div class="card-body">
                    <table class="table" id="mining_report">
                        <thead>
                        <tr>
                            <td>Name</td>
                            <td>Mined Quantity (units)</td>
                            <td>Mined Volume (m3)</td>
                            <td>Mineral Price (isk)</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($top_ten_miners_last as $miner)
                            <tr>
                                <td>{{ $miner->name }}</td>
                                <td>{{ number_format($miner->quantity) }}</td>
                                <td>{{ number_format($miner->volume) }}</td>
                                <td>{{ number_format($miner->price) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
@push('javascript')
    <script type="text/javascript">
        $(document).ready(function () {

        const data = {
            labels: @json($moon_mining->date),
            datasets: [
                {
                    label: 'Quantity',
                    data: @json($moon_mining->quantity),
                    backgroundColor: '#acc239',
                    borderColor: '#acc239',
                    borderWidth: 1
                },
            ]
        };
        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }, },
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            },
        };

        new Chart(document.getElementById('moon_mining_chart').getContext('2d'), config);
        });
    </script>
@endpush