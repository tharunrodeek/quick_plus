

$(document).ready(function () {

    // get_current_QMS_token();


    // getUnreadNotificationCount();


    // setInterval(function () {

    //     getUnreadNotificationCount();

    // }, 3000);


    $(".axispro-lang-btn").click(function (e) {

        /*var lang = $(this).data("lang");

        KTApp.blockPage({
            overlayColor: 'blue',
            type: 'v2',
            state: 'primary',
            size: 'lg',
            message: 'Changing Language. Please wait...'
        });

        AxisPro.APICall('POST', ERP_FUNCTION_API_END_POINT, {
            method: 'change_language',
            format: 'json',
            lang: lang
        }, function (data) {

            setTimeout(function () {

                if (data.status === 'OK') {
                    window.location.reload();
                }
            }, 3000);

        });*/

    });


    $("#notification_icon").click(function () {


        getNotifications();

    });


});


function getNotifications() {


    $.ajax(ERP_FUNCTION_API_END_POINT, {
        method: 'GET',
        data: {
            method: 'getNotifications',
            format: 'json',
            status: 0
        },
        dataType: 'json'

    }).done(function (data) {

        if (data.length > 0) {

            $("#notification_popup").html("");

            $("#no_new_notification_div").hide();

            $.each(data, function (key, val) {

                var link = '#';

                if (val.link != '')
                    link = BASE_URL + val.link;

                var notification_html = '<a href="' + link + '" class="kt-notification__item">' +
                    '                                        <div class="kt-notification__item-icon">' +
                    '                                            <i class="flaticon2-line-chart kt-font-success"></i>' +
                    '                                        </div>' +
                    '                                        <div class="kt-notification__item-details">' +
                    '                                            <div class="kt-notification__item-title">' + val.description + '</div>' +
                    '                                            <div class="kt-notification__item-time">' + val.time_ago + '</div>' +
                    '                                        </div>' +
                    '                                    </a>'

                $("#notification_popup").prepend(notification_html);


            });

        }

        else {
            $("#no_new_notification_div").show();
        }

    });


}

function getUnreadNotificationCount() {

    return;
    $.ajax(ERP_FUNCTION_API_END_POINT, {
        method: 'GET',
        data: {
            method: 'getUnreadNotificationCount',
            format: 'json',
        },
        dataType: 'json'

    }).done(function (data) {

        if (data) {

            console.log(data);

            $("#notification_count").html(data.data);

            $("#common_notification").html("");

            var common_alerts = "";

            var common_notifications = data.common_alerts;

            if(common_notifications.length > 0)
                $("#common_notify_div").show();
            else
                $("#common_notify_div").hide();

            $.each(data.common_alerts, function (key, val) {

                common_alerts += val+"   ";

            });


            $("#common_notification").html(common_alerts);

        }

    });


    // AxisPro.APICall('GET', ERP_FUNCTION_API_END_POINT, {
    //     method: 'getUnreadNotificationCount',
    //     format: 'json',
    // }, function (data) {
    //     if (data) {
    //         $("#notification_count").html(data.data);
    //
    //         $("#common_notification").html("");
    //
    //         var common_alerts = "";
    //
    //         $.each(data.common_alerts, function (key, val) {
    //
    //             common_alerts += val+"   ";
    //
    //         });
    //
    //
    //         $("#common_notification").html(common_alerts);
    //
    //     }
    // });

}


function get_current_QMS_token(callback) {

    //


}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}




