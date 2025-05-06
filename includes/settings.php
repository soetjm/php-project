
<?php
$sql = "SELECT * FROM users WHERE user_id=:user_id limit 1 ";
$id = $_SESSION['userid'];
$data = $DB->read($sql, ['user_id' => $id]);
$myData = "";
if (is_array($data)) {
    $data = $data[0];

    //check if image exists 
    $image = ($data->gender == "Male") ? "ui/images/male.jpg" : "ui/images/female.jpg";
    if (file_exists($data->image)) {
        $image = $data->image;
    }

    $gender_female = "";
    $gender_male = "";
    if ($data->gender == "Male") {
        $gender_male = "checked";
    } else {
        $gender_female = "checked";
    }
    $myData =
        '
    <style type="text/css">
      
        form {
            text-align:left;
            margin: auto;
            /* background-color: black; */
            padding: 10px;
            width: 100%;
            max-width: 400px;
        }

        input[type=text],
        [type=password],
        input[type=button],
        input[type=email] {
            padding: 10px;
            margin: 10px;
            width: 200px;
            border-radius: 5px;
            border: solid 1px gray;
        }

        input[type=button] {
            width: 220px;
            cursor: pointer;
            background-color: #2b5488;
            color: white;
        }

        input[type=radio] {
            transform: scale(1.1);
            cursor: pointer;
        }

        #error {
            text-align: center;
            padding: 0.5em;
            background-color: #ecaf91;
            color: #fff;
            max-width: 400px;
            margin: auto;
            display: none;
        }
        .dragging{
            border:dashed 2px #aaa;
        }
    </style>
        <div id="error">error</div>
        <div style="display:flex">
        <div>
            <span style="font-size:11px">drag and drop an image to change</span><br>
            <img ondragover="handel_drag_and_drop(event)" ondrop="handel_drag_and_drop(event)" ondragleave="handel_drag_and_drop(event)" src="' . $image . '" style="width:200px;height:200px;margin:10px;">
            <label for="change_image_input" id="change_image_button" style="background:#9b9a80;display:inline-block;padding:1em;border-radius:5px;cutsor:pointer">
                Change Image
                <input type="file" onchange="upload_profile_image(this.files)" id="change_image_input" style="display:none;">
            </label>
            
        </div>
        <form action="" id="myform">
            <input type="text" name="username" id="" placeholder="username" value="' . $data->username . '"><br>
            <input type="email" name="email" id="" placeholder="email" value="' . $data->email . '"><br>
            <div style="padding: 10px;">
                Gender:<br>
                <input type="radio" value="Male" name="gender" id="" ' . $gender_male . '>Male<br>
                <input type="radio" value="Female" name="gender" id="" ' . $gender_female . '>Female<br>
            </div>
            <input type="password" name="password" id="" placeholder="Password" value="' . $data->password . '"><br>
            <input type="password" name="password2" id="" placeholder="Retype Password" value="' . $data->password . '"><br>
            <!-- <input type="password" name="password2" id="" placeholder="Retype Password"><br> -->
            <input type="button" value="Save Settings" id="save_settings_button" onclick="collect_data(event)">
            <br>
            <br>
        </form>
        </div>


<!-- //part 12 is finesd next day part  -->


    
    ';

    // $result = $result[0];
    $info->message = $myData;
    $info->data_type = 'contacts';
    echo json_encode($info);

    // die;
} else {
    $info->message = 'No contact were found';
    $info->data_type = 'error';
    echo json_encode($info);
}
