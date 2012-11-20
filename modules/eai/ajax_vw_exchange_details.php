<?php 
/**
 * View exchange details 
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

set_time_limit(240);
set_min_memory_limit("712M");

/*$profile = 0;

if ($profile) {
  xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY, array(
    'ignored_functions' => array(
      'call_user_func',
      'call_user_func_array',
    )
  ));
}*/

CCanDo::checkRead();

$exchange_guid = CValue::get("exchange_guid");

$observations = $doc_errors_msg = $doc_errors_ack = array();

// Chargement de l'échange demandé
$exchange = CMbObject::loadFromGuid($exchange_guid);

$exchange->loadRefs(); 
$exchange->loadRefsInteropActor();
$exchange->getErrors();
$exchange->getObservations();

$limit_size = 100;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("exchange", $exchange);

switch(true) {
  case $exchange instanceof CExchangeTabular:
    $msg_segment_group = $exchange->getMessage();

    if ($msg_segment_group) {
      $doc = $msg_segment_group->toXML();
      if (count($msg_segment_group->children) > $limit_size) {
        $doc->formatOutput = true;
        $msg_segment_group->_xml = "<pre>".htmlentities($doc->saveXML())."</pre>";
      }
      else {
        $msg_segment_group->_xml = CMbString::highlightCode("xml", $doc->saveXML());
      }
    }
    
    $ack_segment_group = $exchange->getACK();

    if ($ack_segment_group) {
      $doc = $ack_segment_group->toXML();
      if (count($ack_segment_group->children) > $limit_size) {
        $doc->formatOutput = true;
        $ack_segment_group->_xml = "<pre>".htmlentities($doc->saveXML())."</pre>";
      }
      else {
        $ack_segment_group->_xml = CMbString::highlightCode("xml", $doc->saveXML());
      }
    }
    
    $smarty->assign("msg_segment_group", $msg_segment_group);
    $smarty->assign("ack_segment_group", $ack_segment_group);
    $smarty->assign("limit_size", $limit_size);
    $smarty->display("inc_exchange_tabular_details.tpl");
    break;

  case $exchange instanceof CEchangeXML:
    $smarty->display("inc_exchange_xml_details.tpl");
    break;

  case $exchange instanceof CExchangeDicom :
    $smarty->display("inc_exchange_dicom_details.tpl");
    break;
  
  default:
    $exchange->guessDataType();
    $smarty->display("inc_exchange_any_details.tpl");
    break;
}

/*
if ($profile) {
  $xhprof_data = xhprof_disable();
  $xhprof_root = 'C:/xampp/htdocs/xhgui/';
  require_once $xhprof_root.'xhprof_lib/config.php';
  require_once $xhprof_root.'xhprof_lib/utils/xhprof_lib.php';
  require_once $xhprof_root.'xhprof_lib/utils/xhprof_runs.php';

  $xhprof_runs = new XHProfRuns_Default();
  $run_id = $xhprof_runs->save_run($xhprof_data, "mediboard");
}
*/