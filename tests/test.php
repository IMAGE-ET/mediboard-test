<?php

include "../includes/errors.php";

// Ordered by start asc stop desc
$events = array(
  "A"  => array ("start" => "09:00", "stop" => "10:00"),
  "B"  => array ("start" => "10:00", "stop" => "11:00"),
  "C"  => array ("start" => "10:00", "stop" => "10:30"),
  "D"  => array ("start" => "10:30", "stop" => "12:30"),
  "H"  => array ("start" => "11:00", "stop" => "12:00"),
  "E"  => array ("start" => "11:00", "stop" => "13:00"),
//"7"  => array ("start" => "11:30", "stop" => "15:30"),
  "F"  => array ("start" => "14:00", "stop" => "15:00"),
  "G"  => array ("start" => "14:00", "stop" => "15:00"),
);

$positions = array();

function inside($value, $min, $max) {
  return $value > $min && $value < $max;
}

function collide($event1, $event2) {
  return
    inside($event1["start"], $event2["start"], $event2["stop"]) ||
    inside($event1["stop" ], $event2["start"], $event2["stop"]) ||
    inside($event2["start"], $event1["start"], $event1["stop"]) ||
    inside($event2["stop" ], $event1["start"], $event1["stop"]);
}

$columns = array();

foreach ($events as $name => $event) {
	foreach ($columns as $column => $names) {
		
	}
	$event 
}

?>