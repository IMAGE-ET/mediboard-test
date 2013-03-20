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

$cdafile = new CCdaTools();
$cdafile->parse($message);
$cdafile->showxml($message);

$smarty = new CSmartyDP();

$smarty->assign("message", $message);
$smarty->assign("treecda", $cdafile);

$smarty->display("inc_highlightcda.tpl");