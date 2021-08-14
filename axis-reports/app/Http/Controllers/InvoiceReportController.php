<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Employee;
use App\InvoiceReport;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class InvoiceReportController extends Controller
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
            $users->prepend('All', '');
            $customers->prepend('All', '');

            $result = [];
            $total_rows = 0;
            if(!empty($request->All())) {

                $result_object = $this->arrayPaginator($request->All(),new InvoiceReport());
                $result = $result_object['result'];
                $total_rows = $result_object['total_rows'];
            }


        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return view('invoice_report/list', compact('result', 'customers', 'users','total_rows'));
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
        set_time_limit (0);
        try {
            if(ob_get_level() > 0) {
                ob_end_clean();
            }

            $data = $this->getReport($request->All(),new InvoiceReport());

            dd($data);

            Excel::create('invoice_list', function ($excel) use ($data) {

                $excel->setTitle('Invoice List');
                $excel->setDescription('Invoice List');
                $excel->sheet('AxisPro-InvoiceList', function ($sheet) use ($data) {
                    $sheet->setOrientation('portrait');
                    $sheet->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

                    $this->setColumnHeader($sheet,['Invoice No','Date','Customer','Display Customer','Reference Customer',
                        'Employee','Payment Status','Invoice Amount','Allocated Amount','Credit Not Amount']);

                    if (!empty($data)) {
                        $total_invoice_amount = 0;
                        foreach ($data as $key => $row) {
                            $i = $key + 2;
                            $sheet->cell('A' . $i, $row->invoice_no);
                            $sheet->cell('B' . $i, sql2date($row->transaction_date));
                            $sheet->cell('C' . $i, $row->customer_name);
                            $sheet->cell('D' . $i, $row->reference_customer);
                            $sheet->cell('E' . $i, $row->customer_ref);
                            $sheet->cell('F' . $i, $row->created_employee);
                            $sheet->cell('G' . $i, payment_status($row->payment_status));
                            $sheet->cell('H' . $i, $row->invoice_amount);
                            $sheet->cell('I' . $i, $row->alloc);
                            $sheet->cell('J' . $i, $row->creditNoteAmntSum);

                            $total_invoice_amount += $row->invoice_amount;
                        }
                        /** Set Total*/
                        $last_row = intval($sheet->getHighestRow());
                        $total_row = strval($last_row + 1);
                        $sheet->setCellValue('H' . $total_row, $total_invoice_amount);
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

            $fileName = storage_path()."/exports/invoice_list.$type";
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
