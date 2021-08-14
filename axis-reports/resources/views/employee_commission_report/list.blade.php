@extends('layouts.default')

@section('content')

    <h4 class="m-b-20">Employee Commission Report</h4>
    <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" data-url="{{route('employee_commission_report.export','xlsx')}}" class="export_button btn btn-info">
            Excel
        </button>
        <button type="button" data-url="{{route('employee_commission_report.export','pdf')}}" class="export_button btn btn-info">
            PDF
        </button>
    </div>

    <div class="row">
        <div class="col-xs-12">
            {!! Form::open(['route' => 'employee_commission_report.list','method' => 'get']) !!}
            <div class="row col-md-4">
                <div class="form-group m-r-20">
                    {!! Form::label('invoice_no', 'Invoice No') !!}
                    {!! Form::text('invoice_no', null, ['class' => 'form-control']) !!}
                </div>

                <div class="form-group m-r-20">
                    {!! Form::label('service', 'Service') !!}
                    {!!Form::select('service', $items, null,['class' => 'form-control']) !!}
                </div>


                {!! Form::submit('Submit', ['class' => 'btn btn-info']) !!}

            </div>
            <div class="row col-md-4">
                <div class="form-group m-r-20">
                    {!! Form::label('tran_date_from', 'Date From') !!}
                    {!! Form::text('tran_date_from', setValue(old('tran_date_from'),today()), ['autocomplete' => 'off','class' => 'form-control date-picker']) !!}
                </div>
                <div class="form-group m-r-20">
                    {!! Form::label('tran_date_to', 'Date To') !!}
                    {!! Form::text('tran_date_to', setValue(old('tran_date_to'),today()), ['autocomplete' => 'off','class' => 'form-control date-picker']) !!}
                </div>


            </div>
            <div class="row col-md-4">
                <div class="form-group m-r-20">
                    {!! Form::label('employee', 'Employee') !!}
                    {!!Form::select('employee', $users, null,['class' => 'form-control']) !!}
                </div>

                <div class="form-group m-r-20">
                    {!! Form::label('customer', 'Customer') !!}
                    {!!Form::select('customer', $customers, null,['class' => 'form-control']) !!}
                </div>

            </div>


            {!! Form::close() !!}
        </div>
    </div>

    <div class="table-responsive">
        <table class="axis-table table table-hover table-striped table-bordered" cellspacing="0">
            <thead>
            <tr>
                <th style="width: 180px">Invoice No</th>
                <th style="width: 180px">Date</th>
                <th style="width: 180px">Customer</th>
                <th style="width: 180px">Reference Customer</th>
                <th style="width: 180px">Service</th>
                <th style="width: 180px">Employee</th>
                <th style="width: 180px">Unit Commission</th>
                <th style="width: 180px">Quantity</th>
                <th style="width: 180px">Total Commission</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($result as $row)
                <tr>
                    <td>{{$row->invoice_no}}</td>
                    <td>{{sql2date($row->transaction_date)}}</td>
                    <td>{{$row->customer_name}}</td>
                    <td>{{$row->reference_customer}}</td>
                    <td>{{$row->description}}</td>
                    <td>{{$row->created_employee}}</td>
                    <td>{{$row->user_commission}}</td>
                    <td>{{$row->quantity}}</td>
                    <td>{{$row->total_commission}}</td>
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







