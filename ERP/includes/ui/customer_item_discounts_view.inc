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
include_once($path_to_root . '/includes/ui/simple_crud_class.inc');
include_once($path_to_root . "/includes/db/crm_contacts_db.inc");

/*
	View/Edit class for contacts
*/

class customer_item_discounts extends simple_crud
{
    var $id;
    var $entity;
    var $sub_class;
    var $class;

    function __construct($name, $id, $class, $subclass = null)
    {
        $fields = array('item_id', 'discount', 'customer_commission','reward_point');

        parent::__construct($name, $fields);
        $this->class = $class;
        $this->subclass = $subclass;
        $this->entity = $id;
    }

    function list_view()
    {
        br();
        $item_discounts = get_customer_discount_items($this->entity);
        start_table(TABLESTYLE, "width='80%'");
        $th = array(trans('Item Category Name'), trans("Discount Amount"), trans("Customer Commission"),trans("Reward Point"), '', "&nbsp;");
        table_header($th);
        $k = 0;

        $last = $myrow = db_fetch($item_discounts);

        while ($last) {

            if ($myrow['id'] != $last['id']) {
                alt_table_row_color($k);
                label_cell($last['description'], "style='text-align:center;'");
                label_cell($last['discount'], "style='text-align:center;'");
                label_cell($last['customer_commission'], "style='text-align:center;'");
                label_cell($last['reward_point'], "style='text-align:center;'");

                edit_button_cell("{$this->name}Edit[{$last['id']}]", trans("Edit"));

                // delete button removes only category unless this is the last contact for this person
                delete_button_cell("{$this->name}Delete[{$last['id']}]", trans("Delete"));
                end_row();
                $description = array();
                $last = $myrow;
            }

            if ($myrow) {
                $description[] = $myrow['description'];
                $myrow = db_fetch($item_discounts);
            }
        }

        //inactive_control_row($th);
        end_table(1);
    }

    function editor_view()
    {
        br();

        start_outer_table(TABLESTYLE);

        table_section(1);
        table_section_title(trans("Enter Item & Discount"));
        stock_categories_list_row(trans("Item Category:"), 'item_id', @$this->data['item_id'], false);
        text_row(trans("Discount Amount:"), 'discount', @$this->data['discount'], 35, 40);
        text_row(trans("Customer Commission:"), 'customer_commission', @$this->data['customer_commission'], 35, 40);
        text_row(trans("Reward Point:"), 'reward_point', @$this->data['reward_point'], 35, 40);

        end_outer_table(1);
    }

    function db_insert()
    {
        $dat = $this->data;
        return add_customer_item_discounts($dat["item_id"], $dat['discount'],
            $_POST['customer_commission'],$_POST['reward_point'], $this->entity);
    }

    function db_update()
    {
        return update_customer_item_discounts($this->selected_id, $_POST['item_id'], $_POST['customer_commission'],
            $_POST['discount'],$_POST['reward_point']);
    }

    function insert_check()
    {
        if (strlen($_POST['item_id']) == 0) {
            display_error(trans("Item Category cannot be empty"));
            set_focus('item_id');
            return false;
        }
//        if (strlen($_POST['reward_point']) == 0) {
//            display_error(trans("Reward Point cannot be empty."));
//            set_focus('discount');
//            return false;
//        }

        if (db_num_rows(check_duplicate_customer_item_discount($this->entity, $_POST['item_id'], $this->selected_id))) {
            display_error(trans("Customer has already assigned a reward point for this item"));
            set_focus('item_id');
            return false;
        }

        return true;
    }

    function db_read()
    {

        return get_customer_discount_item($this->selected_id);
    }

    function delete_check()
    {
        return true;
    }
    //
    //	Delete all contacts for person in current class/entity
    //
    function db_delete()
    {
        return delete_customer_item_discount($this->selected_id);

    }
}

