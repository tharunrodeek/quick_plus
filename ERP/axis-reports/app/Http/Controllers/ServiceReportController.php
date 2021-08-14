<?php

namespace App\Http\Controllers;
use App\Category;
use App\Customer;
use App\Employee;
use App\Item;
use App\SalesMan;
use App\ServiceReportModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;

class ServiceReportController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     * List Report
     */
    public function index(Request $request)
    {
        try {
            $customers = Customer::pluck('name', 'debtor_no');
            $users = Employee::pluck('user_id', 'id');
            $categories = Category::pluck('description','category_id');
            $items = Item::pluck('description','stock_id');
            $sales_men = SalesMan::pluck('salesman_name','salesman_code');

            $users->prepend('All', '');
            $customers->prepend('All', '');
            $categories->prepend('All', '');
            $items->prepend('All', '');
            $sales_men->prepend('All', '');

            $result = [];
            $total_rows = 0;
            if(!empty($request->All())) {

                $result_object = $this->arrayPaginator($request->All(),new ServiceReportModel());
                $result = $result_object['result'];
                $total_rows = $result_object['total_rows'];
            }


        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return view('service_report/list', compact(
                'result', 'customers', 'users','sales_men',
                'categories','items','total_rows'
            )
        );
    }

    /**
     * @param Request $request
     * @param string $type
     * @return mixed
     * @throws \Exception
     * Export Report
     */
    public function export(Request $request, $type = 'pdf')
    {

        ini_set('memory_limit', '500M');
        set_time_limit ( 60 );

        try {

            if(ob_get_level() > 0) {
                ob_end_clean();
            }
            $data = $this->getReport($request->All(),new ServiceReportModel());
            Excel::create('servicereport', function ($excel) use ($data) {
                $excel->setTitle('ServiceReport');
                $excel->setDescription('ServiceRepor');
                $excel->sheet('AxisProServiceReport', function ($sheet) use ($data) {
                    $sheet->setOrientation('portrait');
                    $this->setColumnHeader($sheet,['Invoice No','Date','Customer','Display Customer','Sales Man',
                        'Service','Category','Unit Price','Qty','Total Service Charge','Unit Tax','Total Tax',
                        'Discount Amount','Govt. Fee','Total Govt. Fee','Bank Charge','VAT(Bank Charge)','PF. Amount',
                        'Customer Commission','Reward Amount','Employee Commission',

                        'Govt. Bank',
                        'COGS Account',
                        'Sales Account',

                        'Transaction ID',
                        'REF NAME',
                        'APPLICATION ID',
                        'REF CUSTOMER',
                        'Work Location',
                        'Employee',
                        'Payment Status',
                        'Net Service Amount','Invoice Amount']);
                    if (!empty($data)) {

                        $total_invoice_amount = 0;
                        foreach ($data as $key => $row) {


                            if(empty($row->ref_id))
                                $row->ref_id=$row->ref_name;

                            if(!empty($row->ed_transaction_id))
                                $row->transaction_id=$row->ed_transaction_id;


                            $i = $key + 2;
                            $sheet->cell('A' . $i, $row->invoice_no);
                            $sheet->cell('B' . $i, sql2date($row->transaction_date));
                            $sheet->cell('C' . $i, $row->customer_name)->setAutoSize(false);
                            $sheet->cell('D' . $i, $row->reference_customer)->setAutoSize(false);
                            $sheet->cell('E' . $i, $row->salesman_name)->setAutoSize(false);
                            $sheet->cell('F' . $i, $row->description)->setAutoSize(false);
                            $sheet->cell('G' . $i, $row->category_name);
                            $sheet->cell('H' . $i, $row->unit_price);
                            $sheet->cell('I' . $i, $row->quantity);
                            $sheet->cell('J' . $i, $row->total_price);
                            $sheet->cell('K' . $i, $row->unit_tax);
                            $sheet->cell('L' . $i, $row->total_tax);
                            $sheet->cell('M' . $i, $row->discount_amount);
                            $sheet->cell('N' . $i, $row->govt_fee);
                            $sheet->cell('O' . $i, $row->total_govt_fee);
                            $sheet->cell('P' . $i, $row->bank_service_charge);
                            $sheet->cell('Q' . $i, $row->bank_service_charge_vat);
                            $sheet->cell('R' . $i, $row->pf_amount);
                            $sheet->cell('S' . $i, $row->total_customer_commission);
                            $sheet->cell('T' . $i, $row->reward_amount);
                            $sheet->cell('U' . $i, $row->user_commission);

                            $sheet->cell('V' . $i, $row->govt_bank_account);
                            $sheet->cell('W' . $i, $row->stock_cogs_account);
                            $sheet->cell('X' . $i, $row->stock_sales_account);

                            $sheet->cell('Y' . $i, $row->transaction_id);
                            $sheet->cell('Z' . $i, $row->application_id);
                            $sheet->cell('AA' . $i, $row->ref_id);
                            $sheet->cell('AB' . $i, $row->customer_ref);
                            $sheet->cell('AC' . $i, $row->work_location);
//                            $sheet->cell('W' . $i, $row->application_id);
                            $sheet->cell('AD' . $i, $row->created_employee);
                            $sheet->cell('AE' . $i, payment_status($row->payment_status));
                            $sheet->cell('AF' . $i, $row->net_service_charge);
                            $sheet->cell('AG' . $i, $row->invoice_amount);
                            $total_invoice_amount += $row->invoice_amount;
                        }

                        /** Set Total*/
                        $last_row = intval($sheet->getHighestRow());
                        $total_row = strval($last_row + 1);
                        $sheet->setCellValue($sheet->getHighestColumn() . $total_row, $total_invoice_amount);
                        $sheet->row($total_row, function ($row) {
                            $row->setBackground('#d9d9d9');
                        });
                        /** END -- Set total */

                    }

                    /** Other Formatting */
                    $sheet->setPageMargin(0);
                    //$sheet->setAutoSize(false);
                    $sheet->setBorder('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow());
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#d9d9d9');
                    });
                    /** End -- Other Formatting */
                });
            })->store($type);


            $fileName = storage_path()."/exports/servicereport.$type";
            if (File::exists($fileName))
            {
                return Response::download($fileName);
            }

            return "Export Failed. Try Again";

        } catch (\Exception $e) {
//            throw new \Exception($e->getMessage());
        }
    }
}
