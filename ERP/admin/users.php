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
$page_security = 'SA_USERS';
$path_to_root = "..";
include_once($path_to_root . "/includes/session.inc");

page(trans($help_context = "Users"));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/admin/db/users_db.inc");
include_once($path_to_root . "/modules/ExtendedHRM/includes/ui/common.inc");
//include_once($path_to_root . "/modules/ExtendedHRM/includes/db/empl_db.inc");

simple_page_mode(true);
//-------------------------------------------------------------------------------------------------

function can_process($new)
{

    if ($_POST['ddl_employee'] == '') {
//        display_error( trans("Please select employee"));
//        set_focus('ddl_employee');
//        return false;
    }

    if (strlen($_POST['user_id']) < 4) {
        display_error(trans("The user login entered must be at least 4 characters long."));
        set_focus('user_id');
        return false;
    }

    if (!$new && ($_POST['password'] != "")) {
        if (strlen($_POST['password']) < 4) {
            display_error(trans("The password entered must be at least 4 characters long."));
            set_focus('password');
            return false;
        }

        if (strstr($_POST['password'], $_POST['user_id']) != false) {
            display_error(trans("The password cannot contain the user login."));
            set_focus('password');
            return false;
        }
    }

    return true;
}

//-------------------------------------------------------------------------------------------------

if (($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM') && check_csrf_token()) {

    $permitted_cats = implode(",", get_post('permitted_categories'));

    $ip_restriction = $_POST['ip_restriction'];


    if(empty($_POST['purch_req_send_to_level_one']))
        $_POST['purch_req_send_to_level_one'] = 0;

    if(empty($_POST['purch_req_send_to_level_two']))
        $_POST['purch_req_send_to_level_two'] = 0;

    if (can_process($Mode == 'ADD_ITEM')) {
        if ($selected_id != -1) {


            update_user_prefs($selected_id,
                get_post(array(
                    'user_id',
                    'real_name',
                    'phone',
                    'email',
                    'role_id',
                    'language',
                    'print_profile',
                    'rep_popup' => 0,
                    'pos',
                    'is_local',
                    'cashier_account',
                    'govt_credit_account',
                    'cash_handover_dr_act',
                    'user_language',
                    'dflt_dimension_id','ip_restriction'
                ))
            );

            if ($_POST['password'] != "")
                update_user_password($selected_id, $_POST['user_id'], md5($_POST['password']));


            $sql = "UPDATE 0_users SET permitted_categories='$permitted_cats' WHERE id=$selected_id";
            db_query($sql);

            // if(!empty($_POST['purch_req_send_to'])) {

            //     $purch_req_send_to = get_user_by_login($_POST['purch_req_send_to'])['id'];
            //     $sql = "UPDATE 0_users SET purch_req_send_to=" . $purch_req_send_to . " WHERE id=$selected_id";
            //     db_query($sql);

            // }

            // if(!empty($_POST['purch_req_send_to_level_one'])) {

                $purch_req_send_to_level_one = get_user_by_login($_POST['purch_req_send_to_level_one'])['id'];
                $sql = "UPDATE 0_users SET purch_req_send_to_level_one=" . $purch_req_send_to_level_one . " WHERE id=$selected_id";
                db_query($sql);

            // }

            // if(!empty($_POST['purch_req_send_to_level_two'])) {

                $purch_req_send_to_level_two = get_user_by_login($_POST['purch_req_send_to_level_two'])['id'];
                $sql = "UPDATE 0_users SET purch_req_send_to_level_two=" . $purch_req_send_to_level_two . " WHERE id=$selected_id";
                db_query($sql);

            // }


            $sql_e = "SELECT CONCAT(empl_firstname,' ',empl_lastname) AS empname,email,mobile_phone FROM 0_kv_empl_info  where id='" . $_POST['ddl_employee'] . "' ";
            $result_e = db_query($sql_e, "Could not get data");
            $data_e = db_fetch($result_e);
            $employee_data = $data_e;


                 $sql_user = "UPDATE 0_users SET employee_id='".$_POST['ddl_employee']."',real_name='".$employee_data[0]."',phone='".$employee_data[2]."',
                              email='".$employee_data[1]."'
                         WHERE id=$selected_id ";

//                 db_query($sql_user);


            display_notification_centered(trans("User has been updated."));
        } else {
            $sql_e = "SELECT CONCAT(empl_firstname,'',empl_lastname) AS empname,email,mobile_phone FROM 0_kv_empl_info  where id='" . $_POST['ddl_employee'] . "' ";
            $result_e = db_query($sql_e, "Could not get data");
            $data_e = db_fetch($result_e);
            $employee_data = $data_e;


            add_user($_POST['user_id'], $employee_data[0], md5($_POST['password']),
                $employee_data[2], $employee_data[1], $_POST['role_id'], $_POST['language'],
                $_POST['print_profile'], check_value('rep_popup'), $_POST['pos'],
                check_value('is_local'), $_POST['cashier_account']);
            $id = db_insert_id();
            // use current user display preferences as start point for new user
            $prefs = $_SESSION['wa_current_user']->prefs->get_all();
            $other_prefs = get_post(array(
                'print_profile',
                'rep_popup' => 0,
                'language',
                'govt_credit_account',
                'cash_handover_dr_act',
                'dflt_dimension_id',
                'user_language'
            ));
            $other_prefs['permitted_categories'] = $permitted_cats;
            $other_prefs['ip_restriction'] = $ip_restriction;

            update_user_prefs($id, array_merge($prefs, $other_prefs));


//            if(!empty($_POST['purch_req_send_to_level_one'])) {

                $purch_req_send_to_level_one = get_user_by_login($_POST['purch_req_send_to_level_one'])['id'];
                $sql = "UPDATE 0_users SET purch_req_send_to_level_one=" . $purch_req_send_to_level_one . " WHERE id=$id";
                db_query($sql);

//            }

//            if(!empty($_POST['purch_req_send_to_level_two'])) {

                $purch_req_send_to_level_two = get_user_by_login($_POST['purch_req_send_to_level_two'])['id'];
                $sql = "UPDATE 0_users SET purch_req_send_to_level_two=" . $purch_req_send_to_level_two . " WHERE id=$id";
                db_query($sql);

//            }

            /*------------------------Update User to Employee----------*/
          /*  $sql_user = "UPDATE 0_kv_empl_info SET user_id='$id' WHERE id='" . $_POST['ddl_employee'] . "'";
            db_query($sql_user);*/





            $sql_user = "UPDATE 0_users SET employee_id='".$_POST['ddl_employee']."' 
                         WHERE id=$id ";


            db_query($sql_user);
            /*-----------------------------END--------------------------*/

            display_notification_centered(trans("A new user has been added."));
        }
        $Mode = 'RESET';
    }
}

//-------------------------------------------------------------------------------------------------

if ($Mode == 'Delete' && check_csrf_token()) {
    $cancel_delete = 0;
    if (key_in_foreign_table($selected_id, 'audit_trail', 'user')) {
        $cancel_delete = 1;
        display_error(trans("Cannot delete this user because entries are associated with this user."));
    }
    if ($cancel_delete == 0) {
        delete_user($selected_id);
        display_notification_centered(trans("User has been deleted."));

        $sql_user = "UPDATE 0_kv_empl_info SET user_id='0' WHERE user_id='" . $selected_id . "' ";
        db_query($sql_user);

    } //end if Delete group
    $Mode = 'RESET';
}

//-------------------------------------------------------------------------------------------------
if ($Mode == 'RESET') {
    $selected_id = -1;
    $sav = get_post('show_inactive', null);
    unset($_POST);    // clean all input fields
    $_POST['show_inactive'] = $sav;
}
 
    
 
 
$result = get_users(check_value('show_inactive'),$_POST['cost_center']);
 
 
start_form();

$is_collapsed = !($selected_id != -1 && $Mode == 'Edit');
$collapse_id = 'add-new-user';

br();

dimensions_list_cells(trans('Choose Cost Center :'),'cost_center',$_POST['cost_center'],true,'--All--','','',false);
//dimensions_list_row(trans("Choose Cost Center :"), 'cost_center', null,'---All---',false);

//dimensions_list_row(trans("Select an Employee")." :", 'cost_center', null,  trans("---ALL---"), true, null, true, false,fasle);
list_updated('cost_center');       
submit_cells('RefreshUsers', trans("Search"), '', trans('Refresh Users'), 'default');
$Ajax->activate('_page_body');

echo '<div class="float-right">';
collapse_control('Add New User', $collapse_id, $is_collapsed);
echo '</div>';

br(2);

/**------------------------------------------*/

start_collapsible_div($collapse_id, $is_collapsed);

start_table(TABLESTYLE2);

$_POST['email'] = "";
$_POST['cashier_account'] = get_default_bank_account('AED');
//$_POST['govt_credit_account'] = "";
$permitted_cats = "";
$sql_e = "SELECT employee_id FROM 0_users  where id='" . $selected_id . "' ";
$result_e = db_query($sql_e, "Could not get data");
$data_e = db_fetch($result_e);
$employee_pk_id = $data_e;
$_POST['ddl_employee'] = $employee_pk_id[0];
//users_list_cells_display(trans("Select Employee:"), 'ddl_employee', $_POST['ddl_employee'], '');
//employee_list_cells(trans("Select an Employee")." :", 'empl_id', null,    trans("New Employee"), true, check_value('show_inactive'), false, false,true);
// employee_list_row(trans("Report To:"), 'report_to', null, trans("Select an Employee"));
if ($selected_id != -1) {
    if ($Mode == 'Edit') {
        //editing an existing User
        $myrow = get_user($selected_id);

        $_POST['id'] = $myrow["id"];
        $_POST['user_id'] = $myrow["user_id"];
        $_POST['real_name'] = $myrow["real_name"];
        $_POST['phone'] = $myrow["phone"];
        $_POST['email'] = $myrow["email"];
        $_POST['role_id'] = $myrow["role_id"];
        $_POST['language'] = $myrow["language"];
        $_POST['print_profile'] = $myrow["print_profile"];
        $_POST['rep_popup'] = $myrow["rep_popup"];
        $_POST['pos'] = $myrow["pos"];
        $_POST['is_local'] = $myrow["is_local"];
        $_POST['cash_handover_dr_act'] = $myrow['cash_handover_dr_act'];


        $_POST['cashier_account'] = $myrow['cashier_account'];
        if (empty($myrow['cashier_account']))
            $_POST['cashier_account'] = get_default_bank_account('AED');

        $_POST['govt_credit_account'] = $myrow["govt_credit_account"];
        $permitted_cats = $myrow['permitted_categories'];


        $_POST['user_language'] = $myrow["user_language"];
        $_POST['dflt_dimension_id'] = $myrow["dflt_dimension_id"];
        // $_POST['purch_req_send_to'] = $myrow["purch_req_send_to"];

        $_POST['purch_req_send_to_level_one'] = $myrow["purch_req_send_to_level_one"];
        $_POST['purch_req_send_to_level_two'] = $myrow["purch_req_send_to_level_two"];

        // $purch_req_send_to=0;
        // if (!empty($_POST['purch_req_send_to']))
        //     $purch_req_send_to = get_user($_POST['purch_req_send_to'])['user_id'];


        $purch_req_send_to_level_one=0;
        if (!empty($_POST['purch_req_send_to_level_one']))
            $purch_req_send_to_level_one = get_user($_POST['purch_req_send_to_level_one'])['user_id'];


        $purch_req_send_to_level_two=0;
        if (!empty($_POST['purch_req_send_to_level_two']))
            $purch_req_send_to_level_two = get_user($_POST['purch_req_send_to_level_two'])['user_id'];


        $_POST['ip_restriction'] = $myrow["ip_restriction"];

    }
    hidden('selected_id', $selected_id);
    hidden('user_id');

    start_row();
    label_row(trans("User login:"), $_POST['user_id']);
} else { //end of if $selected_id only do the else when a new record is being entered

    text_row(trans("User Login:"), "user_id", null, 22, 20);
    $_POST['language'] = user_language();
    $_POST['print_profile'] = user_print_profile();
    $_POST['rep_popup'] = user_rep_popup();
    $_POST['pos'] = user_pos();
}
$_POST['password'] = "";
password_row(trans("Password:"), 'password', $_POST['password']);

if ($selected_id != -1) {
    table_section_title(trans("Enter a new password to change, leave empty to keep current."));
}

text_row_ex(trans("Full Name").":", 'real_name',  50);

//text_row_ex(trans("Telephone No.:"), 'phone', 30);

//email_row_ex(trans("Email Address:"), 'email', 50);

$select_opt = array(
    "1" => "YES",
    "0" => "NO",
);
echo '<tr><td class="label">IP RESTRICTION</td><td>' . array_selector('ip_restriction', $_POST['ip_restriction'], $select_opt, $options) . '</td> </tr>';


security_roles_list_row(trans("Access Level:"), 'role_id', null);

bank_accounts_list_row(trans("Cashier Account"), 'cashier_account', $_POST['cashier_account'], false, 'N/A');

gl_all_accounts_list_row(trans("E-Dirham Card / Govt. Credit A/C"), 'govt_credit_account', $_POST['govt_credit_account'], false, false, "Select an Account", false, false, 15);

gl_all_accounts_list_row(trans("Cash Handover DR A/C"), 'cash_handover_dr_act', $_POST['cash_handover_dr_act'], false, false, "--select--");

//languages_list_row(trans("Language:"), 'language', null);

hidden('language', $_POST['language']);

//hidden('real_name', $myrow["real_name"]);
hidden('phone', $myrow["phone"]);
hidden('email', $myrow["email"]);


$options = array('select_submit' => true, 'disabled' => null, 'id' => 'user_language');
$select_opt = array(
    "EN" => "ENGLISH",
    "AR" => "ARABIC"
);
//echo '<tr><td class="label">User Language </td><td>' . array_selector('user_language', $_POST['user_language'], $select_opt, $options) . '</td> </tr>';


dimensions_list_row(trans('Default Cost Center'), 'dflt_dimension_id', $_POST['dflt_dimension_id'], true, '-No Applicable-');


users_list_cells(trans('Level One - Purchase Request Send To') . ":", 'purch_req_send_to_level_one',$purch_req_send_to_level_one);

echo "</tr>";
users_list_cells(trans('Level Two - Purchase Request Send To') . ":", 'purch_req_send_to_level_two',$purch_req_send_to_level_two);


pos_list_row(trans("User's POS") . ':', 'pos', null);

print_profiles_list_row(trans("Printing profile") . ':', 'print_profile', null,
    trans('Browser printing support'));

check_row(trans("Use popup window for reports:"), 'rep_popup', $_POST['rep_popup'],
    false, trans('Set this option to on if your browser directly supports pdf files'));


check_row(trans("Local Nationality ? :"), 'is_local', $_POST['is_local'],
    false, trans('Set this option if the user is a local nationality'));


start_row();

//label_cell(trans("Permitted Categories:"),"class='label'");

//stock_categories_list_row(trans("Categories"),'permitted_categories');


//echo "<select class=\"js-example-basic-multiple\" name=\"states[]\" multiple=\"multiple\">
//  <option value=\"AL\">Alabama</option>
//    ...
//  <option value=\"WY\">Wyoming</option>
//</select>";


label_cell(trans("Permitted Categories for invoicing:"), "class=label");

$permitted_cats = explode(",", $permitted_cats);

$sql = "SELECT category_id, description, inactive FROM " . TB_PREF . "stock_category";
$where_opts = array();
$where_opts[0] = "dflt_mb_flag!='F'";
echo "<td>" . combo_input('permitted_categories', $permitted_cats, $sql, 'category_id', 'description',
        array('order' => 'category_id',
            'spec_option' => false,
            'spec_id' => -1,
            'select_submit' => false,
            'multi' => true,
            'async' => true,
            'where' => $where_opts
        )) . "</td>";


end_row();


end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_collapsible_div();

/**------------------------------------------*/

br();

start_table(TABLESTYLE);

$th = array(trans("User login"), trans("Full Name"), trans("Phone"),
    trans("E-mail"), trans("Last Visit"), trans("Access Level"), "", "");

inactive_control_column($th);
table_header($th);

$k = 0; //row colour counter

while ($myrow = db_fetch($result)) {

    alt_table_row_color($k);

    $last_visit_date = sql2date($myrow["last_visit_date"]);

    /*The security_headings array is defined in config.php */
    $not_me = strcasecmp($myrow["user_id"], $_SESSION["wa_current_user"]->username);

    label_cell($myrow["user_id"]);
    label_cell($myrow["real_name"]);
    label_cell($myrow["phone"]);
    email_cell($myrow["email"]);
    label_cell($last_visit_date, "nowrap");
    label_cell($myrow["role"]);

    if ($not_me)
        inactive_control_cell($myrow["id"], $myrow["inactive"], 'users', 'id');
    elseif (check_value('show_inactive'))
        label_cell('');

    edit_button_cell("Edit" . $myrow["id"], trans("Edit"));
    if ($not_me)
        delete_button_cell("Delete" . $myrow["id"], trans("Delete"));
    else
        label_cell('');
    end_row();

} //END WHILE LIST LOOP

inactive_control_row($th);
end_table(1);

end_form();
end_page();

?>
<style>
    #permitted_categories {
        height: 143px !important;
    }

    .tablestyle2 td:first-child {
        text-align: right;
    }
</style>

<script>

    // alert(1)
    $(document).ready(function () {

        var cats = '<?php $permitted_cats ?>';

        var psdy = 123;

        console.log(cats);

        // var permitted_categories_select=$("select[name='permitted_categories[]']").select2();
        // permitted_categories_select.val(["1", "4"]);
        // permitted_categories_select.trigger("change");
    });

</script>
