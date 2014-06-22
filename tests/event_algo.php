<?php
/**
 * New event positionning algorithm, based on graphs
 *
 * Events have a name (key letter) and a range in hours
 * ---------------
 * 00h
 * 01h AAA
 * 02h AAA
 * 03h AAA BBBBBBB
 * 04h AAA CCC DDD
 * 05h AAA CCC DDD
 * 06h         DDD
 * 07h EEEEEEE DDD
 * 08h EEEEEEE DDD
 * 09h EEEEEEE
 * 10h
 * 11h
 * 12h FFFFF GGGGG
 * 13h FFFFF GGGGG
 * 14h FFFFF GGGGG
 * 15h FFFFF
 * 16h FFFFF HHHHH
 * 17h FFFFF HHHHH
 * 18h FFFFF HHHHH
 * 19h
 * 20h IIIIIIIIIII
 * 21h IIIIIIIIIII
 * 22h IIIIIIIIIII
 * 23h
 * ---------------
 *
 * Steps for positionning are:
 * 1. Sort by min then max for each events
 * 2. Gather all collisions
 * 3. Builds grapes
 * 4. For each grape, place events on columns
 * 5. Build positions for events
 */

error_reporting(E_ALL);

function trace($label, $value) {
  $export  = print_r($value, true);
  $time = date("Y-m-d H:i:s");
  echo "\n<pre>[$time] $label: $export</pre>";
}

function collide($event1, $event2) {
  return ($event1["lower"] < $event2["upper"] && $event2["lower"] < $event1["upper"]);
}

function compare($event1, $event2) {
  return $event1["lower"] != $event2["lower"] ?
    $event1["lower"] - $event2["lower"] :
    $event2["upper"] - $event1["upper"];
}

$events = array(
  "A" => array("lower" =>  1, "upper" =>  6),
  "B" => array("lower" =>  3, "upper" =>  4),
  "C" => array("lower" =>  4, "upper" =>  6),
  "D" => array("lower" =>  5, "upper" =>  9),
  "E" => array("lower" =>  7, "upper" => 10),
  "F" => array("lower" => 12, "upper" => 19),
  "G" => array("lower" => 12, "upper" => 15),
  "H" => array("lower" => 16, "upper" => 19),
  "I" => array("lower" => 20, "upper" => 23),
);

if (isset($_REQUEST["random"])) {
  $events = array();
  foreach (range(1, 20) as $_event_key) {
    $events[$_event_key]["lower"] = $lower = rand(0, 22);
    $events[$_event_key]["upper"] = $lower + rand(1, 3);
  }
}

// trace("Events", $events);

// 1. Sort by min then max for each events
usort($events, "compare");

// trace("Events", $events);

// 2. Gather all collision
$collisions = array();
foreach ($events as $key1 => $event1) {
  $collisions[$key1] = array();
  foreach ($events as $key2 => $event2) {
    if ($key1 !== $key2 && collide($event1, $event2)) {
      $collisions[$key1][$key2] = $key2;
    }
  }
}

// trace("Collisions", $collisions);

// 3. Builds grapes recursively

// Recurvise engraping function (collisions graph crawler)
function engrape(&$grapes, &$grapables, $collisions, $grape_key, $event_key) {
  // Event already in a grape
  if (!isset($grapables[$event_key])) {
    return;
  }

  // Put event in the current grape
  $grapes[$grape_key][$event_key] = $event_key;
  unset($grapables[$event_key]);

  // Recurse on colliding events for same grape
  foreach ($collisions[$event_key] as $_collider_key) {
    engrape($grapes, $grapables, $collisions, $grape_key, $_collider_key);
  }
}

$grapes = array();
$grapables = array_combine(array_keys($events), array_keys($events));
while (count($grapables)) {
  $grape_key = "grape-". count($grapes);
  $event_key = reset($grapables);
  engrape($grapes, $grapables, $collisions, $grape_key, $event_key);
}

// trace("Grapes", $grapes);

// 4. For each grape, place events on columns
$columns = array();
foreach ($grapes as $_grape_key => $_grape) {
  $columns[$_grape_key] = array();
  // Place events on actual columns
  foreach ($_grape as $_event_key) {
    // Trying to place event on the first available existing column
    foreach ($columns[$_grape_key] as $_column_key => $placed_event_keys) {
      if (!count(array_intersect($collisions[$_event_key], $placed_event_keys))) {
        $columns[$_grape_key][$_column_key][$_event_key] = $_event_key;
        continue 2;
      }
    }

    // No suitable column found, create one
    $column_key = count($columns[$_grape_key]);
    $columns[$_grape_key][$column_key][$_event_key] = $_event_key;
  }

}

// trace("Columns", $columns);

// 5. Build positions for events
$positions = array();
// Parse columns to prepare event positions
foreach ($grapes as $_grape_key => $_grape) {
  foreach ($columns[$_grape_key] as $_column_key => $_event_keys) {
    foreach ($_event_keys as $_event_key) {
      $positions[$_event_key] = array(
        "total" => count($columns[$_grape_key]),
        "start" => $_column_key,
        "end"   => count($columns[$_grape_key]),
      );
    }
  }
}

foreach ($positions as $_event_key => &$_position) {
  foreach ($collisions[$_event_key] as $_collider_key) {
    $collider_start = $positions[$_collider_key]["start"];
    if ($_position["start"] < $collider_start && $_position["end"] > $collider_start) {
      $_position["end"] = $collider_start;
    }
  }
}

// trace("Positions", $positions);

// Demonstration --------------------------------------------------------------
echo "
<style type='text/css'>
body {
  font-family: arial;
  font-size: 12px;
}

div.event {
  font-size: 10px;
  padding: 0 2px;
  position: fixed;
  border: 1px solid red;
  overflow: hidden;
}

div.hour {
  padding: 0 2px;
  position: fixed;
  border: 1px solid grey;
  border-width: 1px 0 1px 0;
  left: 520px;
  width: 70px;
  height: 17px;
}
</style>

<div>
  Add 'random' to HTTP parameters to see random demo in action (20 events, 1-3h per event)
</div>
";

foreach (range (0, 23) as $_hour) {
  $top = 100 + $_hour * 20;
  echo "
    <div class='hour' style='top: $top;'> $_hour:00</div>
  ";
}

foreach ($events as $_event_key => $_event) {
  $top    = 100 + $_event["lower"] * 20;
  $height = ($_event["upper"] - $_event["lower"]) * 20 - 3;
  $position = $positions[$_event_key];
  $left  = 600 + 300 * $position["start"] / $position["total"];
  $width = 300 * ($position["end"] - $position["start"]) / $position["total"] - 7;
  $lower = $_event["lower"];
  $upper = $_event["upper"];
  echo "
    <div class='event' style=' top: {$top}px; height: {$height}px; left: {$left}px; width: {$width}px; '>
      <strong title='$lower:00-$upper:00'>$_event_key</strong>: $lower:00-$upper:00
    </div>
 ";
}