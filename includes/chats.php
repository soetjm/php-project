<?php
$arr['userid'] = "null";
if (isset($DATA_OBJ->find->userid)) {
    $arr['userid'] = $DATA_OBJ->find->userid;
}
$refresh = false;
$seen = false;
if ($DATA_OBJ->data_type == "chats_refresh") {
    $refresh = true;
    $seen = $DATA_OBJ->find->seen;
}

$sql = "SELECT * FROM users where user_id = :userid limit 1";
$result = $DB->read($sql, $arr);

if (is_array($result)) {
    //user found
    $result = $result[0];
    $image = ($result->gender == "Male") ? "ui/images/male.jpg" : "ui/images/female.jpg";
    if (file_exists($result->image)) {
        $image = $result->image;
    }
    $result->image = $image;
    $myData = "";
    if (!$refresh) {
        $myData = "Now Chat With: <br>
            <div id='active_contact' >
            <img src='$image' alt='' srcset=''>
            <p>{$result->username}</p>
            </div>";
    }
    $messages = "";
    $new_message = false;
    if (!$refresh) {
        $messages = "
        <div id='messages_holder_parent' onclick='set_seen(event)' style='height:440px;'>
        <div id='messages_holder' style='overflow-y:scroll;height:390px;'>";
    }
    $a['sender'] = $_SESSION['userid'];
    $a['receiver'] = $arr['userid'];
    $sql = "SELECT * FROM messages where (sender = :sender && receiver = :receiver && deleted_sender=0) || (receiver = :sender && sender = :receiver && deleted_receiver=0) order by id desc limit 10";
    $result2 = $DB->read($sql, $a);

    if (is_array($result2)) {
        $result2 = array_reverse($result2);
        foreach ($result2 as $data) {
            $myUser = $DB->get_user($data->sender);
            //chelk for new user
            if ($data->received == 0 && $data->receiver == $_SESSION['userid']) {
                $new_message = true;
            }
            if ($data->receiver == $_SESSION['userid'] && $data->received == 1 && $seen) {
                $DB->write("UPDATE messages set seen = 1 where id='$data->id' limit 1");
            }
            if ($data->receiver == $_SESSION['userid']) {
                $DB->write("UPDATE messages set received = 1 where id='$data->id' limit 1");
            }
            if ($_SESSION['userid'] == $data->sender) {
                $messages .= message_right($data, $myUser);
            } else {
                $messages .= message_left($data, $myUser);
            }
        }
    }

    if (!$refresh) {
        $messages .= message_controls();
    }
    $info->user = $myData;
    $info->messages = $messages;
    $info->data_type = 'chats';
    $info->new_message = $new_message;
    if ($refresh) {
        $info->data_type = 'chats_refresh';
    }
    echo json_encode($info);
} else {
    $a['userid'] = $_SESSION['userid'];
    // $a['receiver'] = $arr['userid'];
    $sql = "SELECT * FROM messages where (sender = :userid || receiver = :userid) group by msgid order by id desc limit 10";
    $result2 = $DB->read($sql, $a);
    $myData = "Previous Chat: <br>";
    if (is_array($result2)) {
        $result2 = array_reverse($result2);
        foreach ($result2 as $data) {
            $other_user = $data->sender;
            if ($data->sender == $_SESSION['userid']) {
                $other_user = $data->receiver;
            }
            $myUser = $DB->get_user($other_user);

            $image = ($myUser->gender == "Male") ? "ui/images/male.jpg" : "ui/images/female.jpg";
            if (file_exists($myUser->image)) {
                $image = $myUser->image;
            }
            $myData .= "
            <div id='active_contact' userid='$myUser->user_id' onclick='start_chat(event)' style='cursor:pointer'>
            <img src='$image' alt='' srcset=''>
            <p>{$myUser->username}</p>
            <span style='font-size'>'$data->message'</span>
            </div>";
        }
    }
    $info->user = $myData;
    $info->messages = "";
    $info->data_type = 'chats';
    echo json_encode($info);
}
