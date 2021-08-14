@extends('layouts.default')

@section('content')

    <h4 class="m-b-20">Invoice Payment Report</h4>
    <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" data-url="{{route('invoice_payment_report.export','xlsx')}}"
                class="export_button btn btn-info">
            Excel
        </button>
        <button type="button" data-url="{{route('invoice_payment_report.export','pdf')}}"
                class="export_button btn btn-info">
            PDF
        </button>
    </div>

    <div class="row">
        <div class="col-xs-12">
            {!! Form::open(['route' => 'invoice_payment_report.list','method' => 'get']) !!}
            <div class="row col-md-4">


                <div class="form-group m-r-20">
                    {!! Form::label('customer', 'Customer') !!}
                    {!!Form::select('customer', $customers, null,['class' => 'form-control']) !!}
                </div>

                <div class="form-group m-r-20">
                    {!! Form::label('payment_method', 'Payment Method') !!}
                    {!!Form::select('payment_method', payment_methods_list(), null,['class' => 'form-control']) !!}
                </div>

                <div class="form-group m-r-20">
                    {!! Form::label('invoice_no', 'Invoice No') !!}
                    {!! Form::text('invoice_no', null, ['class' => 'form-control']) !!}
                </div>

                {!! Form::submit('Submit', ['class' => 'btn btn-info']) !!}

            </div>


            <div class="row col-md-4">
                <div class="form-group m-r-20">
                    {!! Form::label('tran_date_from', 'Date From') !!}
                    {!! Form::text('tran_date_from', setValue(old('tran_date_from'),today()), ['autocomplete' => 'off','class' => 'form-control date-picker']) !!}
                </div>
                <div class="form-group m-r-20">
                    {!! Form::label('bank', 'Collected Bank') !!}
                    {!!Form::select('bank', $banks, null,['class' => 'form-control']) !!}
                </div>

                <div class="form-group m-r-20">
                    {!! Form::label('receipt_no', 'Receipt No') !!}
                    {!! Form::text('receipt_no', null, ['class' => 'form-control']) !!}
                </div>

            </div>

            <div class="row col-md-4">

                <div class="form-group m-r-20">
                    {!! Form::label('tran_date_to', 'Date To') !!}
                    {!! Form::text('tran_date_to', setValue(old('tran_date_to'),today()), ['autocomplete' => 'off','class' => 'form-control date-picker']) !!}
                </div>

                <div class="form-group m-r-20">
                    {!! Form::label('user', 'User') !!}
                    {!!Form::select('user', $users, null,['class' => 'form-control']) !!}
                </div>


            </div>


            {!! Form::close() !!}

        </div>
    </div>

    <div class="table-responsive">
        <table class="axis-table table table-hover table-striped table-bordered" cellspacing="0">
            <thead>
            <tr>
                <th style="width: 180px">Date</th>
                <th style="width: 180px">Receipt No</th>
                <th style="width: 180px">Invoices</th>
                <th style="width: 180px">Sum of Invoices</th>
                <th style="width: 180px">Total Discount/Reward Point</th>
                <th style="width: 180px">Net Payment</th>
                <th style="width: 180px">Collected Bank</th>
                <th style="width: 180px">Customer</th>
                <th style="width: 180px">User</th>
                <th style="width: 180px">Payment Method</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($result as $row)
                <tr>
                    <td>{{sql2date($row->date_alloc)}}</td>
                    <td>{{$row->payment_ref}}</td>
                    <td>{{$row->invoice_numbers}}</td>
                    <td>{{$row->gross_payment}}</td>
                    <td>{{$row->reward_amount}}</td>
                    <td>{{$row->net_payment}}</td>
                    <td>{{$row->bank_account_name}}</td>
                    <td>{{$row->customer}}</td>
                    <td>{{$row->user_id}}</td>
                    <td>{{$row->payment_method}}</td>
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







