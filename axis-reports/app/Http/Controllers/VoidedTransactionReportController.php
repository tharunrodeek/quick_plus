<?php


namespace App\Http\Controllers;

use App\Category;
use App\Customer;
use App\CustomerCommissionReport;
use App\Employee;
use App\EmployeeCommissionReport;
use App\Item;
use App\ServiceReport;
use App\VoidedTransactionReport;
use Illuminate\Http\Request;

use App\Http\Requests;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class VoidedTransactionReportController extends Controller
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
            $users->prepend('All', '');

            $result = [];
            $total_rows = 0;
            if(!empty($request->All())) {

                $result_object = $this->arrayPaginator($request->All(),new VoidedTransactionReport());
                $result = $result_object['result'];
                $total_rows = $result_object['total_rows'];
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return view('voided_trans_report/list', compact(
                'result','users','total_rows'
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
        set_time_limit (0);
        try {
            if(ob_get_level() > 0) {
                ob_end_clean();
            }
            $data = $this->getReport($request->All(),new VoidedTransactionReport());
            Excel::create('voided_trans_report', function ($excel) use ($data) {

                $excel->setTitle('Voided Trans. Report');
                $excel->setDescription('Voided Trans. Report');
                $excel->sheet('AxisPro-VoidedTransReport', function ($sheet) use ($data) {
                    $sheet->setOrientation('portrait');
                    $sheet->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

                    $this->setColumnHeader($sheet, ['Reference', 'Voided Date', 'Transaction Date', 'Voided By',
                        'Transaction By', 'Memo', 'Type', 'Amount']);

                    if (!empty($data)) {
                        $total_amount = 0;
                        foreach ($data as $key => $row) {
                            $i = $key + 2;
                            $sheet->cell('A' . $i, $row->reference);
                            $sheet->cell('B' . $i, sql2date($row->voided_date));
                            $sheet->cell('C' . $i, sql2date($row->trans_date))->setAutoSize(false);
                            $sheet->cell('D' . $i, $row->voided_by)->setAutoSize(false);
                            $sheet->cell('E' . $i, $row->transaction_done_by)->setAutoSize(false);
                            $sheet->cell('F' . $i, $row->memo_);
                            $sheet->cell('G' . $i, $row->type);
                            $sheet->cell('H' . $i, $row->amount);

                            $total_amount += $row->amount;
                        }
                        /** Set Total*/
                        $last_row = intval($sheet->getHighestRow());
                        $total_row = strval($last_row + 1);
                        $sheet->setCellValue($sheet->getHighestColumn() . $total_row, $total_amount);
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

            $fileName = storage_path()."/exports/voided_trans_report.$type";
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
