<?php

/**
 * Add domain with idex EAI
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$domain = new CDomain();

// Récupération des objet_class
$req = new CRequest;
$req->addTable("id_sante400");
$req->addColumn("object_class");
$req->addGroup("object_class");

$ds = CSQLDataSource::get("std");
$idexs_class = CMbArray::pluck($ds->loadList($req->makeSelect()), "object_class");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("domain"     , $domain);
$smarty->assign("idexs_class", $idexs_class);
$smarty->display("inc_add_domain_with_idex.tpl");
