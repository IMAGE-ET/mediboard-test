<?php

/**
 * dPcabinet
 *  
 * @category dPdcabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

global $date, $chir_id, $print;

$date    = CValue::get("date");
$chir_id = CValue::get("chir_id");
$print   = 1;

echo CApp::fetch("dPcabinet", "inc_plage_selector_weekly");
