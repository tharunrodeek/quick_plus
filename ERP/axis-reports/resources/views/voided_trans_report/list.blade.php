@extends('layouts.default')

@section('content')

    <h4 class="m-b-20">Voided Transaction Report</h4>
    <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" data-url="{{route('voided_trans_report.export','xlsx')}}" class="export_button btn btn-info">
            Excel
        </button>
        <button type="button" data-url="{{route('voided_trans_report.export','pdf')}}" class="export_button btn btn-info">
            PDF
        </button>
    </div>

    <div class="row">
        <div class="col-xs-12">
            {!! Form::open(['route' => 'voided_trans_report.list','method' => 'get']) !!}
            <div class="row col-md-4">

                <div class="form-group m-r-20">
                    {!! Form::label('transaction_done_by', 'Transaction Done By') !!}
                    {!!Form::select('transaction_done_by', $users, null,['class' => 'form-control']) !!}
                </div>


            </div>



            <div class="row col-md-4">

                <div class="form-group m-r-20">
                    {!! Form::label('voided_by', 'Voided By') !!}
                    {!!Form::select('voided_by', $users, null,['class' => 'form-control']) !!}
                </div>



            </div>


            <div class="row col-md-4">


                <div class="form-group m-r-20">
                    {!! Form::label('void_type', 'Void Type') !!}
                    {!!Form::select('void_type', ['All','Edited Invoices','Manually Voided'], null,['class' => 'form-control']) !!}
                </div>



            </div>

            <div class="row col-md-4">


                <div class="form-group m-r-20">
                    {!! Form::label('reference', 'Reference') !!}
                    {!! Form::text('reference', null, ['class' => 'form-control']) !!}
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
                <th style="width: 180px">Reference</th>
                <th style="width: 180px">Voided Date</th>
                <th style="width: 180px">Transaction Date</th>
                <th style="width: 180px">Voided By</th>
                <th style="width: 180px">Transaction By</th>
                <th style="width: 180px">Memo</th>
                <th style="width: 180px">Type</th>
                <th style="width: 180px">Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($result as $row)
                <tr>
                    <td>{{$row->reference}}</td>
                    <td>{{sql2date($row->voided_date)}}</td>
                    <td>{{sql2date($row->trans_date)}}</td>
                    <td>{{$row->voided_by}}</td>
                    <td>{{$row->transaction_done_by}}</td>
                    <td>{{$row->memo_}}</td>
                    <td>{{$row->type}}</td>
                    <td>{{$row->amount}}</td>
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







