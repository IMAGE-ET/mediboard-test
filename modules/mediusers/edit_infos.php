<?php /** $Id$ **/

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Sébastien Fillonneau
*/

CCanDo::check();

$mediuser = CMediusers::get();
$mediuser->loadRefsFwd();
$mediuser->_ref_user->isLDAPLinked();

// Récupération des disciplines
$disciplines = new CDiscipline;
$disciplines = $disciplines->loadList();

// Chargement des banques
$order = "nom ASC";
$banque = new CBanque();
$banques = $banque->loadList(null, $order);


// Récupération des spécialités CPAM
$spec_cpam = new CSpecCPAM();
$spec_cpam = $spec_cpam->loadList();

$affiche_nom = CValue::get("affiche_nom", 0);

// Source SMTP
$smtp_source = CExchangeSource::get("mediuser-".$mediuser->_id, "smtp", true, null, false);

$pop_sources = $mediuser->loadRefsSourcePop();
if (count($pop_sources) == 0) {
  $pop_source = CExchangeSource::get("user-pop-".$mediuser->_id, "pop", true, null, false);
  $pop_source->libelle = "newConnection";
  $pop_source->object_class = $mediuser->_class;
  $pop_source->object_id = $mediuser->_id;
  $pop_sources[] = $pop_source;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("banques"     , $banques                );
$smarty->assign("disciplines" , $disciplines            );
$smarty->assign("spec_cpam"   , $spec_cpam              );
$smarty->assign("user"        , $mediuser               );
$smarty->assign("fonction"    , $mediuser->_ref_function);
$smarty->assign("affiche_nom" , $affiche_nom            );
$smarty->assign("smtp_source" , $smtp_source            );
$smarty->assign("pop_source"  , $pop_sources);
$smarty->display("edit_infos.tpl");