<?php
/**********************************************************************
    Direct Axis Technology L.L.C.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
	$path_to_root=".";
	if (!file_exists($path_to_root.'/config_db.php'))
		header("Location: ".$path_to_root."/install/index.php");




//Restricting access for YBC

$white_listed_ips = array('92.97.218.172','https://erp.yalayis.com','http://erp.yalayis.com');

if (!HttpOriginCheck($white_listed_ips)) {
    echo("This application is registered for YBC.
    <br> Please contact the service provider to install this application outside of YBC Server
    <br><br><a target='_blank' href='http://www.axisproerp.com'>www.axisproerp.com</a>
    <br>+971564089262,+971564089263");
    exit();
}


function HttpOriginCheck($allowed)
{

    $origin = "";

    if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
        $origin = $_SERVER['HTTP_ORIGIN'];
    }
    else if (array_key_exists('HTTP_REFERER', $_SERVER)) {
        $origin = $_SERVER['HTTP_REFERER'];
    } else {
        $origin = $_SERVER['REMOTE_ADDR'];
    }

    if (isset($origin) && in_array($origin, $allowed))
        return true;

    return false;

}



	$page_security = 'SA_OPEN';
	ini_set('xdebug.auto_trace',1);
	include_once("includes/session.inc");

	add_access_extensions();
	$app = &$_SESSION["App"];
	if (isset($_GET['application']))
		$app->selected_application = $_GET['application'];

	$app->display();


//display_error(print_r($_SESSION['wa_current_user'],true));

