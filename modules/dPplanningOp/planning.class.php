<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

require_once( $AppUI->getSystemClass ('mbobject' ) );

require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPpatients'  , 'patients'     ) );
require_once( $AppUI->getModuleClass('dPbloc'      , 'plagesop'     ) );
require_once( $AppUI->getModuleClass('dPccam'      , 'acte'         ) );
require_once( $AppUI->getModuleClass('dPcabinet'   , 'consultAnesth') );
require_once( $AppUI->getModuleClass('dPcabinet'   , 'files'        ) );
require_once( $AppUI->getModuleClass('dPplanningOp', 'sejour'       ) );
require_once( $AppUI->getModuleClass('dPsalleOp'   , 'acteccam'     ) );

// @todo: Put the following in $config_dist;
$dPconfig["dPplanningOp"]["operation"] = array (
  "duree_deb" => "0",
  "duree_fin" => "10",
  "hour_urgence_deb" => "0",
  "hour_urgence_fin" => "23",
  "min_intervalle" => "15"
);

class COperation extends CMbObject {
  // DB Table key
  var $operation_id = null;

  // DB References
  var $sejour_id = null;
  var $chir_id = null; // dupliqué en $sejour->praticien_id
  var $plageop_id = null;
  
  // DB Fields S@nté.com communication
  var $code_uf = null;
  var $libelle_uf = null;
  
  // DB Fields
  var $salle_id = null;
  var $date = null;
  var $codes_ccam = null;
  var $libelle = null;
  var $cote = null;
  var $temp_operation = null;
  var $pause = null;
  var $entree_bloc = null;
  var $pose_garrot = null;
  var $debut_op = null;
  var $fin_op = null;
  var $retrait_garrot = null;
  var $sortie_bloc = null;
  var $entree_reveil = null;
  var $sortie_reveil = null;
  var $time_operation = null;
  var $examen = null;
  var $materiel = null;
  var $commande_mat = null;
  var $info = null;
  var $date_anesth = null;
  var $time_anesth = null;
  var $type_anesth = null;  
  var $duree_hospi = null;
  var $ATNC = null;
  var $rques = null;
  var $rank = null;
  
  var $depassement = null;
  var $annulee = null;    // completé par $sejour->annule
    
  // Form fields
  var $_hour_op = null;
  var $_min_op = null;
  var $_hour_urgence = null;
  var $_min_urgence = null;
  var $_hour_anesth = null;
  var $_min_anesth = null;
  var $_lu_type_anesth = null;
  var $_codes_ccam = array();
  
  // Shortcut fields
  var $_datetime = null;
  
  // DB References
  var $_ref_chir = null;
  var $_ref_plageop = null;
  var $_ref_sejour = null;
  var $_ref_consult_anesth = null;
  var $_ref_files = array();
  var $_ref_actes_ccam = array(); 
  var $_ref_documents = array();
  
  // External references
  var $_ext_codes_ccam = null;
  
  // Old fields
//  var $pat_id = null; // remplacé par $sejour->patient_id
//  var $CCAM_code = null;  // DB Field to be removed
//  var $CCAM_code2 = null;  // DB Field to be removed
//  var $compte_rendu = null;  // DB Field to be removed
//  var $cr_valide = null;  // DB Field to be removed
//  var $date_adm = null; // remplacé par $sejour->entree_prevue
//  var $time_adm = null; // remplacé par $sejour->entree_prevue
//  var $chambre = null; // remplacée par $sejour->chambre_seule
//  var $type_adm = null; // remplacé $sejour->type
//  var $venue_SHS = null; // remplacé par $sejour->venue_SHS
//  var $saisie = null; // remplacé par $sejour->saisi_SHS
//  var $modifiee = null;  // remplace $sejour->modif_SHS
//  var $CIM10_code = null; // remplacé par $sejour->DP
//  var $convalescence = null; // remplacé par $sejour->convalescence
//  var $pathologie = null; // remplacé par $sejour->pathologie
//  var $septique = null;   // remplacé par $sejour->septique
//  var $_hour_adm = null;
//  var $_min_adm = null;

  function COperation() {
    $this->CMbObject( 'operations', 'operation_id' );

    $this->_props["chir_id"]        = "ref|notNull";
    $this->_props["plageop_id"]     = "ref";
    $this->_props["date"]           = "date";
    $this->_props["code_uf"]        = "str|maxLength|10";
    $this->_props["libelle_uf"]     = "str|maxLength|35";
    $this->_props["libelle"]        = "str|confidential";
    $this->_props["cote"]           = "notNull|enum|droit|gauche|bilatéral|total";
    $this->_props["temp_operation"] = "time";
    $this->_props["entree_bloc"]    = "time";
    $this->_props["sortie_bloc"]    = "time";
    $this->_props["time_operation"] = "time";
    $this->_props["examen"]         = "str|confidential";
    $this->_props["materiel"]       = "str|confidential";
    $this->_props["commande_mat"]   = "enum|o|n";
    $this->_props["info"]           = "enum|o|n";
    $this->_props["date_anesth"]    = "date";
    $this->_props["time_anesth"]    = "time";
    $this->_props["type_anesth"]    = "num";
    $this->_props["date_anesth"]    = "date";
    $this->_props["duree_hospi"]    = "num";
    $this->_props["ATNC"]           = "enum|o|n";
    $this->_props["rques"]          = "str|confidential";
    $this->_props["rank"]           = "num";
    $this->_props["depassement"]    = "currency|confidential";
    $this->_props["annulee"]        = "enum|0|1";
    
//    $this->_props["pat_id"] = "ref";
//    $this->_props["CCAM_code"] = "code|ccam";
//    $this->_props["CCAM_code2"] = "code|ccam";
//    $this->_props["CIM10_code"] = "code|cim10";
//    $this->_props["convalescence"] = "str|confidential";
//    $this->_props["date_adm"] = "date";
//    $this->_props["time_adm"] = "time";
//    $this->_props["type_adm"] = "enum|comp|ambu|exte";
//    $this->_props["venue_SHS"] = "num|length|8|confidential";
//    $this->_props["chambre"] = "enum|o|n";
//    $this->_props["saisie"] = "enum|o|n";
//    $this->_props["modifiee"] = "enum|0|1";
//    $this->_props["compte_rendu"] = "html|confidential";
//    $this->_props["cr_valide"] = "enum|0|1";
  }

  function check() {
    // Data checking
    $msg = null;

    if(!$this->operation_id) {
      if (!$this->chir_id) {
        $msg .= "Praticien non valide";
      }
    }
    
    return $msg . parent::check();
    
  }
  
  // Only use when current operation is deleted or canceled
  function reorder() {
    $sql = "SELECT operations.operation_id, operations.temp_operation,
    	operations.pause, plagesop.debut
      FROM operations
      LEFT JOIN plagesop
      ON plagesop.id = operations.plageop_id
      WHERE operations.plageop_id = '$this->plageop_id'
      AND operations.rank != 0
      AND operations.operation_id != '$this->operation_id'
      ORDER BY operations.rank";
    $result = db_loadlist($sql);
    if(count($result)) {
      $new_time = $result[0]["debut"];
    }
    $i = 1;
    foreach ($result as $key => $value) {
      $sql = "UPDATE operations SET rank = '$i', time_operation = '$new_time' " .
             "WHERE operation_id = '".$value["operation_id"]."'";
      db_exec( $sql );
      $new_time = mbAddTime($value["temp_operation"], $new_time);
      $new_time = mbAddTime("00:15:00", $new_time);
      $new_time = mbAddTime($value["pause"], $new_time);
      $i++;
    }
  }

  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label" => "acte(s) CCAM", 
      "name" => "acte_ccam", 
      "idfield" => "acte_id", 
      "joinfield" => "operation_id"
    );

    $tables[] = array (
      "label" => "affectation(s) d'hospitalisation", 
      "name" => "affectation", 
      "idfield" => "affectation_id", 
      "joinfield" => "operation_id"
    );

    return parent::canDelete($msg, $oid, $tables);
  }
  
  function delete() {
    // Re-numérotation des autres plages de la même plage
    if ($this->rank)
  	  $this->reorder();
    $msg = parent::delete();
    return $msg;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->codes_ccam = strtoupper($this->codes_ccam);
    if($this->codes_ccam)
      $this->_codes_ccam = explode("|", $this->codes_ccam);
    else
      $this->_codes_ccam[0] = "XXXXXX";
    
    $this->_hour_op = intval(substr($this->temp_operation, 0, 2));
    $this->_min_op  = intval(substr($this->temp_operation, 3, 2));
    
    $this->_hour_urgence = intval(substr($this->time_operation, 0, 2));
    $this->_min_urgence  = intval(substr($this->time_operation, 3, 2));

    if ($this->type_anesth != null) {
      $anesth = dPgetSysVal("AnesthType");
      $this->_lu_type_anesth = $anesth[$this->type_anesth];
    }

    $this->_hour_anesth = substr($this->time_anesth, 0, 2);
    $this->_min_anesth  = substr($this->time_anesth, 3, 2);
  }
  
  function updateDBFields() {
    if($this->codes_ccam) {
      $this->codes_ccam = strtoupper($this->codes_ccam);
      $codes_ccam = explode("|", $this->codes_ccam);
      $XPosition = true;
      while($XPosition !== false) {
        $XPosition = array_search("XXXXXXX", $codes_ccam);
        if ($XPosition !== false) {
          array_splice($codes_ccam, $XPosition, 1);
        }
      }
      $this->codes_ccam = implode("|", $codes_ccam);
    }
  	if ($this->_hour_anesth !== null and $this->_min_anesth !== null) {
      $this->time_anesth = 
        $this->_hour_anesth.":".
        $this->_min_anesth.":00";
  	}
    if ($this->_lu_type_anesth) {
      $anesth = dPgetSysVal("AnesthType");
      foreach($anesth as $key => $value) {
        if($value == $this->_lu_type_anesth)
          $this->type_anesth = $key;
      }
    }
    if ($this->_hour_op !== null and $this->_min_op !== null) {
      $this->temp_operation = 
        $this->_hour_op.":".
        $this->_min_op.":00";
    }
    if ($this->_hour_urgence !== null and $this->_min_urgence !== null) {
      $this->time_operation = 
        $this->_hour_urgence.":".
        $this->_min_urgence.":00";
    }
  }

  function store() {
    if ($msg = parent::store())
      return $msg;

    // Cas d'une annulation
    if ($this->annulee) {
      $this->reorder();
    }
    
    // Vérification qu'on a pas des actes CCAM codés obsolètes
    if($this->codes_ccam) {
      $this->loadRefsActesCCAM();
      foreach($this->_ref_actes_ccam as $keyActe => $acte) {
        if(strpos(strtoupper($this->codes_ccam), strtoupper($acte->code_acte)) === false) {
          $this->_ref_actes_ccam[$keyActe]->delete();
        }
      }
    }
    
    // Cas de la création dans une plage de spécialité
    $plageTmp = new CPlageOp;
    $plageTmp->load($this->plageop_id);
    if ($plageTmp->id_spec) {
      $plageTmp->id_spec = 0;
      $chirTmp = new CMediusers;
      $chirTmp->load($this->chir_id);
      $plageTmp->chir_id = $chirTmp->user_id;
      $plageTmp->store();
    }
    
    return $msg;
  }
  
  function loadRefChir() {
    $this->_ref_chir = new CMediusers;
    $this->_ref_chir->load($this->chir_id);
  }
  
  function loadRefPlageOp() {
    if($this->plageop_id) {
      $this->_ref_plageop = new CPlageOp;
      $this->_ref_plageop->load($this->plageop_id);
      $this->_datetime = $this->_ref_plageop->date;
    } else {
      $this->_datetime = $this->date;
    }
    $this->_datetime .= " ".$this->time_operation;
  }
  
  function loadRefCCAM() {
    $this->_ext_codes_ccam = array();
    foreach ($this->_codes_ccam as $code) {
      $ext_code_ccam = new CCodeCCAM($code);
      $ext_code_ccam->LoadLite();
      $this->_ext_codes_ccam[] = $ext_code_ccam;
    }
    
    $ext_code_ccam =& $this->_ext_codes_ccam[0];
    $code_ccam = @$this->_codes_ccam[0];

    if ($this->libelle !== null && $this->libelle != "") {
      $ext_code_ccam->libelleCourt = "<em>[$this->libelle]</em><br />".$ext_code_ccam->libelleCourt;
      $ext_code_ccam ->libelleLong = "<em>[$this->libelle]</em><br />".$ext_code_ccam->libelleLong;
    }
    
  }
  
  function loadRefsConsultAnesth() {
    $this->_ref_consult_anesth = new CConsultAnesth();
    $where = array();
    $where["operation_id"] = "= '$this->operation_id'";
    $this->_ref_consult_anesth->loadObject($where);
  }
  
  function loadRefSejour() {
    $this->_ref_sejour = new CSejour();
    $this->_ref_sejour->load($this->sejour_id);
  }
  
  function loadRefsFwd() {
    $this->loadRefsConsultAnesth();
    $this->loadRefChir();
    $this->loadRefPlageOp();
    $this->loadRefCCAM();
    $this->loadRefSejour();
    $this->_ref_sejour->loadRefsFwd();
    $this->_view = "Intervention de {$this->_ref_sejour->_ref_patient->_view} par le Dr. {$this->_ref_chir->_view}";
  }
  
  function loadRefsFiles() {
    $this->_ref_files = array();
    if ($this->operation_id) {
      $where = array("file_operation" => "= '$this->operation_id'");
      $this->_ref_files = new CFile();
      $this->_ref_files = $this->_ref_files->loadList($where);
    }
  }
  
  function loadRefsActesCCAM() {
    $where = array("operation_id" => "= '$this->operation_id'");
    $this->_ref_actes_ccam = new CActeCCAM;
    $this->_ref_actes_ccam = $this->_ref_actes_ccam->loadList($where);
  }
  
  function loadRefsDocuments() {
    $this->_ref_documents = new CCompteRendu();
    $where = array();
    $where[] = "(type = 'operation' OR type = 'hospitalisation')";
    $where["object_id"] = "= '$this->operation_id'";
    $order = "nom";
    $this->_ref_documents = $this->_ref_documents->loadList($where, $order);
  }

  function loadRefsBack() {
    $this->loadRefsFiles();
    $this->loadRefsActesCCAM();
    $this->loadRefsDocuments();
  }
  
  function loadPossibleActes () {
    $depassement_affecte = false;
  
    // existing acts may only be affected once to possible acts
    $used_actes = array();
    foreach ($this->_ext_codes_ccam as $codeKey => $codeValue) {
      $code =& $this->_ext_codes_ccam[$codeKey];
      $code->load($code->code);
      

      foreach ($code->activites as $activiteKey => $activiteValue) {
        $activite =& $code->activites[$activiteKey];
        foreach ($activite->phases as $phaseKey => $phaseValue) {
          $phase =& $activite->phases[$phaseKey];
          
          $possible_acte = new CActeCCAM;
          $possible_acte->montant_depassement = 0;
          $possible_acte->code_acte = $code->code;
          $possible_acte->code_activite = $activite->numero;
          $possible_acte->code_phase = $phase->phase;
          $possible_acte->execution = mbAddDateTime($this->temp_operation, $this->_datetime);
          
          
          $possible_acte->executant_id = $possible_acte->code_activite == 4 ?
            $this->_ref_plageop->anesth_id : 
            $this->chir_id;
          
          if (!$depassement_affecte and $possible_acte->code_activite == 1) {
            $depassement_affecte = true;
            $possible_acte->montant_depassement = $this->depassement;        	
          }
          
          $possible_acte->updateFormFields();
          $possible_acte->loadRefs();
          
          // Affect a loaded acte if exists
          foreach ($this->_ref_actes_ccam as $curr_acte) {
            if ($curr_acte->code_acte == $possible_acte->code_acte 
            and $curr_acte->code_activite == $possible_acte->code_activite 
            and $curr_acte->code_phase == $possible_acte->code_phase) {
              if (!isset($used_actes[$curr_acte->acte_id])) {
                $possible_acte = $curr_acte;
                $used_actes[$curr_acte->acte_id] = true;
                break;
              }
            }
          }
          
          $phase->_connected_acte = $possible_acte;
          
          foreach ($phase->_modificateurs as $modificateurKey => $modificateurValue) {
            $modificateur =& $phase->_modificateurs[$modificateurKey];
            if (strpos($phase->_connected_acte->modificateurs, $modificateur->code) !== false) {
              $modificateur->_value = $modificateur->code;
            } else {
              $modificateur->_value = "";              
            }
          }
        }
      }
    } 
  }
    
  function fillTemplate(&$template) {
  	$this->loadRefsFwd();
    if ($this->plageop_id) {
      $this->_ref_plageop->loadRefsFwd();
    }

    $dateFormat = "%d / %m / %Y";
    $timeFormat = "%Hh%M";

    $template->addProperty("Admission - Date"                 , mbTranformTime(null, $this->_ref_sejour->entree_prevue, $dateFormat));
    $template->addProperty("Admission - Heure"                , mbTranformTime(null, $this->_ref_sejour->entree_prevue, $timeFormat));
    $template->addProperty("Hospitalisation - Durée"          , $this->_ref_sejour->_duree_prevue);
    $template->addProperty("Hospitalisation - Date sortie"    , mbTranformTime(null, $this->_ref_sejour->sortie_prevue, $dateFormat));
    $template->addProperty("Opération - Anesthésiste - nom"   , @$this->_ref_plageop->_ref_anesth->_user_last_name);
    $template->addProperty("Opération - Anesthésiste - prénom", @$this->_ref_plageop->_ref_anesth->_user_first_name);
    $template->addProperty("Opération - Anesthésie"           , $this->_lu_type_anesth);
    $template->addProperty("Opération - libellé"              , $this->libelle);
    $template->addProperty("Opération - CCAM - code"          , @$this->_ext_codes_ccam[0]->code);
    $template->addProperty("Opération - CCAM - description"   , @$this->_ext_codes_ccam[0]->libelleLong);
    $template->addProperty("Opération - CCAM2 - code"         , @$this->_ext_codes_ccam[1]->code);
    $template->addProperty("Opération - CCAM2 - description"  , @$this->_ext_codes_ccam[1]->libelleLong);
    $template->addProperty("Opération - CCAM3 - code"         , @$this->_ext_codes_ccam[2]->code);
    $template->addProperty("Opération - CCAM3 - description"  , @$this->_ext_codes_ccam[2]->libelleLong);
    $template->addProperty("Opération - CCAM complet"         , implode(" - ", $this->_codes_ccam));
    $template->addProperty("Opération - salle"                , @$this->_ref_plageop->_ref_salle->nom);
    $template->addProperty("Opération - côté"                 , $this->cote);
    $template->addProperty("Opération - date"                 , mbTranformTime(null, $this->_datetime, $dateFormat));
    $template->addProperty("Opération - heure"                , mbTranformTime(null, $this->time_operation, $timeFormat));
    $template->addProperty("Opération - durée"                , mbTranformTime(null, $this->temp_operation, $timeFormat));
    $template->addProperty("Opération - entrée bloc"          , mbTranformTime(null, $this->entree_bloc, $timeFormat));
    $template->addProperty("Opération - pose garrot"          , mbTranformTime(null, $this->pose_garrot, $timeFormat));
    $template->addProperty("Opération - début op"             , mbTranformTime(null, $this->debut_op, $timeFormat));
    $template->addProperty("Opération - fin op"               , mbTranformTime(null, $this->fin_op, $timeFormat));
    $template->addProperty("Opération - retrait garrot"       , mbTranformTime(null, $this->retrait_garrot, $timeFormat));
    $template->addProperty("Opération - sortie bloc"          , mbTranformTime(null, $this->sortie_bloc, $timeFormat));
    $template->addProperty("Opération - depassement"          , $this->depassement);
    $template->addProperty("Opération - exams pre-op"         , nl2br($this->examen));
    $template->addProperty("Opération - matériel"             , nl2br($this->materiel));
    $template->addProperty("Opération - convalescence"        , nl2br($this->_ref_sejour->convalescence));
  }
}

?>