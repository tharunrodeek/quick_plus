<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 12/7/2019
 * Time: 4:27 PM
 * Created By : Bipin
 */

class AxisNotification
{

    /**
     * @param $id
     * @param $user_id
     * @return array|bool
     * Set read status to 1 for one notification
     */
    public static function makeRead($id, $user_id)
    {
        try {

            db_update('0_notification_users',
                [
                    "read=1",
                    "read_at=now()"
                ],
                [
                    "notification_id=$id",
                    "user_id=$user_id"
                ]
            );
//            $sql = "UPDATE 0_notification_users SET `read`=1, read_at=now() WHERE notification_id=$id,user_id=$user_id";
//            db_query($sql);

            return true;

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @param $user_id
     * @return array|bool
     * Set read status to 1 for all unread notifications
     */
    public static function makeReadAll($user_id)
    {
        try {

            db_update('0_notification_users',
                [
                    'read_status' => 1,
                    'read_at' => 'now()'
                ],
                [
                    "read_status = 0",
                    " AND ",
                    "user_id=$user_id"
                ]
            );

//            $sql = "UPDATE 0_notification_users SET `read_status`=1,read_at=now() WHERE `read_status`=0";
//            db_query($sql);

            return true;

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @param $data
     * @return array|bool|mysqli_result|resource
     * Insert new notification
     */
    public static function insert($data)
    {
        try {

            if (empty($data['users']))
                return false;

            begin_transaction();

            $insert_data = [
                'description' => db_escape($data['description']),
                'link' => isset($data['link']) && !empty($data['link']) ? db_escape($data['link']) : "#",
                'created_by' => $_SESSION['wa_current_user']->user
            ];

            db_insert('0_notifications', $insert_data);
            $notification_id = db_insert_id();

            $notification_users = [];
            foreach ($data['users'] as $recipient) {
                $notification_users[] = [
                    'user_id' => $recipient,
                    'notification_id' => $notification_id
                ];
            }

            db_insert_batch('0_notification_users', $notification_users);

            commit_transaction();

            return true;

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

    /**
     * @param null $status
     * @return array
     * Get All Notifications by read status
     */
    public static function getAll($status = null)
    {
        try {

            $user_id = $_SESSION['wa_current_user']->user;

            $sql = "select a.* from 0_notifications a 
            left join 0_notification_users b on b.notification_id=a.id 
            where /*b.`read_status`=$status and*/ b.user_id=$user_id order by b.id desc, b.read_status asc";
            $get = db_query($sql);

            $return_result = [];

            while ($myrow = db_fetch_assoc($get)) {

                $utc_created_at = $myrow['created_at'];

                //Shows Time Ago with UAE time UTC+4 Hrs
                $uae_time_ago = date("Y-m-d H:i:s", strtotime("$utc_created_at + 4 hours"));
                $myrow['time_ago'] = AxisPro::time_elapsed_string($uae_time_ago);

                $return_result[] = $myrow;
            }

            return $return_result;

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }
    }


    
    public static function getUnreadCount($user_id) {

        try {

            $sql = "select count(*) cnt from 0_notification_users WHERE user_id=$user_id and read_status=0";
            $get = db_query($sql);

            $result = db_fetch($get);

            return $result['cnt'];

        } catch (Exception $e) {
            return AxisPro::catchException($e);
        }

    }

}