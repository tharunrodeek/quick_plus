<?php

namespace App\Http\Controllers;

use App\Category;
use App\Customer;
use App\CustomerCommissionReport;
use App\Employee;
use App\EmployeeCommissionReport;
use App\Item;
use App\ServiceList;
use App\ServiceReport;
use App\VoidedTransactionReport;
use Illuminate\Http\Request;

use App\Http\Requests;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class ServiceListController extends Controller
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
            $categories = Category::pluck('description', 'category_id');
            $categories->prepend('All', '');

            $result = [];
            $total_rows = 0;
            if(!empty($request->All())) {

                $result_object = $this->arrayPaginator($request->All(),new ServiceList());
                $result = $result_object['result'];
                $total_rows = $result_object['total_rows'];
            }


        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return view('service_list/list', compact(
                'result', 'categories','total_rows'
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
            $data = $this->getReport($request->All(), new ServiceList());
            Excel::create('service_list', function ($excel) use ($data) {

                $excel->setTitle('Service List');
                $excel->setDescription('Service List');
                $excel->sheet('AxisPro-ServiceList', function ($sheet) use ($data) {
                    $sheet->setOrientation('portrait');
                    $sheet->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

                    $this->setColumnHeader($sheet, ['Stock ID', 'Service Name', 'Service Name(Arabic)', 'Category',
                        'Sales Account',
                        'COGS Account',
                        'Service Charge', 'Govt.Fee', 'Amer Charge', 'Bank Charge', 'VAT(Bank Charge)',
                        'Comm. Local User',
                        'Comm. Non. Local User',
                        'TAX',
                        'Total'
                    ]);

                    if (!empty($data)) {

                        foreach ($data as $key => $row) {
                            $i = $key + 2;
                            $sheet->cell('A' . $i, $row->stock_id);
                            $sheet->cell('B' . $i, $row->item_description)->setAutoSize(false);
                            $sheet->cell('C' . $i, $row->long_description)->setAutoSize(false);
                            $sheet->cell('D' . $i, $row->category_name);

                            $sheet->cell('E' . $i, $row->sales_account_name);
                            $sheet->cell('F' . $i, $row->cog_account_name);

                            $sheet->cell('G' . $i, $row->service_charge);
                            $sheet->cell('H' . $i, $row->govt_fee);
                            $sheet->cell('I' . $i, $row->pf_amount);
                            $sheet->cell('J' . $i, $row->bank_service_charge);
                            $sheet->cell('K' . $i, $row->bank_service_charge_vat);
                            $sheet->cell('L' . $i, $row->commission_loc_user);
                            $sheet->cell('M' . $i, $row->commission_non_loc_user);
                            $sheet->cell('N' . $i, $row->tax);
                            $sheet->cell('O' . $i, $row->total_amount+$row->tax);
                        }

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

            $fileName = storage_path()."/exports/service_list.$type";
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
