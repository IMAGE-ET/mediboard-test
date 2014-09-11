<?php

/**
 * Edit user exchange sources
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

$mediuser = CMediusers::get();

// Source SMTP
$smtp_source = CExchangeSource::get("mediuser-".$mediuser->_id, "smtp", true, null, false);

// Source POP
$pop_sources = $mediuser->loadRefsSourcePop();
// Dans le cas où l'on aucune source POP on va en créer une vide
$new_source_pop = new CSourcePOP();
$new_source_pop->object_class = $mediuser->_class;
$new_source_pop->object_id    = $mediuser->_id;
$new_source_pop->name = "SourcePOP-".$mediuser->_id.'-'.($new_source_pop->countMatchingList()+1);

// Source FTP
$archiving_source = CExchangeSource::get("archiving-".$mediuser->_guid, "ftp", true, null, false);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("smtp_sources"      , array($smtp_source));
$smarty->assign("archiving_sources" , array($archiving_source));
$smarty->assign("pop_sources"       , $pop_sources);
$smarty->assign("new_source_pop"    , $new_source_pop);

$smarty->display("inc_edit_exchange_sources.tpl");
