<?php

namespace App\Http\Controllers;

use App\ServiceReport;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;


    /**
     * @param $array
     * @param $request
     * @return array
     * Generates Pagination
     */
    public function arrayPaginator($request,$model)
    {

        $page = Input::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        $sql = $model->getSQL($request);
        $total_count = DB::select("select count(*) as cnt from ($sql) as tmpTable");
        $sql = $sql." LIMIT $perPage OFFSET $offset";

        $result = DB::select($sql);
        $total_count=$total_count[0]->cnt;


        return ['result' => $result,'total_rows' => $total_count];

//        return new LengthAwarePaginator(array_slice($array, $offset, $perPage, true), $total_count, $perPage, $page,
//            ['path' => $request->url(), 'query' => $request->query()]);
    }

    /**
     * @param $sheet
     * @param array $array
     * Set Column Header
     */
    public function setColumnHeader($sheet, $array = [])
    {
//        foreach (range('A', 'Z') as $key => $v) {
//            $sheet->cell($v."1", $array[$key]);
//            if(count($array) == $key+1){
//                return;
//            }
//        }

        $cell = 'A';
        foreach ($array as $key => $v) {
            if($key != 0)  ++$cell;
            $sheet->cell($cell."1", $v);
        }
    }

    /**
     * @param array $filters
     * @param null $model
     * @return mixed
     * Get the Report for the given model
     */
    public function getReport($filters = [],$model = null)
    {
        $sql = $model->getSQL($filters);

        $result = DB::select($sql);
        return $result;

    }

}
