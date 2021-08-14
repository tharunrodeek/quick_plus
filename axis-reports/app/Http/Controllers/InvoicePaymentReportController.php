<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Category;
use App\Customer;
use App\CustomerCommissionReport;
use App\Employee;
use App\EmployeeCommissionReport;
use App\InvoicePaymentReport;
use App\Item;
use App\ServiceReport;
use App\VoidedTransactionReport;
use Illuminate\Http\Request;

use App\Http\Requests;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class InvoicePaymentReportController extends Controller
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

            $users = Employee::pluck('user_id', 'id');
            $customers = Customer::pluck('name', 'debtor_no');
            $banks = Bank::pluck('bank_account_name', 'id');

            $users->prepend('All', '');
            $customers->prepend('All', '');
            $banks->prepend('All', '');

            $result = [];
            $total_rows = 0;
            if(!empty($request->All())) {

                $result_object = $this->arrayPaginator($request->All(),new InvoicePaymentReport());
                $result = $result_object['result'];
                $total_rows = $result_object['total_rows'];
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return view('invoice_payment_report/list', compact(
                'result', 'users', 'customers', 'banks','total_rows'
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
            $data = $this->getReport($request->All(), new InvoicePaymentReport());
            Excel::create('invoice_payment_report', function ($excel) use ($data) {

                $excel->setTitle('Inv.Payment Report');
                $excel->setDescription('Inv.Payment Report');
                $excel->sheet('AxisPro-InvPayment Report', function ($sheet) use ($data) {
                    $sheet->setOrientation('portrait');
                    $sheet->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

                    $this->setColumnHeader($sheet, ['Date ', 'Receipt No', 'Invoices', 'Sum of Invoices',
                        'Total Discount/Reward Point', 'Net Payment', 'Collected Bank', 'Customer','User','Payment Method']);

                    if (!empty($data)) {
                        $total_amount = 0;
                        foreach ($data as $key => $row) {
                            $i = $key + 2;
                            $sheet->cell('A' . $i, sql2date($row->date_alloc));
                            $sheet->cell('B' . $i, $row->payment_ref);
                            $sheet->cell('C' . $i, $row->invoice_numbers)->setAutoSize(false);
                            $sheet->cell('D' . $i, $row->gross_payment)->setAutoSize(false);
                            $sheet->cell('E' . $i, $row->reward_amount)->setAutoSize(false);
                            $sheet->cell('F' . $i, $row->net_payment);
                            $sheet->cell('G' . $i, $row->bank_account_name);
                            $sheet->cell('H' . $i, $row->customer);
                            $sheet->cell('I' . $i, $row->user_id);
                            $sheet->cell('J' . $i, $row->payment_method);

                            $total_amount += $row->net_payment;
                        }
                        /** Set Total*/
                        $last_row = intval($sheet->getHighestRow());
                        $total_row = strval($last_row + 1);
                        $sheet->setCellValue("F" . $total_row, $total_amount);
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

            $fileName = storage_path()."/exports/invoice_payment_report.$type";
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
