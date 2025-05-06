var send_audio = new Audio("message_sent.mp3");
    var receved_audio = new Audio("message_received.mp3");
    var CURRENT_CHAT_USER = "";
    var SEEN_STATUS = false;

    function _(element) {
        return document.getElementById(element)
    }

    var label_contact = _("label_contact");
    label_contact.addEventListener("click", get_contacts);

    var label_chat = _("label_chat");
    label_chat.addEventListener("click", get_chats);

    var label_setting = _("label_setting");
    label_setting.addEventListener("click", get_settings);

    var logout = _("logout");
    logout.addEventListener("click", logout_user);

    function get_data(find, type) {

        var xml = new XMLHttpRequest();
        var loader_holder = _("loader_hold");
        loader_holder.className = "loader_on";
        xml.onload = function() {

            if (xml.readyState == 4 || xml.status == 200) {
                // alert(xml.responseText);
                loader_holder.className = "loader_off";
                handel_result(xml.responseText, type);
            }

        }

        var data = {};
        data.find = find;
        data.data_type = type;
        data = JSON.stringify(data);
        xml.open("POST", "api.php", true);
        xml.send(data)
    }

    function handel_result(result, type) {
        // alert(result)
        if (result.trim() != "") {
            var inner_right_pannel = _("inner_right_pannel");
            inner_right_pannel.style.overflow = 'visible';
            var obj = JSON.parse(result);
            if (typeof(obj.logged_in) != "undefined" && !obj.logged_in) {
                window.location = 'login.php'; //remamber to change to login.php
            } else {
                // alert(result);
                switch (obj['data_type']) {
                    case "user_info":
                        var username = _("username");
                        var email = _("email");
                        var profile_image = _("profile_image");
                        username.innerHTML = obj['username'];
                        email.innerHTML = obj['email'];
                        profile_image.src = obj['image'];
                        break;
                    case 'contacts':

                        var inner_left_pannel = _("inner_left_pannel");
                        inner_right_pannel.style.overflow = 'hidden';
                        inner_left_pannel.innerHTML = obj['message'];
                        break;
                    case 'chats_refresh':
                        SEEN_STATUS = false;
                        var messages_holder = _("messages_holder");
                        messages_holder.innerHTML = obj['messages'];
                        if (typeof obj['new_message'] != 'undefined') {
                            if (obj['new_message']) {
                                receved_audio.play();
                                messages_holder.scrollTo(0, messages_holder.scrollHeight)
                            }
                        }
                        break;
                    case 'send_message':
                        send_audio.play();
                    case 'chats':
                        SEEN_STATUS = false;
                        var inner_left_pannel = _("inner_left_pannel");
                        inner_left_pannel.innerHTML = obj['user'];
                        inner_right_pannel.innerHTML = obj['messages'];

                        var messages_holder = _("messages_holder");
                        messages_holder.scrollTo(0, messages_holder.scrollHeight)
                        var message_text = _("message_text");
                        message_text.focus();
                        if (typeof obj['new_message'] != 'undefined') {
                            if (obj['new_message']) {
                                receved_audio.play();
                            }
                        }
                        break;
                    case 'settings':
                        var inner_left_pannel = _("inner_left_pannel");
                        inner_left_pannel.innerHTML = obj['message'];
                        break;
                    case 'send_image':
                        alert(obj['message']);
                        break;
                    case 'save_settings':
                        alert(obj['message']);
                        get_data({}, "user_info");
                        get_settings(true);
                        break;
                        // case 'send_message':
                        //     alert(obj['message']);
                        //     // get_data({}, "user_info");
                        //     // get_settings(true);
                        //     break;
                }
            }

        }
    }

    function logout_user() {
        var answer = confirm("Are you sure to log out");
        if (answer) {
            get_data({}, 'logout');
        }
    }

    get_data({}, "user_info");
    get_data({}, "contacts");

    var radio_contacts = _("radio_contact");
    radio_contacts.checked = true;

    function get_contacts(e) {
        get_data({}, "contacts")
    }

    function get_chats(e) {
        get_data({}, "chats")
        // alert("hi");
    }

    function get_settings(e) {
        get_data({}, "settings")
    }

    function send_message(e) {
        var message_text = _("message_text");
        if (message_text.value.trim() == "") {
            alert('please type something')
            return;
        }
        // alert(message_text.value);
        get_data({
            message: message_text.value.trim(),
            user_id: CURRENT_CHAT_USER
        }, "send_message")
    }

    function enter_pressed(e) {
        if (e.keyCode == 13) {
            send_message(e)
        }
        SEEN_STATUS = true;
    }
    setInterval(function() {
        var radio_chat = _("radio_chat");
        if (CURRENT_CHAT_USER != "" && radio_chat.checked) {
            get_data({
                userid: CURRENT_CHAT_USER,
                seen: SEEN_STATUS
            }, "chats_refresh")
        }
        // var radio_chat = _("radio_chat");
        // radio_chat.checked = true;
    }, 1000)

    function set_seen(e) {
        SEEN_STATUS = true;
    }

    function delete_message(e) {
        if (confirm("Are you sure you want to delete this message")) {
            var msgid = e.target.getAttribute("msgid")
            get_data({
                rowid: msgid
            }, "delete_message")

            get_data({
                userid: CURRENT_CHAT_USER,
                seen: SEEN_STATUS
            }, "chats_refresh")
        }
    }

    function delete_thread(e) {
        if (confirm("Are you sure you want to delete this whole thread??")) {
            get_data({
                userid: CURRENT_CHAT_USER,
            }, "delete_thread")

            get_data({
                userid: CURRENT_CHAT_USER,
                seen: SEEN_STATUS
            }, "chats_refresh")
        }
    }


    // var label=_('label_chat');
    // label.addEventListener("click",function(){
    //     var inner_pannel=_("inner_left_pannel");
    //     var ajax=new XMLHttpRequest();
    //     ajax.onload=function(){
    //         if(ajax.status==200 || ajax.readyState == 4){
    //             inner_pannel.innerHTML=ajax.responseText
    //         }
    //     }
    //     ajax.open("POST","file.php",true);
    //     ajax.send()
    // })