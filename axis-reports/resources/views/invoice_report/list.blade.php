@extends('layouts.default')

@section('content')

    <h4 class="m-b-20">Invoice List</h4>
    <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" data-url="{{route('invoice_report.export','xlsx')}}" class="export_button btn btn-info">Excel</button>
        <button type="button" data-url="{{route('invoice_report.export','pdf')}}" class="export_button btn btn-info">PDF</button>
    </div>

    <div class="row">
        <div class="col-xs-12">
            {!! Form::open(['route' => 'invoice_report.list','method' => 'get']) !!}
            <div class="row col-md-4">
                <div class="form-group m-r-20">
                    {!! Form::label('invoice_no', 'Invoice No') !!}
                    {!! Form::text('invoice_no', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group m-r-20">
                    {!! Form::label('customer', 'Customer') !!}
{{--                    {!!Form::select('customer[]', $customers, null,['class' => 'form-control','multiple'=>'']) !!}--}}


                    <select name="customer[]" class="form-control" multiple>
                        @foreach($customers as $key => $val)
                            <option <?php if (in_array($key, isset($_GET['customer']) ? $_GET['customer'] : [])) {
                                echo "selected";
                            } ?> value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>


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
                    {!! Form::label('payment_status', 'Payment Status') !!}
                    {!!Form::select('payment_status', payment_status_all_list(), null,['class' => 'form-control']) !!}
                </div>

                <div class="form-group m-r-20">
                    {!! Form::label('customer_ref', 'Customer Ref') !!}
                    {!! Form::text('customer_ref', null, ['class' => 'form-control']) !!}
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
                <th style="width: 180px">Display Customer</th>
                <th style="width: 180px">Reference Customer</th>
                <th style="width: 180px">Employee</th>
                <th style="width: 180px">Payment Status</th>
                <th style="width: 180px">Invoice Amount</th>
                <th style="width: 180px">Allocated Amount</th>
                <th style="width: 180px">Credit Note Amount</th>
            </tr>
            </thead>
            <tbody>



            @foreach ($result as $row)

                <?php

                $link = "PARAM_0=".$row->trans_no."-10&PARAM_1=".
                    $row->trans_no."-10&PARAM_2=&PARAM_3=0&PARAM_4=&PARAM_5=&PARAM_6=&PARAM_7=0&REP_ID=107";

                $link = "../../invoice_print?".$link;
                ?>

                <tr>
                    <td><a href="{{$link}}" target="_blank" style="text-decoration: underline !important;">{{$row->invoice_no}}</a></td>

                    <td>{{sql2date($row->transaction_date)}}</td>
                    <td>{{$row->customer_name}}</td>
                    <td>{{$row->reference_customer}}</td>
                    <td>{{$row->customer_ref}}</td>
                    <td>{{$row->created_employee}}</td>
                    <td>{{payment_status($row->payment_status)}}</td>
                    <td>{{$row->invoice_amount}}</td>
                    <td>{{$row->alloc}}</td>
{{--                    <td>{{$row->creditNoteAmntSum}}</td>--}}
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







