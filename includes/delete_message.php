<?php
$arr['rowid'] = "null";
if (isset($DATA_OBJ->find->rowid)) {
    $arr['rowid'] = $DATA_OBJ->find->rowid;
}

$sql = "SELECT * FROM messages where id = :rowid limit 1";
$result = $DB->read($sql, $arr);

if (is_array($result)) {
    $row = $result[0];
    if ($_SESSION['userid'] == $row->sender) {
        $sql = "UPDATE  messages SET deleted_sender = 1 where id = '$row->id' limit 1";
        $DB->write($sql);
    }
    if ($_SESSION['userid'] == $row->receiver) {
        $sql = "UPDATE  messages SET deleted_receiver = 1 where id = '$row->id' limit 1";
        $DB->write($sql);
    }
}
