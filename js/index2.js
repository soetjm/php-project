function collect_data() {
    var save_settings_button = _("save_settings_button");
    save_settings_button.disabled = true;
    save_settings_button.value = "Loding...Please wait..";
    var myform = _("myform");
    var inputs = myform.getElementsByTagName("INPUT");
    var data = {};
    for (let i = inputs.length - 1; i >= 0; i--) {
        var key = inputs[i].name;

        switch (key) {

            case "username":
                data.username = inputs[i].value;
                break;
            case "email":
                data.email = inputs[i].value;
                break;
            case "gender":
                if (inputs[i].checked) {
                    data.gender = inputs[i].value;
                }
                break;
            case "password":
                data.password = inputs[i].value;
                break;
            case "password2":
                data.password2 = inputs[i].value;
                break;
        }
    }
    send_data(data, "save_settings")
}

function send_data(data, type) {
    var xml = new XMLHttpRequest

    xml.onload = function() {

        if (xml.readyState == 4 || xml.status == 200) {
            handel_result(xml.responseText, type);
            var save_settings_button = _("save_settings_button");
            save_settings_button.disabled = false;
            save_settings_button.value = "Sign up";
        }
    }
    data.data_type = type;
    var data_string = JSON.stringify(data);
    xml.open("POST", "api.php", true);
    xml.send(data_string);
}

function upload_profile_image(files) {
    // alert(files[0].name);
    var change_image_button = _("change_image_button");
    change_image_button.disabled = true;
    change_image_button.innerHTML = "Uploading Image.... ";

    var myForm = new FormData();

    var xml = new XMLHttpRequest

    xml.onload = function() {

        if (xml.readyState == 4 || xml.status == 200) {
            // alert(xml.responseText);
            get_data({}, "user_info");
            get_settings(true);
            //var change_image_button = _("change_image_button");
            change_image_button.disabled = false;
            change_image_button.innerHTML = "Change Image";
        }
    }
    myForm.append("file", files[0]);
    myForm.append("data_type", 'change_profile_image');
    xml.open("POST", "upload.php", true);
    xml.send(myForm);
}

function handel_drag_and_drop(e) {
    if (e.type == "dragover") {
        e.preventDefault();
        e.target.className = "dragging";
    } else if (e.type == "dragleave") {
        e.target.className = "";
    } else if (e.type == "drop") {
        e.preventDefault();
        e.target.className = "";
        upload_profile_image(e.dataTransfer.files)
    } else {
        e.target.className = "";
    }
}

function start_chat(e) {
    var userid = e.target.getAttribute('userid');
    if (e.target.id == "") {
        userid = e.target.parentNode.getAttribute('userid');
    }
    CURRENT_CHAT_USER = userid;
    var radio_chat = _("radio_chat");
    radio_chat.checked = true;
    get_data({
        userid: CURRENT_CHAT_USER
    }, "chats")
}

function send_image(files) {
    var file = files[0];
    var fileName = files[0].name
    var extIndex = fileName.lastIndexOf(".");
    var extName = fileName.substring(extIndex + 1).toUpperCase();
    if (extName != "JPG" || extName == "PNG") {
        alert('upload only jpg or png file')
        return
    };
    var myForm = new FormData();

    var xml = new XMLHttpRequest

    xml.onload = function() {

        if (xml.readyState == 4 || xml.status == 200) {
            handel_result(xml.responseText, "send_image");
            get_data({
                userid: CURRENT_CHAT_USER,
                seen: SEEN_STATUS
            }, "chats_refresh")
        }
    }
    myForm.append("file", files[0]);
    myForm.append("data_type", 'send_image');
    myForm.append("userid", CURRENT_CHAT_USER);
    xml.open("POST", "upload.php", true);
    xml.send(myForm);
}

function close_image(e) {
    e.target.className = 'iamge_off';
}

function image_show(e) {
    var image = e.target.src.split('uploades')[1];
    image[image.length - 1] = "";
    // alert(image);
    var image_viewer = _("image_vewer");
    image_viewer.innerHTML = `<img style="width:100%;height:100%;z-index:8;" src="uploades${image}"/>`
    image_viewer.className = 'image_on';
}