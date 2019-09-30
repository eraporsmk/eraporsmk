<?php
header('Content-Type: application/json');
$file = str_replace(".", "", $_GET['file']);
$file = 'storage/'.$file . ".txt";
if (file_exists($file)) {
	$text = file_get_contents($file);
	echo $text;
	$obj = json_decode($text);
	if ($obj->percent == 100) {
		unlink($file);
	}
} else {
	$json = array(
		"percent" => 0, 
		"message" => "Mempersiapkan pengiriman data", 
		"total" => 0, 
		"memory_usage" => NULL,
		"no"	=> 0,
		"response" => array(
			"text" => NULL, 
			"table" => NULL
		)
	);
	//file_put_contents("./assets/temp/" . session_id() . ".txt", json_encode($json));
	echo json_encode($json);
}
