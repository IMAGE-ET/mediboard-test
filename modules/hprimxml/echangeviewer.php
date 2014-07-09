<?php

/**
 * Exchange viewer
 *
 * @category HprimXML
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$echange_hprim_id = CValue::get("echange_hprim_id");

$echange_hprim = new CEchangeHprim();
$echange_hprim->load($echange_hprim_id);

$evt             = new CHPrimXMLEventPatient();
$domGetEvenement = $evt->getHPrimXMLEvenements($this->_message);
$domGetEvenement->formatOutput = true;
$doc_errors_msg  = @$domGetEvenement->schemaValidate(null, true, false);

$echange_hprim->_message = utf8_encode($domGetEvenement->saveXML());

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("echange_hprim", $echange_hprim);
$smarty->display("echangeviewer.tpl");

