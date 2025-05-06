<?php
$info = (object)[];
$data = false;
$data['user_id'] = $_SESSION['userid'];
// $data['date'] = date("Y-m-d H:i:s");
$data['username'] = $DATA_OBJ->username;

if (empty($DATA_OBJ->username)) {
    $Error .= "Please Enter Valid UserName.<br>";
} else {
    if (strlen($DATA_OBJ->username) < 3) {

        $Error .= "username must be at lest 3 character.<br>";
    }

    if (!preg_match("/^[a-z A-Z]*$/", $DATA_OBJ->username)) {
        $Error .= "Please Enter Valid UserName.<br>";
    }
}

$data['email'] = $DATA_OBJ->email;

if (empty($DATA_OBJ->email)) {
    $Error .= "Please Enter Valid email.<br>";
} else {
    if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $DATA_OBJ->email)) {
        $Error .= "Please Enter Valid email.<br>";
    }
}
$data['gender'] = isset($DATA_OBJ->gender) ? $DATA_OBJ->gender : null;

if (empty($DATA_OBJ->gender)) {
    $Error .= "Please Select gender.<br>";
} else {
    if ($DATA_OBJ->gender != "Male" && $DATA_OBJ->gender != "Female") {
        $Error .= "Please select valid gender.<br>";
    }
}
$data['password'] = $DATA_OBJ->password;
$password = $DATA_OBJ->password2;

if (empty($DATA_OBJ->password)) {
    $Error .= "Please Enter Valid password.<br>";
} else {
    if ($DATA_OBJ->password != $DATA_OBJ->password2) {

        $Error .= "password must be match.<br>";
    }
    if (strlen($DATA_OBJ->password) < 6) {

        $Error .= "password must be at lest 6 character.<br>";
    }
}

if ($Error == "") {

    // $query = "INSERT INTO users(user_id,username,gender,email,password,date)  
    // VALUES(:user_id,:username,:gender,:email,:password,:date)";
    $query = "UPDATE users set username=:username,gender=:gender,email=:email,password=:password  WHERE user_id=:user_id limit 1";
    $result = $DB->write($query, $data);
    if ($result) {
        $info->message = 'Your data was  saved';
        $info->data_type = 'save_settings';
        echo json_encode($info);
    } else {
        $info->message = 'Your data was not add due to error';
        $info->data_type = 'save_settings';
        echo json_encode($info);
    }
} else {
    $info->message = $Error;
    $info->data_type = 'save_settings';
    echo json_encode($info);
}
