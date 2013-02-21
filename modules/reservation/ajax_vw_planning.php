<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

global $m;

// On sauvegarde le module pour que les mises en session des param�tes se fassent
// dans le module depuis lequel on acc�de � la ressource
$save_m = $m;

$current_m     = CValue::get("current_m");
$m = $current_m;

$date_planning = CValue::getOrSession("date_planning");
$praticien_id  = CValue::getOrSession("praticien_id");
$scroll_top    = CValue::get("scroll_top", null);
$bloc_id       = CValue::getOrSession("bloc_id", "");
$show_cancelled = CValue::getOrSession("show_cancelled", 0);

$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);

$group = CGroups::loadCurrent();

// R�cup�ration des salles
$salle = new CSalle();
$where = array();
$ljoin = array();
$order = "bloc_operatoire.nom";

if ($bloc_id) {
  $where["bloc_id"] = "= '$bloc_id'";
}
else {
  $blocs = $bloc->loadGroupList();
  $where["bloc_id"] = CSQLDataSource::prepareIn(array_keys($blocs));
} 

$where["group_id"] = "= '$group->_id'";
$ljoin["bloc_operatoire"] = "bloc_operatoire.bloc_operatoire_id = sallesbloc.bloc_id";

$salles = $salle->loadList($where, $order, null, null, $ljoin);
$salles_ids = array_keys($salles);

// R�cup�ration des op�rations
$operation = new COperation();

$where = array();
$ljoin = array();

$where["operations.date"] = "= '$date_planning'";
if (!$show_cancelled) {
  $where["operations.annulee"] = "= '0'";
}
$where["operations.plageop_id"] = "IS NULL";
$where["operations.salle_id"] = CSQLDataSource::prepareIn($salles_ids);

$ljoin["sallesbloc"] = "sallesbloc.salle_id = operations.salle_id";
$ljoin["bloc_operatoire"] = "bloc_operatoire.bloc_operatoire_id = sallesbloc.bloc_id";

if ($bloc_id) {
  $where["sallesbloc.bloc_id"] = "= '$bloc_id'";
}
else {
  $where["sallesbloc.bloc_id"] = CSQLDataSource::prepareIn(array_keys($blocs));
}

if ($praticien_id) {
  $where["operations.chir_id"] = " = '$praticien_id'";
}

$operations = $operation->loadList($where, null, null, null, $ljoin);

$prats  = CMbObject::massLoadFwdRef($operations, "chir_id");
CMbObject::massLoadFwdRef($operations, "salle_id");
CMbObject::massLoadFwdRef($operations, "anesth_id");
CMbObject::massLoadFwdRef($prats, "function_id");

// R�cup�ration des commentaires
$commentaire = new CCommentairePlanning();
$where = array();

$where[] = "'$date_planning' BETWEEN date(debut) AND date(fin)";
$where["salle_id"] = CSQLDataSource::prepareIn($salles_ids);

$commentaires = $commentaire->loadList($where);

// R�cup�ration des plages op�ratoires
$plageop = new CPlageOp();
$where = array();

$where["date"] = " = '$date_planning'";
$where["salle_id"] = CSQLDataSource::prepareIn($salles_ids);

$plages = $plageop->loadList($where);

// Cr�ation du planning
$planning = new CPlanningWeek(0, 0, count($salles), count($salles), false, "auto");
$planning->title =  "Planning du ".mbDateToLocale($date_planning);

if ($bloc_id) {
  $planning->title .= " - $bloc->nom";
}

$planning->guid = "planning_interv";
$planning->hour_min  = mbTime(CAppUI::conf("reservation debut_planning").":00");
$planning->dragndrop = $planning->resizable = CCanDo::edit();
$planning->hour_divider = 60 / intval(CAppUI::conf("dPplanningOp COperation min_intervalle"));
$planning->show_half = true;
$i = 0;
$today = mbDate();

foreach ($salles as $_salle) {
  if ($bloc_id) {
    $planning->addDayLabel($i, $_salle->_shortview);
  }
  else {
    $planning->addDayLabel($i, $_salle->_view);
  }
  if ($today == $date_planning) {
    $planning->addEvent(new CPlanningEvent(null, "$i ".mbTime(), null, null, "red", null, "now"));
  }
  $i++;
}

// Tri des op�rations par salle
$operations_by_salle = array();
foreach ($operations as $key => $_operation) {
  if (!$_operation->salle_id) {
    unset($operations[$key]);
    continue;
  }
  
  if (!isset($operations_by_salle[$_operation->salle_id])) {
    $operations_by_salle[$_operation->salle_id] = array();
  }
  $operations_by_salle[$_operation->salle_id][] = $_operation;
}

// Tri des commentaires par salle
$commentaires_by_salle = array();
foreach ($commentaires as $key => $_commentaire) {
  $salle_id = $_commentaire->salle_id;
  if (!isset($commentaires_by_salle[$salle_id])) {
    $commentaires_by_salle[$salle_id] = array();
  }
  $commentaires_by_salle[$salle_id][] = $_commentaire;
}

// Tri des plages par salle
$plages_by_salle = array();
CMbObject::massLoadFwdRef($plages, "chir_id");
CMbObject::massLoadFwdRef($plages, "spec_id");

foreach ($plages as $_plage) {
  $_plage->loadRefChir();
  $_plage->loadRefSpec();
  $salle_id = $_plage->salle_id;
  if (!isset($plages_by_salle[$salle_id])) {
    $plages_by_salle[$salle_id] = array();
  }
  $plages_by_salle[$salle_id][] = $_plage;
}

// Ajout des �v�nements (op�rations)
$can_edit = CCanDo::edit();

$diff_hour_urgence = CAppUI::conf("reservation diff_hour_urgence");

foreach ($operations_by_salle as $salle_id => $_operations) {
  $i = array_search($salle_id, $salles_ids);
  foreach ($_operations as $_operation) {
    $_operation->_ref_salle = $_operation->loadFwdRef("salle_id");
    
    $first_log = $_operation->loadFirstLog();
    
    $chir    = $_operation->loadRefChir();
    $chir->loadRefFunction();
    $chir_2  = $_operation->loadRefChir2();
    $chir_2->loadRefFunction();
    $chir_3  = $_operation->loadRefChir3();
    $chir_3->loadRefFunction();
    $chir_4  = $_operation->loadRefChir4();
    $chir_4->loadRefFunction();
    
    $anesth  = $_operation->_ref_anesth = $_operation->loadFwdRef("anesth_id");
    $sejour  = $_operation->loadRefSejour();
    $patient = $sejour->loadRefPatient();
    $besoins = $_operation->loadRefsBesoins();
    
    if (!$anesth->_id) {
      $anesth = $_operation->loadFwdRef("anesth_id", true);
    }
    if ($_operation->horaire_voulu) {
      $debut = "$i {$_operation->horaire_voulu}";
      $debut_op = $_operation->horaire_voulu;
      $fin_op = mbAddTime($_operation->temp_operation, $_operation->horaire_voulu);
      $duree = mbMinutesRelative($_operation->horaire_voulu, $fin_op);
    }
    else {
      $debut = "$i {$_operation->time_operation}";
      $debut_op = $_operation->time_operation;
      $fin_op = mbAddTime($_operation->temp_operation, $_operation->time_operation);
      $duree = mbMinutesRelative($_operation->time_operation, $fin_op);
    }
    
    $libelle = "<span style='display: none;' data-entree_prevue='$sejour->entree_prevue' ".
      "data-sortie_prevue='$sejour->sortie_prevue' data-sejour_id='$sejour->_id' data-duree='$_operation->temp_operation'></span>";
    
    if (abs(mbHoursRelative("$_operation->date $debut_op", $first_log->date)) <= $diff_hour_urgence) {
      $libelle .= "<span style='float: right' title='Intervention en urgence'><img src='images/icons/attente_fourth_part.png' /></span>";
    }
    
    $libelle .= "<span onmouseover='ObjectTooltip.createEx(this, \"".CMbString::htmlEntities($patient->_guid)."\")'>".CMbString::htmlEntities($patient->nom. " " .$patient->prenom)."</span>, ".$patient->getFormattedValue("naissance").
    "\n<span style='font-size: 11px; font-weight: bold;' onmouseover='ObjectTooltip.createEx(this, \"".$_operation->_guid."\")'>".mbTransformTime($debut_op, null, "%H:%M")." - ".mbTransformTime($fin_op, null, "%H:%M")."</span>".
    "\n<span onmouseover='ObjectTooltip.createEx(this, \"".$sejour->_guid."\")'>".$sejour->getFormattedValue("entree")."</span>".
    "\n<span style='font-size: 11px; font-weight: bold;'>".CMbString::htmlEntities($_operation->libelle)."</span>".
    "\n<span onmouseover='ObjectTooltip.createEx(this, \"".$chir->_guid."\")'>".CMbString::htmlEntities($chir->_view)."</span>";
    
    if ($chir_2->_id) {
      $libelle .= "\n<span onmouseover='ObjectTooltip.createEx(this, \"".$chir_2->_guid."\")'>".CMbString::htmlEntities($chir_2->_view)."</span>";
    }
    
    if ($chir_3->_id) {
      $libelle .= "\n<span onmouseover='ObjectTooltip.createEx(this, \"".$chir_3->_guid."\")'>".CMbString::htmlEntities($chir_3->_view)."</span>";
    }
    
    if ($chir_4->_id) {
      $libelle .= "\n<span onmouseover='ObjectTooltip.createEx(this, \"".$chir_4->_guid."\")'>".CMbString::htmlEntities($chir_4->_view)."</span>";
    }
    
    if ($anesth->_id) {
      $libelle .= "\n<span onmouseover='ObjectTooltip.createEx(this, \"".$anesth->_guid."\")'>".CMbString::htmlEntities($anesth->_view)."</span>";
    }
    
    $libelle .= "\n".CMbString::htmlEntities($_operation->rques);
    
    if (count($besoins)) {
      CMbObject::massLoadFwdRef($besoins, "type_ressource_id");
      
      $last_besoin = end($besoins);
      
      $libelle .= "<span class='compact' style='color: #000'>";
      foreach ($besoins as $_besoin) {
        $_type_ressource = $_besoin->loadRefTypeRessource();
        $libelle .= CMbString::htmlEntities($_type_ressource->libelle);
        if ($_besoin != $last_besoin) {
          $libelle .= " - ";
        }
      }
      $libelle .= "</span>";
    }
    
    // Rouge
    if ($sejour->annule) {
      $color = "#f22";
    }
    else {
      switch ($sejour->recuse) {
        case "0":
          // Orange
          if ($sejour->type == "ambu")  {
            $color = "#fa2";
          }
          // Vert
          else if ($sejour->type == "comp") {
            $color = "#7f6";
          }
          break;
        // Bleu
        case "-1" :
          $color = "#68f";
      }
    }
    
    $event = new CPlanningEvent($_operation->_guid, $debut, $duree, $libelle, $color, true, null, $_operation->_guid, false);
    
    if ($can_edit) {
      $event->addMenuItem("edit" , "Modifier cette op�ration");
      $event->addMenuItem("cut"  , "Couper cette op�ration");
      $event->addMenuItem("copy"  , "Copier cette op�ration");
      $event->addMenuItem("clock", "Modifier les dates d'entr�e et sortie du s�jour");
    }
    
    $event->plage["id"] = $_operation->_id;
    $event->type = "operation_horsplage";
    $event->draggable = $event->resizable = CCanDo::edit();
    $planning->addEvent($event);
    
    if ($_operation->presence_preop) {
      $hour_debut_preop = mbSubTime($_operation->presence_preop, $_operation->time_operation);
      $debut_preop = "$i $hour_debut_preop";
      $duree = mbMinutesRelative($hour_debut_preop, $_operation->time_operation);
      $event = new CPlanningEvent("pause-".$_operation->_guid, $debut_preop, $duree, "", "#ddd", true);
      
      $planning->addEvent($event);
    }
    
    if ($_operation->presence_postop) {
      $hour_fin_postop = mbAddTime($_operation->presence_postop, $fin_op);
      $debut_postop = "$i $fin_op";
      $duree = mbMinutesRelative($fin_op, $hour_fin_postop);
      $event = new CPlanningEvent("pause-".$_operation->_guid, $debut_postop, $duree, "", "#ddd", true);
      
      $planning->addEvent($event);
    }
  }
}

// Ajout des �v�nements (commentaires)
foreach ($commentaires_by_salle as $salle_id => $_commentaires) {
  $i = array_search($salle_id, $salles_ids);
  
  foreach ($_commentaires as $_commentaire) {
    $debut = "$i ".mbTime($_commentaire->debut);
    
    $duree = mbMinutesRelative(mbTime($_commentaire->debut), mbTime($_commentaire->fin));
    
    $libelle = "<span style='display: none;' data-entree_prevue='$_commentaire->debut' data-sortie_prevue='$_commentaire->fin'></span>".
    "<span style='font-size: 11px; font-weight: bold;'>".CMbString::htmlEntities($_commentaire->libelle)."</span>".
    "\n<span class='compact'>".CMbString::htmlEntities($_commentaire->commentaire)."</span>";
    
    $event = new CPlanningEvent($_commentaire->_guid, $debut, $duree, $libelle, "#$_commentaire->color", true, null, $_commentaire->_guid, false);
    
    $event->type = "commentaire_planning";
    $event->draggable = $event->resizable = CCanDo::edit();
    $event->plage["id"] = $_commentaire->_id;
    
    if ($can_edit) {
      $event->addMenuItem("edit" , "Modifier ce commentaire");
    }
    
    $planning->addEvent($event);
  }
}

// Ajout des plages
foreach ($plages_by_salle as $salle_id => $_plages) {
  $i = array_search($salle_id, $salles_ids);
  
  foreach ($_plages as $_plage) {
    $debut = "$i ".mbTime($_plage->debut);
    
    $duree = mbMinutesRelative(mbTime($_plage->debut), mbTime($_plage->fin));
    
    $libelle = $_plage->chir_id ? $_plage->_ref_chir->_view : $_plage->_ref_spec->_view;
    
    $event = new CPlanningEvent($_plage->_guid, $debut, $duree, $libelle, "#aaa", true, null, $_plage->_guid, false);
    
    $event->type = "commentaire_planning";
    $event->plage["id"] = $_plage->_id;
    
    
    $planning->addEvent($event);
  }
}

$m = $save_m;

$smarty = new CSmartyDP();

$smarty->assign("planning", $planning);
$smarty->assign("salles"  , $salles);
$smarty->assign("salles_ids", $salles_ids);
$smarty->assign("date_planning", $date_planning);
$smarty->assign("scroll_top", $scroll_top);
$smarty->assign("show_cancelled", $show_cancelled);

$smarty->display("inc_vw_planning.tpl");
