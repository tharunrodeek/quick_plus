<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 11/7/2018
 * Time: 5:01 PM
 */

/**
 * @param $date
 * @return string
 */
function date2sq1($date){
    return Carbon\Carbon::parse($date)->format('Y-m-d');
}

function today() {
    $date = date('d-M-Y');
    return sql2date(Carbon\Carbon::parse($date)->format('d-m-Y'));
}

function setValue($old,$default) {
    return isset($old) ? $old : $default;
}

function sql2date($date) {
    return Carbon\Carbon::parse($date)->format('d-m-Y');
}

function payment_status_list() {
    return  [
        0 => 'All',
        1 => 'Fully Paid',
        2 => 'Not Paid',
        3 => 'Partially Paid',
    ];
}

function payment_status_all_list() {
    return  [
        0 => 'All',
        1 => 'Fully Paid',
        2 => 'Not Paid',
        3 => 'Partially Paid',
        4 => 'Not Paid/Partially Paid',
    ];
}

function payment_methods_list() {
    return  [
        '' => 'All',
        'Cash' => 'Cash',
        'CreditCard' => 'Credit Card',
        'BankTransfer' => 'Bank Transfer'
    ];
}


function work_location_list() {
    return  [
        '' => 'All',
        'AL-BARSHA' => 'AL-BARSHA',
        'AIRPORT' => 'AIRPORT',
        'EXPO' => 'EXPO',
        'SHURAA' => 'SHURAA',
        'CREATIVE-ZONE' => 'CREATIVE-ZONE',
    ];
}

function payment_status($id) {
   return payment_status_list()[$id];
}

function paginate ($total_rows)
{

    if(empty($total_rows))
        return false;

    $pagConfig = array(
        'baseURL'=>app('request')->url(),
        'totalRows'=>$total_rows,
        'perPage'=>10
    );
    $pagination =  new Pagination($pagConfig);
    return $pagination->createLinks();
}

function setMenuActiveClass($route_name) {

    if (starts_with(Route::currentRouteName(), $route_name.".")) {
        return "sideline-active";
    }

}

function input($var,$default = null) {
    if(!isset($var) || empty($var))
        return $default;
    return $var;
}



class Pagination{
    protected $baseURL      = '';
    protected $totalRows    = '';
    protected $perPage      = 10;
    protected $numLinks     =  2;
    protected $currentPage  =  0;
    protected $firstLink    = 'First';
    protected $nextLink     = 'Next &raquo;';
    protected $prevLink     = '&laquo; Prev';
    protected $lastLink     = 'Last';
    protected $fullTagOpen  = '<div class="pagination">';
    protected $fullTagClose = '</div>';
    protected $firstTagOpen = '';
    protected $firstTagClose= '&nbsp;';
    protected $lastTagOpen  = '&nbsp;';
    protected $lastTagClose = '';
    protected $curTagOpen   = '&nbsp;<b>';
    protected $curTagClose  = '</b>';
    protected $nextTagOpen  = '&nbsp;';
    protected $nextTagClose = '&nbsp;';
    protected $prevTagOpen  = '&nbsp;';
    protected $prevTagClose = '';
    protected $numTagOpen   = '&nbsp;';
    protected $numTagClose  = '';
    protected $showCount    = true;
    protected $currentOffset= 0;
    protected $queryStringSegment = 'page';

    function __construct($params = array()){
        if (count($params) > 0){
            $this->initialize($params);
        }
    }

    function initialize($params = array()){
        if (count($params) > 0){
            foreach ($params as $key => $val){
                if (isset($this->$key)){
                    $this->$key = $val;
                }
            }
        }
    }

    /**
     * Generate the pagination links
     */
    function createLinks(){
        // If total number of rows is zero, do not need to continue
        if ($this->totalRows == 0 OR $this->perPage == 0){
            return '';
        }
        // Calculate the total number of pages
        $numPages = ceil($this->totalRows / $this->perPage);
        // Is there only one page? will not need to continue
        if ($numPages == 1){
            if ($this->showCount){
                $info = 'Showing : ' . $this->totalRows;
                return $info;
            }else{
                return '';
            }
        }

        // Determine query string
        $query_string_sep = (strpos($this->baseURL, '?') === FALSE) ? '?page=' : '&amp;page=';
        $this->baseURL = $this->baseURL.$query_string_sep;

        // Determine the current page
        $this->currentPage = isset($_GET[$this->queryStringSegment]) ? $_GET[$this->queryStringSegment] : 1 ;

        if (!is_numeric($this->currentPage) || $this->currentPage == 0){
            $this->currentPage = 1;
        }

        // Links content string variable
        $output = '';

        // Showing links notification
        if ($this->showCount){
            $currentOffset = ($this->currentPage > 1)?($this->currentPage - 1)*$this->perPage:$this->currentPage;
            $info = 'Showing ' . $currentOffset . ' to ' ;

            if( ($currentOffset + $this->perPage) < $this->totalRows )
                $info .= $this->currentPage * $this->perPage;
            else
                $info .= $this->totalRows;

            $info .= ' of ' . $this->totalRows . ' | ';

            $output .= $info;
        }

        $this->numLinks = (int)$this->numLinks;

        // Is the page number beyond the result range? the last page will show
        if($this->currentPage > $this->totalRows){
            $this->currentPage = $numPages;
        }

        $uriPageNum = $this->currentPage;

        // Calculate the start and end numbers.
        $start = (($this->currentPage - $this->numLinks) > 0) ? $this->currentPage - ($this->numLinks - 1) : 1;
        $end   = (($this->currentPage + $this->numLinks) < $numPages) ? $this->currentPage + $this->numLinks : $numPages;


        unset($_GET['page']);
        $params = "&".http_build_query($_GET);

        // Render the "First" link
        if($this->currentPage > $this->numLinks){
            $firstPageURL = str_replace($query_string_sep,'',$this->baseURL);
            $output .= $this->firstTagOpen.'<a href="'.$firstPageURL.$params.'">'.$this->firstLink.'</a>'.$this->firstTagClose;
        }
        // Render the "previous" link
        if($this->currentPage != 1){
            $i = ($uriPageNum - 1);
            if($i == 0) $i = '';
            $output .= $this->prevTagOpen.'<a href="'.$this->baseURL.$i.$params.'">'.$this->prevLink.'</a>'.$this->prevTagClose;
        }
        // Write the digit links
        for($loop = $start -1; $loop <= $end; $loop++){
            $i = $loop;
            if($i >= 1){


                if($this->currentPage == $loop){
                    $output .= $this->curTagOpen.$loop.$this->curTagClose;
                }else{
                    $output .= $this->numTagOpen.'<a href="'.$this->baseURL.$i.$params.'">'.$loop.'</a>'.$this->numTagClose;
                }
            }
        }
        // Render the "next" link
        if($this->currentPage < $numPages){
            $i = ($this->currentPage + 1);
            $output .= $this->nextTagOpen.'<a href="'.$this->baseURL.$i.$params.'">'.$this->nextLink.'</a>'.$this->nextTagClose;
        }
        // Render the "Last" link
        if(($this->currentPage + $this->numLinks) < $numPages){
            $i = $numPages;
            $output .= $this->lastTagOpen.'<a href="'.$this->baseURL.$i.$params.'">'.$this->lastLink.'</a>'.$this->lastTagClose;
        }
        // Remove double slashes
        $output = preg_replace("#([^:])//+#", "\\1/", $output);
        // Add the wrapper HTML if exists
        $output = $this->fullTagOpen.$output.$this->fullTagClose;

        return $output;
    }
}