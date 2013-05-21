<?php

/**
 * Modification de liste de choix
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

// Liste sélectionnée
$liste_id = CValue::getOrSession("liste_id");
$liste = new CListeChoix();
$liste->load($liste_id); 

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("liste", $liste);

$smarty->display("inc_list_choix.tpl");
