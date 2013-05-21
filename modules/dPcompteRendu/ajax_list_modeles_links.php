<?php

/**
 * Liste des modèles d'un pack de modèles
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$pack_id = CValue::get("pack_id");

// Chargement du pack
$pack = new CPack;
$pack->load($pack_id);
$pack->loadBackRefs("modele_links", "modele_to_pack_id");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("pack", $pack);

$smarty->display("inc_list_modeles_links.tpl");
