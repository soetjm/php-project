<?php
$info = (object)[];
$data = false;

//validet info
$data['user_id'] = $_SESSION['userid'];



if ($Error == "") {

    $query = "SELECT * FROM users WHERE user_id = :user_id limit 1";
    $result = $DB->read($query, $data);
    if (is_array($result)) {
        $result = $result[0];
        $result->data_type = 'user_info';

        //check if image is exists
        $image = ($result->gender == "Male") ? "ui/images/male.jpg" : "ui/images/female.jpg";
        if (file_exists($result->image)) {
            $image = $result->image;
        }

        $result->image = $image;
        echo json_encode($result);
        // $result->logged_in = true;
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
