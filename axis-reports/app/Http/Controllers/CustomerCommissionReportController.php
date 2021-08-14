<?php

namespace App\Http\Controllers;

use App\Category;
use App\Customer;
use App\CustomerCommissionReport;
use App\Employee;
use App\EmployeeCommissionReport;
use App\Item;
use App\ServiceReport;
use Illuminate\Http\Request;

use App\Http\Requests;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class CustomerCommissionReportController extends Controller
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
            $items = Item::pluck('description', 'stock_id');

            $users->prepend('All', '');
            $customers->prepend('All', '');
            $items->prepend('All', '');



            $result = [];
            $total_rows = 0;
            if(!empty($request->All())) {

                $result_object = $this->arrayPaginator($request->All(),new CustomerCommissionReport());
                $result = $result_object['result'];
                $total_rows = $result_object['total_rows'];
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return view('customer_commission_report/list', compact(
                'result', 'customers', 'users', 'items','total_rows'
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
        set_time_limit(0);

        try {

            if(ob_get_level() > 0) {
                ob_end_clean();
            }

            $data = $this->getReport($request->All(),new CustomerCommissionReport());
            Excel::create('customer_commission_report', function ($excel) use ($data) {

                $excel->setTitle('Customer Comm. Report');
                $excel->setDescription('Customer Comm. Report');
                $excel->sheet('AxisPro-CustomerCommReport', function ($sheet) use ($data) {
                    $sheet->setOrientation('portrait');
                    $sheet->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

                    $this->setColumnHeader($sheet, ['Invoice No', 'Date', 'Customer', 'Reference Customer',
                        'Service', 'Employee', 'Unit Commission', 'Quantity',
                        'Total Commission']);

                    if (!empty($data)) {
                        $total_commission = 0;
                        foreach ($data as $key => $row) {
                            $i = $key + 2;
                            $sheet->cell('A' . $i, $row->invoice_no);
                            $sheet->cell('B' . $i, sql2date($row->transaction_date));
                            $sheet->cell('C' . $i, $row->customer_name)->setAutoSize(false);
                            $sheet->cell('D' . $i, $row->reference_customer)->setAutoSize(false);
                            $sheet->cell('E' . $i, $row->description)->setAutoSize(false);
                            $sheet->cell('F' . $i, $row->created_employee);
                            $sheet->cell('G' . $i, $row->customer_commission);
                            $sheet->cell('H' . $i, $row->quantity);
                            $sheet->cell('I' . $i, $row->total_customer_commission);
                            $total_commission += $row->total_customer_commission;
                        }
                        /** Set Total*/
                        $last_row = intval($sheet->getHighestRow());
                        $total_row = strval($last_row + 1);
                        $sheet->setCellValue($sheet->getHighestColumn() . $total_row, $total_commission);
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

            $fileName = storage_path()."/exports/customer_commission_report.$type";
            if (File::exists($fileName))
            {
                return Response::download($fileName);
            }

            return "Export Failed. Try Again";

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
