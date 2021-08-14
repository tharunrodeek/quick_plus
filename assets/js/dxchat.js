var DXCHAT = {

    init: function () {
        // DXCHAT.showChatBox(1);
    },

    showChatBox: function (cbox_id) {

        var dxchatbox = '<button class="dxchat-open-button" data-cbox="1">Chat</button>' +
            '<div class="dxchat-chat-popup cbox-' + cbox_id + '">' +
            '  <form action="/action_page.php" class="dxchat-form-container">' +
            '    <h1 class="dxchat-title">' +
            '<img src="http://localhost/egfm/assets/media/logos/logo-10.png" class="dxchat-logo"/>' +
            '' +
            'AxisPro - CHAT</h1>' +
            '    <label for="msg"><b>Message</b></label>' +
            '     <div class="dxchat-contents">' +

            '<div class="dxchat sent">' +
            '<img src="//placehold.it/300" class="rounded-circle profile-img" />' +
            '<span class="contents">Hi</span>' +
            '</div>' +
            '' +
            '<div class="dxchat rcvd">' +
            '<img src="//placehold.it/300" class="rounded-circle profile-img" />' +
            '<span class="contents">Hello RCVD msg</span>' +
            '</div>' +
            '' +
            '</div>' +
            '    <textarea class="dxchat-msg cbox-' + cbox_id + '" placeholder="Type message.." name="dxchat-msg" required></textarea>' +
            '    <button type="button" class=" dxchat-send-button">Send</button>' +
            '    <button type="button" class=" cancel dxchat-close-button">Close</button>' +
            '  </form>' +
            '</div>';

        $('body').append(dxchatbox);


        var users_popup = '<div id="light" class="dxchat_popup_content">' +
            '<a href="javascript:void(0)" style="float: right" onclick="document.getElementById(\'light\').style.display=\'none\';document.getElementById(\'fade\').style.display=\'none\'">X</a>' +
            '<h3>AxisPro Chat - Users</h3>' +
            '<ul class="dxchat-users-ul">' +
            '    <li>' +
            '      <img src="https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y" >' +
            '      <h4>Bipin</h4>' +
            '       <p>System Administrator</p>' +
            '    </li>\n' +
            '      ' +
            '    <li>\n' +
            '      <img src="https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y" >' +
            '      <h4>Tharun</h4>' +
            '       <p>System Administrator</p>' +
            '    </li>' +
            '  </ul>' +
            '' +

            '  ' +
            '</div>' +
            '  <div id="fade" class="black_overlay"></div>';

        $('body').append(users_popup);

    },

    loadChat: function (cbox) {

        $(".cbox-" + cbox).show();
        var req_param = {
            method: 'dx_get_chat',
            from_id: 128,
            to_id: 1,
            type: 'PRIVATE',
            format: 'json'
        };
        var params = {
            method: 'GET',
            data: req_param
        };

        $.ajax(ERP_FUNCTION_API_END_POINT, params).done(function (r) {
            var result = JSON.parse(r);
            if (result) {
                var html_content = "";
                $.each(result, function (key, val) {
                    var cls = "rcvd";
                    if (val.from_id == 1)
                        cls = "sent";
                    html_content += '<div class="dxchat ' + cls + '">' +
                        '<img src="https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y" ' +
                        'class="rounded-circle profile-img" />' +
                        '<span class="contents">' + val.text + '</span>' +
                        '</div>';
                });

                $(".dxchat-contents").html(html_content);
                setTimeout(function () {
                    var d = $(".dxchat-contents");
                    d.scrollTop(d.prop("scrollHeight"));
                }, 2000);

                $(".dxchat-msg").focus();
            }
        });
    },

    send: function ($this) {

        var from_id = 1;//DUMMY
        var msg_input = $($this).parents(".dxchat-chat-popup").find("textarea");
        var msg = msg_input.val();

        if (msg.trim() === "")
            return false;

        var params = {
            method: 'POST',
            data: {
                method: 'dx_chat_send',
                from_id: from_id,
                to_id: 128,
                msg: msg
            }
        };

        $.ajax(ERP_FUNCTION_API_END_POINT, params).done(function (r) {

            var result = JSON.parse(r);
            if (result.status === 'OK') {

                var html_content = '<div class="dxchat sent">' +
                    '<img src="https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y" ' +
                    'class="rounded-circle profile-img" />' +
                    '<span class="contents">' + msg + '</span>' +
                    '</div>';

                var d = $(".dxchat-contents");
                d.append(html_content);
                d.scrollTop(d.prop("scrollHeight"));

                msg_input.val("");
            }
        });


    }

};


$(document).ready(function (e) {
    DXCHAT.init();
});

$(document).on("click", ".dxchat-close-button", function () {
    $(this).parents('.dxchat-chat-popup').hide();
});


$(document).on("click", ".dxchat-send-button", function () {
    var $this = this;
    DXCHAT.send($this);
});


$(document).on("click", ".dxchat-open-button", function () {
    var $this = this;
    var cbox = $($this).data('cbox');
    DXCHAT.loadChat(cbox);

    document.getElementById('light').style.display = 'block';
    document.getElementById('fade').style.display = 'block';

});


