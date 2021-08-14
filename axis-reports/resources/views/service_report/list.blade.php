

<?php


 ?>

@extends('layouts.default')

@section('content')

    <h4 class="m-b-20">Service Report</h4>
    <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" data-url="{{route('service_report.export','xlsx')}}" class="export_button btn btn-info">Excel</button>
        <button type="button" data-url="{{route('service_report.export','pdf')}}" class="export_button btn btn-info">PDF</button>
    </div>

    <div class="row">
        <div class="col-xs-12">
            {!! Form::open(['route' => 'service_report.list','method' => 'get']) !!}
            <div class="row col-md-4">
                <div class="form-group m-r-20">
                    {!! Form::label('invoice_no', 'Invoice No') !!}
                    {!! Form::text('invoice_no', null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group m-r-20">
                    {!! Form::label('customer', 'Customer') !!}

                    {{--                    {!!Form::select('customer[]', $customers, [24,25],['class' => 'form-control','multiple'=>'multiple']) !!}--}}

                    <select name="customer[]" class="form-control" multiple>
                        @foreach($customers as $key => $val)
                            <option <?php if (in_array($key, isset($_GET['customer']) ? $_GET['customer'] : [])) {
                                echo "selected";
                            } ?> value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>


                </div>

                <div class="form-group m-r-20">
                    {!! Form::label('category', 'Category') !!}
                    {!!Form::select('category', $categories, null,['class' => 'form-control']) !!}
                </div>


                <div class="form-group m-r-20">
                    {!! Form::label('sales_man_id', 'Sales Man') !!}
                    {!!Form::select('sales_man_id', $sales_men, null,['class' => 'form-control']) !!}
                </div>

                {!! Form::submit('Submit', ['class' => 'btn btn-info','name' => 'submit']) !!}

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

                <div class="form-group m-r-20">
                    {!! Form::label('service', 'Service') !!}
                    {!!Form::select('service', $items, null,['class' => 'form-control']) !!}
                </div>


                <div class="form-group m-r-20">
                    {!! Form::label('display_customer', 'Display Customer') !!}
                    {!! Form::text('display_customer', null, ['class' => 'form-control']) !!}
                </div>

            </div>
            <div class="row col-md-4">
                <div class="form-group m-r-20">
                    {!! Form::label('employee', 'Employee') !!}
                    {!!Form::select('employee', $users, null,['class' => 'form-control']) !!}
                </div>
                <div class="form-group m-r-20">
                    {!! Form::label('payment_status', 'Payment Status') !!}
                    {!!Form::select('payment_status', payment_status_list(), null,['class' => 'form-control']) !!}
                </div>

                <div class="form-group m-r-20">
                    {!! Form::label('transaction_id', 'Transaction ID') !!}
                    {!! Form::text('transaction_id', null, ['class' => 'form-control']) !!}
                </div>


                <div class="form-group m-r-20">
                    {!! Form::label('work_location', 'Work Location') !!}
                    {!!Form::select('work_location', work_location_list(), null,['class' => 'form-control']) !!}
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
                <th style="width: 180px">Sales Man</th>
                <th style="width: 180px">Service</th>
                <th style="width: 180px">Category</th>
                <th style="width: 180px">Unit Price</th>
                <th style="width: 180px">Quantity</th>
                <th style="width: 180px">Total Service Charge</th>
                <th style="width: 180px">Unit Tax</th>
                <th style="width: 180px">Total Tax</th>
                <th style="width: 180px">Discount Amount</th>
                <th style="width: 180px">Govt. Fee</th>
                <th style="width: 180px">Total Govt. Fee</th>
                <th style="width: 180px">Bank Charge</th>
                <th style="width: 180px">VAT(Bank Charge)</th>
                <th style="width: 180px">PF.Amount</th>
                <th style="width: 180px">Customer Commission</th>
                <th style="width: 180px">Reward Amount</th>
                <th style="width: 180px">Employee Commission</th>
                <th style="width: 180px">Transaction ID</th>
                <th style="width: 180px">REF NAME</th>
                <th style="width: 180px">APPLICATION ID</th>
                <th style="width: 180px">REF CUSTOMER</th>
                <th style="width: 180px">Work Location</th>
                <th style="width: 180px">Employee</th>
                <th style="width: 180px">Payment Status</th>
                {{--<th style="width: 180px">Payment Method</th>--}}
                <th style="width: 180px">Net Service Amount</th>
                <th style="width: 180px">Invoice Amount</th>
            </tr>
            </thead>
            <tbody>




            @foreach ($result as $row)

                <?php

                        if(empty($row->ref_id))
                            $row->ref_id=$row->ref_name;

                        if(!empty($row->ed_transaction_id))
                            $row->transaction_id=$row->ed_transaction_id;

                ?>

                <tr>
                    <td>{{$row->invoice_no}}</td>
                    <td>{{sql2date($row->transaction_date)}}</td>
                    <td>{{$row->customer_name}}</td>
                    <td>{{$row->reference_customer}}</td>
                    <td>{{$row->salesman_name}}</td>
                    <td>{{$row->description}}</td>
                    <td>{{$row->category_name}}</td>
                    <td>{{$row->unit_price}}</td>
                    <td>{{$row->quantity}}</td>
                    <td>{{$row->total_price}}</td>
                    <td>{{$row->unit_tax}}</td>
                    <td>{{$row->total_tax}}</td>
                    <td>{{$row->discount_amount}}</td>
                    <td>{{$row->govt_fee}}</td>
                    <td>{{$row->total_govt_fee}}</td>
                    <td>{{$row->bank_service_charge}}</td>
                    <td>{{$row->bank_service_charge_vat}}</td>
                    <td>{{$row->pf_amount}}</td>
                    <td>{{$row->total_customer_commission}}</td>
                    <td>{{$row->reward_amount}}</td>
                    <td>{{$row->user_commission}}</td>
                    <td>{{$row->transaction_id}}</td>
                    <td>{{$row->application_id}}</td>
                    <td>{{$row->ref_id}}</td>
                    <td>{{$row->customer_ref}}</td>
                    <td>{{$row->work_location}}</td>
                    <td>{{$row->created_employee}}</td>
                    <td>{{payment_status($row->payment_status)}}</td>
{{--                    <td>{{$row->payment_method}}</td>--}}
                    <td>{{$row->net_service_charge}}</td>
                    <td>{{$row->invoice_amount}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{--{{ $result->links() }}--}}

        <?php


        echo paginate($total_rows);

        ?>

        {{--<div>--}}
            {{--Showing {{ $result->firstItem() }} to {{ $result->lastItem() }} of {{$result->total()}} records--}}
        {{--</div>--}}

    </div>

@stop

@section("script")
    {{--JS here--}}
@stop







