<?php
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");
header("retry: 2000");

$lastId = $_SERVER["HTTP_LAST_EVENT_ID"];
if (isset($lastId) && !empty($lastId) && is_numeric($lastId)) {
    $lastId = intval($lastId);
    $lastId++;
}
else{
$lastId=0;
}

$svg_oldData="";
$txt_oldData="";
$event_name="";

while (true) {
$file_svg = "data.json";
$file_txt = "data.txt";
$svg_data=file_get_contents($file_svg,TRUE);
$txt_data=file_get_contents($file_txt,TRUE);


    if ($svg_data != $svg_oldData) {
        $event_name = "svg_message";
        sendMessage($lastId, $svg_data, $event_name);
        $lastId++;
        $svg_oldData=$svg_data;
    }
    else if ($txt_data != $txt_oldData) {
        $event_name = "txt_message";
        sendMessage($lastId, $txt_data, $event_name);
        $lastId++;
        $txt_oldData=$txt_data;
    }
    sleep(0.1);
}

function sendMessage($id, $data, $event_name) {

    echo "event: $event_name\n";
    echo "id: $id\n";
    echo "data: $data\n\n";
    ob_flush();
    flush();
}
?>
