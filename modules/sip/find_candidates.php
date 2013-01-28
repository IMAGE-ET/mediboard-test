<?php /*  $ */

/**
 * Find candidates
 *
 * @category sip
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

// Récuperation des patients recherchés
$patient_nom              = CValue::getOrSession("nom");
$patient_prenom           = CValue::getOrSession("prenom");
$patient_jeuneFille       = CValue::getOrSession("nom_jeune_fille");
$patient_ville            = CValue::getOrSession("ville");
$patient_cp               = CValue::getOrSession("cp");
$patient_sexe             = CValue::getOrSession("sexe");
$patient_day              = CValue::get("Date_Day");
$patient_month            = CValue::get("Date_Month");
$patient_year             = CValue::get("Date_Year");
$quantity_limited_request = CValue::getOrSession("quantity_limited_request");
$pointer                  = CValue::getOrSession("pointer");
$patient_naissance = null;

if(($patient_year) || ($patient_month) || ($patient_day)){
  $patient_naissance = "on";
}

$naissance = null;
if ($patient_naissance == "on"){
  $year =($patient_year)?"$patient_year-":"%-";
  $month =($patient_month)?"$patient_month-":"%-";
  $day =($patient_day)?"$patient_day":"%";
  if ($day!="%") {
    $day = str_pad($day,2,"0",STR_PAD_LEFT);
  }

  $naissance = $year.$month.$day;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("nom"                     , $patient_nom        );
$smarty->assign("prenom"                  , $patient_prenom     );
$smarty->assign("nom_jeune_fille"         , $patient_jeuneFille );
$smarty->assign("naissance"               , $naissance          );
$smarty->assign("ville"                   , $patient_ville      );
$smarty->assign("cp"                      , $patient_cp         );
$smarty->assign("sexe"                    , $patient_sexe       );
$smarty->assign("quantity_limited_request", $quantity_limited_request);
$smarty->assign("pointer"                 , null);

$smarty->display("find_candidates.tpl");
