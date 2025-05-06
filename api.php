<?php

session_start();


$DATA_RAW = file_get_contents("php://input");
$DATA_OBJ = json_decode($DATA_RAW);

//checked if logged in
$info = (object)[];
if (!isset($_SESSION['userid'])) {
    /*if ($_SERVER['PHP_SELF'] != "login.php") {*/
    if ((isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type != "login" && $DATA_OBJ->data_type != "signup")) {
        $info->logged_in = false;
        echo json_encode($info);
        die;
    }
}
// require __DIR__ . '/classes/database.php';
require_once __DIR__ . '/classes/autoload.php';

$DB = new DataBase();

//$DATA_OBJ = json_decode($data,true);//true is used to convert the php object to associtive array
// $DATA_OBJ = json_decode($DATA_RAW);

$Error = "";

if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "signup") {
    //signup

    include __DIR__ . '/includes/signup.php';
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "user_info") {

    //user info

    include __DIR__ . '/includes/user_info.php';
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "login") {

    //login

    include __DIR__ . '/includes/login.php';
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "logout") {

    //login

    include __DIR__ . '/includes/logout.php';
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "contacts") {

    //login

    include __DIR__ . '/includes/contacts.php';
} else if (isset($DATA_OBJ->data_type) && ($DATA_OBJ->data_type == "chats" || $DATA_OBJ->data_type == "chats_refresh")) {

    //login

    include __DIR__ . '/includes/chats.php';
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "settings") {

    //login

    include __DIR__ . '/includes/settings.php';
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "save_settings") {

    //login

    include __DIR__ . '/includes/save_settings.php';
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "send_message") {

    //login
    //include save message
    include __DIR__ . '/includes/send_message.php';
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "delete_message") {

    //login
    //include save message
    include __DIR__ . '/includes/delete_message.php';
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "delete_thread") {

    //login
    //include save message
    include __DIR__ . '/includes/delete_thread.php';
}

function message_left($data, $result)
{
    $image = ($result->gender == "Male") ? "ui/images/male.jpg" : "ui/images/female.jpg";
    if (file_exists($result->image)) {
        $image = $result->image;
    }
    $a = "
    <div id='message_left' >
    <div></div>
    <img id='prof_left' src='$image' alt='' srcset=''>
    <b>{$result->username}</b><br>
    $data->message<br>";
    if ($data->files != "" && file_exists($data->files)) {
        $a .= " <img src='$data->files' style='width:100%' onclick='image_show(event)/><br>";
    }
    $a .= "<span style='font-size:11px;color:#999'>" . date("jS M Y H:i:s a", strtotime($data->date)) . " </span>
     <img src='ui/icons/trash.png' id='trash' onclick='delete_message(event)' msgid='$data->id' />
    </div>
    ";
    return $a;
}

function message_right($data, $result)
{
    $image = ($result->gender == "Male") ? "ui/images/male.jpg" : "ui/images/female.jpg";
    if (file_exists($result->image)) {
        $image = $result->image;
    }
    $a = "
    <div id='message_right' style='width:50%' >";

    if ($data->seen) {
        $a .= "<div><img src='ui/icons/tick.png' style='' /></div>";
    } else if ($data->received) {
        $a .= "<div><img src='ui/icons/tick2.png' style='' /></div>";
    }
    $a .= "<img id='prof_img' src='$image' alt='' srcset='' style='float:right;'>
    <b>{$result->username}</b><br>
    $data->message<br>";
    if ($data->files != "" && file_exists($data->files)) {
        $a .= " <img src='$data->files' style='width:100%' onclick='image_show(event)'/><br>";
    }
    $a .= "<span style='font-size:11px;color:#999'>" . date("jS M Y H:i:s a", strtotime($data->date)) . "</span>
    <img src='ui/icons/trash.png' id='trash' onclick='delete_message(event)' msgid='$data->id' />
    </div>
    ";
    return $a;
}
function message_controls()
{
    return "
   </div>
        <span onclick='delete_thread(event)' style='color:purple;cursor:pointer;'>Delete This Thread</span>
            <div style='display:flex;width:100%;height:40px;'>
                <label for='message_file'><img src='ui/icons/clip.png' style='opacity:0.8;width:30px;margin:5px;cursor:pointer;'></label>
                <input type='file' id='message_file' name='file' style='display:none' onchange='send_image(this.files)'/>
                <input id='message_text' onkeyup='enter_pressed(event)' style='flex:6;border:solid thin #ccc;broder-bottom:none;font-size:14px;padding:4px;' type='text'placeholder='type your message' >
                <input style='flex:1;cursor:pointer;' type='button' value='send' onclick='send_message(event)'/>
            </div>

        </div>
    ";
}
