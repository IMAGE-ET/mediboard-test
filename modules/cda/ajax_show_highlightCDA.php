<?php 

/**
 * $Id$
 *
 * Affiche les informations la structure, le xml et si le document est valide
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

$message = CValue::post("message");
$message = stripslashes($message);

$treecda = CCdaTools::parse($message);
$xml     = CCdaTools::showxml($message);

$smarty = new CSmartyDP();

$smarty->assign("message", $message);
$smarty->assign("treecda", $treecda);
$smarty->assign("xml"    , $xml);

$smarty->display("inc_highlightcda.tpl");