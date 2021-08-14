@extends('layouts.default')

@section('content')

    <h4 class="m-b-20">Service List</h4>
    <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" data-url="{{route('service_list.export','xlsx')}}" class="export_button btn btn-info">
            Excel
        </button>
        <button type="button" data-url="{{route('service_list.export','pdf')}}" class="export_button btn btn-info">
            PDF
        </button>
    </div>

    <div class="row">
        <div class="col-xs-12">
            {!! Form::open(['route' => 'service_list.list','method' => 'get']) !!}
            <div class="row col-md-4">

                <div class="form-group m-r-20">
                    {!! Form::label('category_id', 'Category') !!}
                    {!!Form::select('category_id', $categories, null,['class' => 'form-control']) !!}
                </div>

                {!! Form::submit('Submit', ['class' => 'btn btn-info']) !!}

            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <div class="table-responsive">
        <table class="axis-table table table-hover table-striped table-bordered" cellspacing="0">
            <thead>
            <tr>
                <th style="width: 180px">Stock ID</th>
                <th style="width: 180px">Service Name</th>
                <th style="width: 180px">Service Name (Arabic)</th>
                <th style="width: 180px">Category</th>
                <th style="width: 180px">Service Charge</th>
                <th style="width: 180px">Govt. Fee</th>
                <th style="width: 180px">Amer Charge</th>
                <th style="width: 180px">Bank Charge</th>
                <th style="width: 180px">VAT(Bank Charge)</th>
                <th style="width: 180px">Local User Commission</th>
                <th style="width: 180px">Non-Local User Commission</th>
                <th style="width: 180px">TAX</th>
                <th style="width: 180px">Total Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($result as $row)
                <tr>
                    <td>{{$row->stock_id}}</td>
                    <td>{{$row->item_description}}</td>
                    <td>{{$row->long_description}}</td>
                    <td>{{$row->category_name}}</td>
                    <td>{{number_format($row->service_charge,2)}}</td>
                    <td>{{number_format($row->govt_fee,2)}}</td>
                    <td>{{number_format($row->pf_amount,2)}}</td>
                    <td>{{number_format($row->bank_service_charge,2)}}</td>
                    <td>{{number_format($row->bank_service_charge_vat,2)}}</td>
                    <td>{{number_format($row->commission_loc_user,2)}}</td>
                    <td>{{number_format($row->commission_non_loc_user,2)}}</td>
                    <td>{{number_format($row->tax,2)}}</td>
                    <td>{{number_format($row->tax+$row->total_amount,2)}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <?php


        echo paginate($total_rows);

        ?>

    </div>

@stop

@section("script")
    {{--JS here--}}
@stop







