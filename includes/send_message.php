<?php
$arr['userid'] = "null";
if (isset($DATA_OBJ->find->user_id)) {
    $arr['userid'] = $DATA_OBJ->find->user_id;
}
$sql = "SELECT * FROM users where user_id = :userid limit 1";
$result = $DB->read($sql, $arr);

if (is_array($result)) {
    //user found
    $arr['message'] = $DATA_OBJ->find->message;
    $arr['date'] = date('Y-m-d H:i:s');
    $arr['sender'] = $_SESSION['userid'];
    $arr['msgid'] = get_random_string_max(60);

    $arr2['sender'] = $_SESSION['userid'];
    $arr2['receiver'] = $arr['userid'];
    $sql = "SELECT * FROM messages where (sender = :sender && receiver = :receiver) || (receiver = :sender && sender = :receiver) limit 1";
    $result2 = $DB->read($sql, $arr2);

    if (is_array($result2)) {
        $arr['msgid'] = $result2[0]->msgid;
    }
    $quary = "INSERT INTO messages (sender,receiver,message,date,msgid) VALUES (:sender,:userid,:message,:date,:msgid)";
    $DB->write($quary, $arr);
    $result = $result[0];
    $image = ($result->gender == "Male") ? "ui/images/male.jpg" : "ui/images/female.jpg";
    if (file_exists($result->image)) {
        $image = $result->image;
    }
    $result->image = $image;
    $myData = "Now Chat With: <br>
            <div id='active_contact' >
            <img src='$image' alt='' srcset=''>
            <p>{$result->username}</p>
            </div>";

    $messages = "
        <div id='messages_holder_parent' style='height:440px;'>
        <div id='messages_holder' style='overflow-y:scroll;height:390px;'>";

    //read from db
    // $arr2 = false;
    $a['msgid'] = $arr['msgid'];
    $sql = "SELECT * FROM messages where msgid = :msgid order by id desc  limit 10";
    $result2 = $DB->read($sql, $a);

    if (is_array($result2)) {
        $result2 = array_reverse($result2);
        foreach ($result2 as $data) {
            $myUser = $DB->get_user($data->sender);
            if ($_SESSION['userid'] == $data->sender) {
                $messages .= message_right($data, $myUser);
            } else {
                $messages .= message_left($data, $myUser);
            }
        }
    }
    $messages .= message_controls();

    $info->user = $myData;
    $info->messages = $messages;
    $info->data_type = 'send_message';
    echo json_encode($info);
} else {
    //user not found
    $info->message = 'That contact was not found';
    $info->data_type = 'send_message';
    echo json_encode($info);
}

function get_random_string_max($length)
{
    $array = array(
        0,
        1,
        2,
        3,
        4,
        5,
        6,
        7,
        8,
        9,
        'a',
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'i',
        'j',
        'k',
        'l',
        'm',
        'n',
        'o',
        'p',
        'q',
        'r',
        's',
        't',
        'u',
        'v',
        'w',
        'x',
        'y',
        'z',
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z'
    );
    $text = "";
    $length = rand(4, $length);
    for ($i = 0; $i < $length; $i++) {
        $random = rand(0, 61);
        $text .= $array[$random];
    }
    return $text;
}
