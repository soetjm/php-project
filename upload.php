<?php
// Start the session
session_start();

// Initialize response object
$info = (object)[];

if (!isset($_SESSION['userid'])) {
    if ((isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type != "login" && $DATA_OBJ->data_type != "signup")) {
        $info->logged_in = false;
        echo json_encode($info);
        die;
    }
}

// Include necessary classes and DB initialization
require_once __DIR__ . '/classes/autoload.php';
$DB = new DataBase();

// Get the data type (change_profile_image or send_image)
$data_type = isset($_POST['data_type']) ? $_POST['data_type'] : "";

// Initialize destination variable
$distination = "";

// Check if a file is uploaded
if (isset($_FILES['file']) && $_FILES['file']['name'] != "") {

    // Allowed file types
    $allowed = ['image/jpeg', 'image/png', 'image/gif'];

    // Check for errors in file upload
    if ($_FILES['file']['error'] == 0) {

        // Validate file type
        if (!in_array($_FILES['file']['type'], $allowed)) {
            $info->message = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
            echo json_encode($info);
            die;
        }

        // Validate file size (limit to 5MB)
        if ($_FILES['file']['size'] > 5000000) {
            $info->message = "File size exceeds the 5MB limit.";
            echo json_encode($info);
            die;
        }

        // Create upload directory if it doesn't exist
        $folder = 'uploads/';
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        // Generate a unique name for the uploaded file
        $distination = $folder . uniqid() . "_" . basename($_FILES['file']['name']);

        // Move the uploaded file to the destination folder
        if (move_uploaded_file($_FILES['file']['tmp_name'], $distination)) {
            $info->message = "Your Image is successfully uploaded.";
            $info->data_type = $data_type;
            echo json_encode($info);
        } else {
            $info->message = "There was an error moving the uploaded file.";
            echo json_encode($info);
            die;
        }
    } else {
        $info->message = "There was an error with the file upload.";
        echo json_encode($info);
        die;
    }
}

if ($data_type == "change_profile_image") {
    if ($distination != "") {
        // Update the user's profile image in the database
        $id = $_SESSION['userid'];
        $quary = "UPDATE users SET image = '$distination' WHERE user_id = '$id' LIMIT 1";
        $DB->write($quary, []);
    }
} else if ($data_type == "send_image") {
    $arr['userid'] = "null";
    if (isset($_POST['userid'])) {
        $arr['userid'] = addslashes($_POST['userid']);
    }
    $arr['message'] = "";
    $arr['date'] = date('Y-m-d H:i:s');
    $arr['sender'] = $_SESSION['userid'];
    $arr['msgid'] = get_random_string_max(60);
    $arr['file'] = $distination;

    // Check if there's an existing conversation between sender and receiver
    $arr2['sender'] = $_SESSION['userid'];
    $arr2['receiver'] = $arr['userid'];
    $sql = "SELECT * FROM messages WHERE (sender = :sender AND receiver = :receiver) OR (receiver = :sender AND sender = :receiver) LIMIT 1";
    $result2 = $DB->read($sql, $arr2);

    // If conversation exists, use the existing msgid
    if (is_array($result2)) {
        $arr['msgid'] = $result2[0]->msgid;
    }

    // Insert the new message into the database
    $quary = "INSERT INTO messages (sender, receiver, message, date, msgid, files) VALUES (:sender, :userid, :message, :date, :msgid, :file)";
    $DB->write($quary, $arr);
}

/**
 * Generates a random string with the specified maximum length
 * 
 * @param int $length Maximum length of the random string
 * @return string Random string
 */
function get_random_string_max($length)
{
    $array = array(
        0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
    );
    $text = "";
    $length = rand(4, $length);
    for ($i = 0; $i < $length; $i++) {
        $random = rand(0, 61);
        $text .= $array[$random];
    }
    return $text;
}

