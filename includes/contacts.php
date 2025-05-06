<?php
$myId = $_SESSION['userid'];
$sql = "SELECT * FROM users where user_id != '$myId' limit 10";
$myuser = $DB->read($sql, []);

$myData =
    '<style>
        @keyframes appear{
            0%{opacity:0;transform:translateY(100px);}
            100%{opacity:1;transform:translateY(0px);}
        }
        #contact{
        cursor:pointer;
        transition:all .5s cubic-bezier(.78,.11,.42,.85);
        }
        #contact:hover{
        transform:scale(1.1);
        }
     </style>
    
    <div style="text-align: center;animation:appear 1s ease">';
if (is_array($myuser)) {
    foreach ($myuser as $row) {
        $image = ($row->gender == "Male") ? "ui/images/male.jpg" : "ui/images/female.jpg";
        if (file_exists($row->image)) {
            $image = $row->image;
        }
        $myData .= "<div id='contact' userid='$row->user_id' onclick='start_chat(event)'>
            <img src='$image' alt='' srcset=''>
            <br>{$row->username}
            </div>";
    }
}

$myData .= '
    </div>';

// $result = $result[0];
$info->message = $myData;
$info->data_type = 'contacts';
echo json_encode($info);

die;

$info->message = 'No contact were found';
$info->data_type = 'error';
echo json_encode($info);
