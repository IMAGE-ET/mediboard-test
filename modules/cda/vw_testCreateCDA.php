<?php 

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$cda = new CCDADocumentCDA();
$cr = new CCompteRendu();
$cr->load($cr->getRandomValue("compte_rendu_id", true));
$documentCDA = $cda->generateCDA($cr);
$cdaXML = $documentCDA->toXML("ClinicalDocument", "urn:hl7-org:v3");
$cdaXML->purgeEmptyElements();
$message = $cdaXML->saveXML();

$cdafile = new CCdaTools();
$cdafile->parse($message);
$cdafile->showxml($message);

$smarty = new CSmartyDP();

$smarty->assign("message", $message);
$smarty->assign("treecda", $cdafile);

$smarty->display("inc_highlightcda.tpl");