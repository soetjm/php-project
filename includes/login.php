<?php
$info = (object)[];
$data = false;

//validet info
$data['email'] = $DATA_OBJ->email;

if (empty($DATA_OBJ->email)) {
    $Error = "Please Enter Your Email";
}
if (empty($DATA_OBJ->password)) {
    $Error = "Please Enter Your Password";
}

if ($Error == "") {

    $query = "SELECT * FROM users WHERE email = :email limit 1";
    $result = $DB->read($query, $data);
    if (is_array($result)) {
        $result = $result[0];
        if ($result->password == $DATA_OBJ->password) {
            $_SESSION['userid'] = $result->user_id;
            $info->message = 'your are succesfuly loggedin';
            $info->data_type = 'info';
            echo json_encode($info);
        } else {
            $info->message = 'Wrong password';
            $info->data_type = 'error';
            echo json_encode($info);
        }
    } else {
        $info->message = 'Wrong Email';
        $info->data_type = 'error';
        echo json_encode($info);
    }
} else {
    $info->message = $Error;
    $info->data_type = 'Error';
    echo json_encode($info);
}
