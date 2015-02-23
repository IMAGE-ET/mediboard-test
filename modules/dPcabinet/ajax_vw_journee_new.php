<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$function_id  = CValue::getOrSession("function_id");
$date         = CValue::getOrSession("date");

// filters
$show_free  = CValue::get("show_free", 1);
$cancelled  = CValue::get("cancelled");
$facturated = CValue::get("facturated");
$finished   = CValue::get("finished");
$actes      = CValue::get("actes");


$function = new CFunctions();
$function->load($function_id);
$muser = new CMediusers();
$musers = $muser->loadProfessionnelDeSanteByPref(PERM_READ, $function_id, null, true);

$planning               = new CPlanningWeek(0, 0, count($musers), count($musers), false, "auto");
$planning->title        = "Planning du " . htmlentities(CMbDT::format($date, "%A %d %B %Y"));
$planning->guid         = "planning_j_n";
$planning->dragndrop    = 1;
$planning->hour_divider = 12;
$planning->show_half    = true;

$i = 0;
foreach ($musers as $_user) {
  $user_id = $_user->_id;

  // plages conge
  $conge = new CPlageConge();
  $where = array();
  $where["date_debut"] = " <= '$date'";
  $where["date_fin"] = " >= '$date'";
  $where["user_id"] = " = '$_user->_id'";
  $nb_conges = $conge->countList($where);

  if ($nb_conges) {
    $conge->loadObject($where);

    $libelle = '<h3 style="text-align: center">
    CONGES</h3>
    <p style="text-align: center">'.CMbString::htmlEntities($conge->libelle).'</p>';
    if ($conge->replacer_id) {
      $replacer = $conge->loadRefReplacer();
      $libelle .= '<p style="text-align: center">Remplacé par : '.CMbString::htmlEntities($replacer->_view).'</p>';
    }

    $event = new CPlanningEvent(
      $conge->_guid,
      "$i $conge->date_debut 00:00:00",
      1440,
      $libelle,
      "#dddddd",
      true,
      "hatching",
      null,
      false
    );
    $event->below = 1;
    $planning->addEvent($event);
  }

  // plages consult
  $plage = new CPlageconsult();
  $plage->date = $date;
  $plages = $plage->loadMatchingList();

  $style = "color:black;
    font-size:1.3em;
    text-shadow: 0 0 10px white;";
  $style .= $nb_conges ? "text-decoration: line-through;" : null;

  // add prat to the calendar
  $planning->addDayLabel(
    $i, "<span style=\"$style\">".CMbString::htmlEntities($_user->_view)."</span>", null, "#".$_user->_color, "ObjectTooltip.createEx(this, '".$_user->_guid."');", false, array("user_id" => $user_id));

  $plage = new CPlageconsult();
  $plage->chir_id = $user_id;
  $plage->date = $date;
  /** @var CPlageconsult[] $plages */
  $plages = $plage->loadMatchingList();

  foreach ($plages as $_plage) {
    // range
    $range = new CPlanningRange(
      $_plage->_guid,
      $i." ".$_plage->debut,
      CMbDT::minutesRelative($_plage->debut, $_plage->fin),
      $_plage->libelle,
      $_plage->color
    );
    $range->type = "plageconsult";

    $planning->addRange($range);

    //colors
    $color = "#cfc";
    if ($_plage->remplacant_id && $_plage->remplacant_id != $chirSel) {
      // Je suis remplacé par un autre médecin
      $color = "#FAA";
    }
    if ($_plage->remplacant_id && $_plage->remplacant_id == $chirSel) {
      // Je remplace un autre médecin
      $color = "#FDA";
    }


    // consults libres
    if ($show_free) {
      $_plage->loadRefsConsultations(false);
      $utilisation = $_plage->getUtilisation();
      foreach ($utilisation as $_timing => $_nb) {
        if (!$_nb) {
          $heure = CMbDT::format($_timing, "%H:%M");
          $debute = "$i $_timing";
          $title = "<strong>$heure</strong>";
          $event = new CPlanningEvent($debute, $debute, $_plage->_freq, $title, $color, true, "droppable", null, false);
          $event->type        = "rdvfree";
          $event->plage["id"] = $_plage->_id;
          if ($_plage->locked == 1) {
            $event->disabled = true;
          }
          $event->plage["color"] = $_plage->color;
          //Ajout de l'évènement au planning
          $planning->addEvent($event);
        }
      }
    }

    // load consultations manually
    $consult = new CConsultation();
    $where = array();
    $where["plageconsult_id"] = " = '$_plage->_id' ";
    $where["annule"] = " = '$cancelled'";
    if ($finished) {
      $where["chrono"] = " = '$finished'";
    }
    if ($facturated) {
      $where["facture"] = " = '1'";
    }
    /** @var CConsultation[] $consults */
    $consults = $consult->loadList($where, "heure");

    // consultations prises
    foreach ($consults as $_consult) {
      $_consult->loadPosition();

      $_actes = $_consult->loadRefsActes();
      $nb_actes = $_consult->_count_actes;
      // avec des actes
      if ($actes && !$nb_actes) {
        continue;
      }
      // sans actes
      if ($actes === "0" && $nb_actes > 0) {
        continue;
      }

      $debute = "$i $_consult->heure";
      $motif = CMbString::htmlEntities($_consult->motif);
      $heure = CMbDT::format($_consult->heure, "%H:%M");

      if ($_consult->patient_id) {
        $_consult->loadRefPatient();
        if ($color = "#cfc") {
          $color = "#fee";
          if ($_consult->premiere) {
            $color = "#faa";
          }
          elseif ($_consult->derniere) {
            $color = "#faf";
          }
          elseif ($_consult->sejour_id) {
            $color = "#CFFFAD";
          }
        }

        $title = "";
        if ($_consult->_consult_sejour_out_of_nb) {
          $title.= "<span style='float:right;'>$_consult->_consult_sejour_nb/ $_consult->_consult_sejour_out_of_nb</span>";
        }
        $title .= "<strong>$heure</strong> ".CMbString::htmlEntities($_consult->_ref_patient->_view) . "\n" . $motif;

        $event = new CPlanningEvent(
          $_consult->_guid,
          $debute,
          $_consult->duree * $_plage->_freq,
          $title,
          $color,
          true,
          null,
          $_consult->_guid,
          false
        );
      }
      else {
        if ($color = "#cfc") {
          $color = "#faa";
        }
        $event = new CPlanningEvent(
          $_consult->_guid,
          $debute, $_consult->duree * $_plage->_freq,
          $motif ? $motif : "[PAUSE]",
          $color,
          true,
          null,
          null
        );
      }
      $event->type        = "rdvfull";
      $event->plage["id"] = $_plage->_id;
      $event->plage["consult_id"] = $_consult->_id;
      if ($_plage->locked == 1) {
        $event->disabled = true;
      }

      $_consult->loadRefCategorie();
      if ($_consult->categorie_id) {
        $event->icon = "./modules/dPcabinet/images/categories/".$_consult->_ref_categorie->nom_icone;
        $event->icon_desc = CMbString::htmlEntities($_consult->_ref_categorie->nom_categorie);
      }

      if ($_consult->patient_id) {
        $event->draggable /*= $event->resizable */ = 1;
        $event->hour_divider = 60 / CMbDT::transform($_plage->freq, null, "%M");
      }

      if ($_consult->canDo()->edit) {
        if ($_consult->chrono == 16) {
          $event->addMenuItem("tick", CMbString::htmlEntities("Notifier l'arrivée"));
        }
        if ($_consult->chrono == 32) {
          $event->addMenuItem("tick_cancel", CMbString::htmlEntities("Annuler l'arrivée"));
        }
      }

      //Ajout de l'évènement au planning
      $event->plage["color"] = $_plage->color;
      $planning->addEvent($event);
    }

  }

  $i++;
}
//$planning->allow_superposition = 1 ;
$planning->rearrange(true);


// smarty
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("nday", CMbDT::date("+1 DAY", $date));
$smarty->assign("pday", CMbDT::date("-1 DAY", $date));
$smarty->assign("planning", $planning);
$smarty->assign("height_calendar"     , CAppUI::pref("height_calendar", "2000"));
$smarty->display("inc_vw_journee_new.tpl");