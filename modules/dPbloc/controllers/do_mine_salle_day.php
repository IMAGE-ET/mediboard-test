<?php

$date     = CValue::post("date");
$salle_id = CValue::post("salle_id");

$salle_mine = new CDailySalleOccupation();
$salle_mine->mine($salle_id, $date);
if ($msg = $salle_mine->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}
else {
  CAppUI::setMsg("CDailySalleOccupation-msg-create");
}

echo CAppUI::getMsg();
CApp::rip();