<?php
/**********************************************************************
 * Direct Axis Technology L.L.C.
 * Released under the terms of the GNU General Public License, GPL,
 * as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 ***********************************************************************/
define('VARLIB_PATH', $path_to_root . '/tmp');
define('VARLOG_PATH', $path_to_root . '/tmp');

class SessionManager
{
    function sessionStart($name, $limit = 0, $path = '/', $domain = null, $secure = null)
    {
        // Set the cookie name
        session_name($name);

        // Set SSL level
        $https = isset($secure) ? $secure : (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

        // Set session cookie options
        if (version_compare(PHP_VERSION, '5.2', '<')) // avoid failure on older php versions
            session_set_cookie_params($limit, $path, $domain, $https);
        else
            session_set_cookie_params($limit, $path, $domain, $https, true);

        session_start();

        // Make sure the session hasn't expired, and destroy it if it has
        if ($this->validateSession()) {
            // Check to see if the session is new or a hijacking attempt
            if (!$this->preventHijacking()) {
                // Reset session data and regenerate id
                $_SESSION = array();
                $_SESSION['IPaddress'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['userAgent'] = @$_SERVER['HTTP_USER_AGENT'];
                $this->regenerateSession();

                // Give a 5% chance of the session id changing on any request
            } elseif (rand(1, 100) <= 5) {
                $this->regenerateSession();
            }
        } else {
            $_SESSION = array();
            session_destroy();
            session_start();
        }
    }

    function preventHijacking()
    {
//        if (!isset($_SESSION['IPaddress']) || !isset($_SESSION['userAgent']))
//            return false;
//
//        if ($_SESSION['IPaddress'] != $_SERVER['REMOTE_ADDR'])
//            return false;
//
//        if ($_SESSION['userAgent'] != @$_SERVER['HTTP_USER_AGENT'])
//            return false;

        return true;
    }

    function regenerateSession()
    {
        // If this session is obsolete it means there already is a new id
        if (isset($_SESSION['OBSOLETE']) && ($_SESSION['OBSOLETE'] == true))
            return;

        // Set current session to expire in 10 seconds
        $_SESSION['OBSOLETE'] = true;
        $_SESSION['EXPIRES'] = time() + 60;

        // Create new session without destroying the old one
        session_regenerate_id();
        // Grab current session ID and close both sessions to allow other scripts to use them
        $newSession = session_id();
        session_write_close();
        // Set session ID to the new one, and start it back up again

        session_id($newSession);
        session_start();

        // Now we unset the obsolete and expiration values for the session we want to keep
        unset($_SESSION['OBSOLETE']);
        unset($_SESSION['EXPIRES']);
    }

    function validateSession()
    {
        if (isset($_SESSION['OBSOLETE']) && !isset($_SESSION['EXPIRES']))
            return false;

        if (isset($_SESSION['EXPIRES']) && $_SESSION['EXPIRES'] < time())
            return false;

        return true;
    }
}

function output_html($text)
{
    global $before_box, $Ajax, $messages;
    // Fatal errors are not send to error_handler,
    // so we must check the output
    if ($text && preg_match('/\bFatal error(<.*?>)?:(.*)/i', $text, $m)) {
        $Ajax->aCommands = array();  // Don't update page via ajax on errors
        $text = preg_replace('/\bFatal error(<.*?>)?:(.*)/i', '', $text);
        $messages[] = array(E_ERROR, $m[2], null, null);
    }
    $Ajax->run();
    return in_ajax() ? fmt_errors() : ($before_box . fmt_errors() . $text);
}

//----------------------------------------------------------------------------------------

function kill_login()
{
    session_unset();
    session_destroy();
}

//----------------------------------------------------------------------------------------

function login_fail()
{
    global $path_to_root;

    $_SESSION['login_failed'] = true;


    if (isset($_GET['login_method']) && $_GET['login_method'] == 'AJAX') {
        echo "FAIL";
        exit();
    }

//	header("HTTP/1.1 401 Authorization Required");
////	echo "<center><br><br><font size='5' color='red'><b>" . trans("Incorrect Password") . "<b></font><br><br>";
////	echo "<b>" . trans("The user and password combination is not valid for the system.") . "<b><br><br>";
////
////	echo trans("If you are not an authorized user, please contact your system administrator to obtain an account to enable you to use the system.");
////	echo "<br><a href='$path_to_root/index.php'>" . trans("Try again") . "</a>";
////	echo "</center>";

//	kill_login();
    header("Location: $path_to_root/index.php");

    die();
}

function password_reset_fail()
{
    global $path_to_root;

    echo "<center><br><br><font size='5' color='red'><b>" . trans("Incorrect Email") . "<b></font><br><br>";
    echo "<b>" . trans("The email address does not exist in the system, or is used by more than one user.") . "<b><br><br>";

    echo trans("Plase try again or contact your system administrator to obtain new password.");
    echo "<br><a href='$path_to_root/index.php?reset=1'>" . trans("Try again") . "</a>";
    echo "</center>";

    kill_login();
    die();
}

function password_reset_success()
{
    global $path_to_root;

    echo "<center><br><br><font size='5' color='green'><b>" . trans("New password sent") . "<b></font><br><br>";
    echo "<b>" . trans("A new password has been sent to your mailbox.") . "<b><br><br>";

    echo "<br><a href='$path_to_root/index.php'>" . trans("Login here") . "</a>";
    echo "</center>";

    kill_login();
    die();
}

function check_faillog()
{
    global $SysPrefs, $login_faillog;

    $user = $_SESSION["wa_current_user"]->user;

    if (@$SysPrefs->login_delay && (@$login_faillog[$user][$_SERVER['REMOTE_ADDR']] >= @$SysPrefs->login_max_attempts) && (time() < $login_faillog[$user]['last'] + $SysPrefs->login_delay))
        return true;

    return false;
}

/*
	Ensure file is re-read on next request if php caching is active
*/
function cache_invalidate($filename)
{
    if (function_exists('opcache_invalidate'))    // OpCode extension
        opcache_invalidate($filename);
}

/*
	Simple brute force attack detection is performed before connection to company database is open. Therefore access counters have to be stored in file.
	Login attempts counter is created for every new user IP, which partialy prevent DOS attacks.
*/
function write_login_filelog($login, $result)
{
    global $login_faillog, $SysPrefs, $path_to_root;

    $user = $_SESSION["wa_current_user"]->user;

    $ip = $_SERVER['REMOTE_ADDR'];

    if (!isset($login_faillog[$user][$ip]) || $result) // init or reset on successfull login
        $login_faillog[$user] = array($ip => 0, 'last' => '');

    if (!$result) {
        if ($login_faillog[$user][$ip] < @$SysPrefs->login_max_attempts) {

            $login_faillog[$user][$ip]++;
        } else {
            $login_faillog[$user][$ip] = 0; // comment out to restart counter only after successfull login.
            error_log(sprintf(trans("Brute force attack on account '%s' detected. Access for non-logged users temporarily blocked."), $login));
        }
        $login_faillog[$user]['last'] = time();
    }

    $msg = "<?php\n";
    $msg .= "/*\n";
    $msg .= "Login attempts info.\n";
    $msg .= "*/\n";
    $msg .= "\$login_faillog = " . var_export($login_faillog, true) . ";\n";

    $filename = VARLIB_PATH . "/faillog.php";

    if ((!file_exists($filename) && is_writable(VARLIB_PATH)) || is_writable($filename)) {
        file_put_contents($filename, $msg);
        cache_invalidate($filename);
    }
}

//----------------------------------------------------------------------------------------

function check_page_security($page_security)
{
    global $SysPrefs;

    $msg = '';

    if (!$_SESSION["wa_current_user"]->check_user_access()) {
        // notification after upgrade from pre-2.2 version
        $msg = $_SESSION["wa_current_user"]->old_db ?
            trans("Security settings have not been defined for your user account.")
            . "<br>" . trans("Please contact your system administrator.")
            : trans("Please remove \$security_groups and \$security_headings arrays from config.php file!");
    } elseif (!$SysPrefs->db_ok && !$_SESSION["wa_current_user"]->can_access('SA_SOFTWAREUPGRADE')) {
        $msg = trans('Access to application has been blocked until database upgrade is completed by system administrator.');
    }

    if ($msg) {
        display_error($msg);
        end_page(@$_REQUEST['popup']);
        kill_login();
        exit;
    }

    if (!$_SESSION["wa_current_user"]->can_access_page($page_security)) {

        echo "<center><br><br><br><b>";
        echo trans("The security settings on your account do not permit you to access this function");
        echo "</b>";
        echo "<br><br><br><br></center>";
        end_page(@$_REQUEST['popup']);
        exit;
    }
    if (!$SysPrefs->db_ok
        && !in_array($page_security, array('SA_SOFTWAREUPGRADE', 'SA_OPEN', 'SA_BACKUP'))) {
        display_error(trans('System is blocked after source upgrade until database is updated on System/Software Upgrade page'));
        end_page();
        exit;
    }

}

/*
	Helper function for setting page security level depeding on 
	GET start variable and/or some value stored in session variable.
	Before the call $page_security should be set to default page_security value.
*/
function set_page_security($value = null, $trans = array(), $gtrans = array())
{
    global $page_security;

    // first check is this is not start page call
    foreach ($gtrans as $key => $area)
        if (isset($_GET[$key])) {
            $page_security = $area;
            return;
        }

    // then check session value
    if (isset($trans[$value])) {
        $page_security = $trans[$value];
        return;
    }
}

//-----------------------------------------------------------------------------
//	Removing magic quotes from nested arrays/variables
//
function strip_quotes($data)
{
    if (get_magic_quotes_gpc()) {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = strip_quotes($data[$k]);
            }
        } else
            return stripslashes($data);
    }
    return $data;
}

/*
	htmlspecialchars does not support certain encodings.
	ISO-8859-2 fortunately has the same special characters positions as 
	ISO-8859-1, so fix is easy. If any other unsupported encoding is used,
	add workaround here.
*/
function html_specials_encode($str)
{
    return htmlspecialchars($str, ENT_QUOTES, $_SESSION['language']->encoding == 'iso-8859-2' ?
        'ISO-8859-1' : $_SESSION['language']->encoding);
}

function html_cleanup(&$parms)
{
    foreach ($parms as $name => $value) {
        if (is_array($value))
            html_cleanup($parms[$name]);
        else
            $parms[$name] = html_specials_encode($value);
    }
    reset($parms); // needed for direct key() usage later throughout the sources
}

//============================================================================
//
//
function login_timeout()
{
    // skip timeout on logout page
    if ($_SESSION["wa_current_user"]->logged) {
        $tout = $_SESSION["wa_current_user"]->timeout;
        if ($tout && (time() > $_SESSION["wa_current_user"]->last_act + (int)$tout)) {
            $_SESSION["wa_current_user"]->logged = false;
        }
        $_SESSION["wa_current_user"]->last_act = time();
    }
}

//============================================================================
if (!isset($path_to_root)) {
    $path_to_root = ".";
}

// Prevent register_globals vulnerability
if (isset($_GET['path_to_root']) || isset($_POST['path_to_root']))
    die("Restricted access");

include_once($path_to_root . "/includes/errors.inc");
// colect all error msgs
set_error_handler('error_handler' /*, errtypes */);
set_exception_handler('exception_handler');

include_once($path_to_root . "/includes/current_user.inc");
include_once($path_to_root . "/frontaccounting.php");
include_once($path_to_root . "/admin/db/security_db.inc");
include_once($path_to_root . "/includes/lang/language.inc");
include_once($path_to_root . "/config_db.php");
include_once($path_to_root . "/includes/ajax.inc");
include_once($path_to_root . "/includes/ui/ui_msgs.inc");
include_once($path_to_root . "/includes/prefs/sysprefs.inc");

include_once($path_to_root . "/includes/hooks.inc");
//
// include all extensions hook files.
//
foreach ($installed_extensions as $ext) {
    if (file_exists($path_to_root . '/' . $ext['path'] . '/hooks.php'))
        include_once($path_to_root . '/' . $ext['path'] . '/hooks.php');
}
/** 
 * Moved from below
 *
 * session.<config_variable> must be set before starting the session. 
 */
ini_set('session.gc_maxlifetime', 36000); // 10hrs

$Session_manager = new SessionManager();
$Session_manager->sessionStart('FA' . md5(dirname(__FILE__)));

$_SESSION['SysPrefs'] = new sys_prefs();

$SysPrefs = &$_SESSION['SysPrefs'];

//----------------------------------------------------------------------------------------
// set to reasonable values if not set in config file (pre-2.3.12 installations)

if ((!isset($SysPrefs->login_delay)) || ($SysPrefs->login_delay < 0))
    $SysPrefs->login_delay = 10;

if ((!isset($SysPrefs->login_max_attempts)) || ($SysPrefs->login_max_attempts < 0))
    $SysPrefs->login_max_attempts = 3;

if ($SysPrefs->go_debug > 0)
    error_reporting(-1);
else
    error_reporting(E_USER_WARNING | E_USER_ERROR | E_USER_NOTICE);
ini_set("display_errors", "On");

if ($SysPrefs->error_logfile != '') {
    ini_set("error_log", $SysPrefs->error_logfile);
    ini_set("ignore_repeated_errors", "On");
    ini_set("log_errors", "On");
}

/*
	Uncomment the setting below when using FA on shared hosting
	to avoid unexpeced session timeouts.
	Make sure this directory exists and is writable!
*/
// ini_set('session.save_path', VARLIB_PATH.'/');

hook_session_start(@$_POST["company_login_name"]);

// this is to fix the "back-do-you-want-to-refresh" issue - thanx PHPFreaks
header("Cache-control: private");

get_text_init();

if ($SysPrefs->login_delay > 0 && file_exists(VARLIB_PATH . "/faillog.php"))
    include_once(VARLIB_PATH . "/faillog.php");

// Page Initialisation
if (!isset($_SESSION['wa_current_user']) || !$_SESSION['wa_current_user']->logged_in()
    || !isset($_SESSION['language']) || !method_exists($_SESSION['language'], 'set_language')) {
    $l = array_search_value($dflt_lang, $installed_languages, 'code');
    $_SESSION['language'] = new language($l['name'], $l['code'], $l['encoding'],
        (isset($l['rtl']) && $l['rtl'] === true) ? 'rtl' : 'ltr');
}

$_SESSION['language']->set_language($_SESSION['language']->code);


include_once($path_to_root . "/includes/access_levels.inc");
include_once($path_to_root . "/version.php");
include_once($path_to_root . "/includes/main.inc");
include_once($path_to_root . "/includes/app_entries.inc");

// Ajax communication object
$Ajax = new Ajax();

// js/php validation rules container
$Validate = array();
// bindings for editors
$Editors = array();
// page help. Currently help for function keys.
$Pagehelp = array();

$Refs = new references();

// intercept all output to destroy it in case of ajax call
register_shutdown_function('end_flush');
ob_start('output_html', 0);

if (!isset($_SESSION["wa_current_user"]))
    $_SESSION["wa_current_user"] = new current_user();


//$_SESSION


html_cleanup($_GET);
html_cleanup($_POST);
html_cleanup($_REQUEST);
html_cleanup($_SERVER);

// logout.php is the only page we should have always 
// accessable regardless of access level and current login status.
if (!defined('FA_LOGOUT_PHP_FILE')) {

    login_timeout();

    if (!$_SESSION["wa_current_user"]->old_db && file_exists($path_to_root . '/company/' . user_company() . '/installed_extensions.php'))
        include($path_to_root . '/company/' . user_company() . '/installed_extensions.php');

    install_hooks();

    if (!$_SESSION["wa_current_user"]->logged_in()) {
        if (@$SysPrefs->allow_password_reset && !$SysPrefs->allow_demo_mode
            && (isset($_GET['reset']) || isset($_POST['email_entry_field']))) {
            if (!isset($_POST["email_entry_field"])) {
                include($path_to_root . "/access/password_reset.php");
                exit();
            } else {
                if (isset($_POST["company_login_nickname"]) && !isset($_POST["company_login_name"])) {
                    for ($i = 0; $i < count($db_connections); $i++) {
                        if ($db_connections[$i]["name"] == $_POST["company_login_nickname"]) {
                            $_POST["company_login_name"] = $i;
                            unset($_POST["company_login_nickname"]);
                            break 1; // cannot pass variables to break from PHP v5.4 onwards
                        }
                    }
                }
                $_succeed = isset($db_connections[$_POST["company_login_name"]]) &&
                    $_SESSION["wa_current_user"]->reset_password($_POST["company_login_name"],
                        $_POST["email_entry_field"]);
                if ($_succeed) {
                    password_reset_success();
                }

                password_reset_fail();
            }
        }
        // Show login screen
        if (!isset($_POST["user_name_entry_field"]) or $_POST["user_name_entry_field"] == "") {
            // strip ajax marker from uri, to force synchronous page reload
            $_SESSION['timeout'] = array('uri' => preg_replace('/JsHttpRequest=(?:(\d+)-)?([^&]+)/s',
                '', html_specials_encode($_SERVER['REQUEST_URI'])),
                'post' => $_POST);


            if (isAjax()) {
                echo json_encode(['status' => 'LOGIN_TIME_OUT', 'msg' => 'Login timeout, Please login again']);
                exit();

            }

            if (in_ajax()) {
                $Ajax->redirect($_SESSION['timeout']['uri']);
                exit;
            }


            header("Location: " . $SysPrefs->project_url . "login.php");
            exit();

            include($path_to_root . "/themes/daxis/login.php");
            if (in_ajax()) {

                $Ajax->redirect($_SESSION['timeout']['uri']);

            }
            exit;
        } else {

            # Verify captcha
            /* $post_data = http_build_query(
                 array(
                     'secret' => '6LdDGmIUAAAAANU4uiLM-fPytmJ4E_pMD7eZ_tJI',
                     'response' => $_POST['g-recaptcha-response'],
                     'remoteip' => $_SERVER['REMOTE_ADDR']
                 )
             );
             $opts = array('http' =>
                 array(
                     'method'  => 'POST',
                     'header'  => 'Content-type: application/x-www-form-urlencoded',
                     'content' => $post_data
                 )
             );
             $context  = stream_context_create($opts);
             $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
             $result = json_decode($response);
             if (!$result->success) {
                 $_SESSION['captcha_error'] = 1;
                 login_fail();
             } */

            $current_domain = $_SERVER['SERVER_NAME'];

            if ($current_domain != "erp.yalayis.com") {

                echo("This application is registered for YBC.
                <br> Please contact the service provider to install this application outside of YBC Server
                <br><br><a target='_blank' href='http://www.axisproerp.com'>www.axisproerp.com</a>
                <br>+971564089262,+971564089263");
                exit();
            }



            if (isset($_POST["company_login_nickname"]) && !isset($_POST["company_login_name"])) {
                for ($i = 0; $i < count($db_connections); $i++) {
                    if ($db_connections[$i]["name"] == $_POST["company_login_nickname"]) {
                        $_POST["company_login_name"] = $i;
                        unset($_POST["company_login_nickname"]);
                        break 1; // cannot pass variables to break from PHP v5.4 onwards
                    }
                }
            }
            $succeed = isset($db_connections[$_POST["company_login_name"]]) &&
                $_SESSION["wa_current_user"]->login($_POST["company_login_name"],
                    $_POST["user_name_entry_field"], $_POST["password"]);
            // select full vs fallback ui mode on login

            if (isset($_GET['login_method']) && $_GET['login_method'] == 'AJAX' && $succeed) {
                echo "SUCCESS";
                exit();
            }


            $_SESSION["wa_current_user"]->ui_mode = $_POST['ui_mode'];


            if (!$succeed) {

                // Incorrect password
                login_fail();
            } elseif (isset($_SESSION['timeout']) && !$_SESSION['timeout']['post']) {
                // in case of GET request redirect to avoid confirmation dialog
                // after return from menu option

                if (isset($_GET['login_method']) && $_GET['login_method'] == 'AJAX') {
                    echo "SUCCESS";
                    exit();
                }


                if (isAjax()) {

                    echo json_encode(['status' => 'LOGIN_TIME_OUT', 'msg' => 'Login timeout, Please login again']);
                    exit();

                }


                header("HTTP/1.1 303 See Other");
//				header("Location: ".$_SESSION['timeout']['uri']);
                header("Location: " . $path_to_root . "/?application=dashboard");
                exit();
            }
            $lang = &$_SESSION['language'];
            $lang->set_language($_SESSION['language']->code);
        }
    } else {
        set_global_connection();

        if (db_fixed())
            db_set_encoding($_SESSION['language']->encoding);

        $SysPrefs->refresh();

        if ($SysPrefs->refreshPermissionsOnEachRequest()){
            $_SESSION['wa_current_user']->refreshPermissions();
        }


        if (isset($_GET['login_method']) && $_GET['login_method'] == 'AJAX') {
            echo "SUCCESS";
            exit();
        }

    }
    if (!isset($_SESSION["App"])) {

        if (isset($_GET['login_method']) && $_GET['login_method'] == 'AJAX') {
            echo "SUCCESS";
            exit();
        }

        $_SESSION["App"] = new front_accounting();
        $_SESSION["App"]->init();
    }
}

// POST vars cleanup needed for direct reuse.
// We quote all values later with db_escape() before db update.
$_POST = strip_quotes($_POST);


global $SysPrefs;
if (isset($SysPrefs->maintenance_flag) && $SysPrefs->maintenance_flag) {
    header("Location: $path_to_root/maintenance_mode.php");
    exit;
}