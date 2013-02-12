<!-- $Id$ -->

{{mb_default var=modurgence value=0}}

<script type="text/javascript">

checkAld = function(){
  var oForm = getForm("editSejour");
  var url = new Url("dPplanningOp", "ajax_check_ald");
  url.addParam("patient_id", $V(oForm.patient_id));
  url.addParam("sejour_id", $V(oForm.sejour_id));
  url.requestUpdate(SystemMessage.id, {insertion: function(receiver, text){$("ald_patient").update(text); $("ald_patient_easy").update(text);}});
}

var Value = {
  // Synchronize elements value between Easy and Expert forms
  synchronize: function(element, expert) {
    expert = expert || "editOp";
    
    var other = element.form.name == expert ?
      document.editOpEasy :
      document.forms[expert];
      
    if (other) {
      $V(other[element.name], element.value);
    }
  }
}

var listCategoriePrat = {{$categorie_prat|@json}};

function modifPrat(){
  var oForm = document.editSejour;
  var sValue = oForm.praticien_id.value;
  
  oForm.pathologie.value= sValue ? 
    listCategoriePrat[sValue] || "" : "";

  if (oForm._protocole_prescription_chir_id) {
    $V(oForm._protocole_prescription_chir_id, "");
  }
  var libelle = $("editSejour_libelle_protocole");
  if (libelle) {
    libelle.value = "";
  }
}

function refreshViewProtocoleAnesth(prescription_id) {
  if($("prot_anesth_view")) {
    var url = new Url("dPplanningOp", "httpreq_vw_protocole_anesth");
    url.addParam("prescription_id", prescription_id);
    url.requestUpdate("prot_anesth_view");
  }
}

function checkDureeHospi(sType) {
  var oForm = document.editSejour;
  oTypeField  = oForm.type;
  oDureeField = oForm._duree_prevue;
  if(sType == "syncType") {
    if($V(oDureeField) == 0 && $V(oTypeField) == "comp") {
      $V(oTypeField, "ambu");
    } else if($V(oDureeField) > 0 && $V(oTypeField) == "ambu") {
        $V(oTypeField, "comp");
    }
  } else if(sType == "syncDuree") {
    if($V(oDureeField) > 0 && $V(oTypeField) == "ambu") {
      $V(oDureeField, "0");
    }
  } else {
    if($V(oTypeField) == "comp" && ($V(oDureeField) == 0 || $V(oDureeField) == '')) {
      $V(oDureeField, prompt("Veuillez saisir une durée prévue d'hospitalisation d'au moins 1 jour", "1"));
      oDureeField.focus();
      return false;
    }
    if ($V(oTypeField) == "ambu" && $V(oDureeField) != 0 && $V(oDureeField) != '') {
      alert('Pour une admission de type Ambulatoire, la durée du séjour doit être de 0 jour.');
      oDureeField.focus();
      return false;
    }
    return true;
  }
}

function reinitDureeSejour(){
  var form = document.editSejour;
  field2 = form._duree_prevue;
  field2.value = '0';
}

function removePlageOp(bIgnoreGroup){
  var oFormOp = document.editOp;  
  var oFormSejour = document.editSejour;
  if(oFormOp){
    if((oFormOp._group_id.value != oFormSejour.group_id.value) || bIgnoreGroup){
      oFormOp._group_id.value = oFormSejour.group_id.value;
      {{if !$modurgence}}
        $V(oFormOp.plageop_id, "");
        $V(oFormOp._date, "");
        $V(oFormOp.date, "");
      {{/if}}
    }
  }
}

CanBloc = {{$modules.dPbloc->_can|json}};  

function checkCancelAlerts() {
   var msg = "Vous êtes sur le point d'annuler ce séjour, ceci entraîne :";
   msg += "\n\n1. Tous les placements dans les lits seront supprimés.";
   
  {{if count($sejour->_cancel_alerts.all)}}
   msg += "\n\n2. Attention, vous allez également annuler des opérations :";
   {{foreach from=$sejour->_cancel_alerts.all item=alert}}
   msg += "\n\t- " + "{{$alert|smarty:nodefaults|escape:'javascript'}}";
   {{/foreach}}
   {{/if}}
   msg += "\n\nSouhaitez-vous continuer ?";   
  if (!confirm(msg)) {
    return;
  }

  {{if count($sejour->_cancel_alerts.acted)}}
   msg = "Ce séjour contient une ou plusieurs interventions qui ont probablement déjà eu lieu :";
  {{foreach from=$sejour->_cancel_alerts.acted item=alert}}
  msg += "\n\t- " + "{{$alert|smarty:nodefaults|escape:'javascript'}}";
  {{/foreach}}
  if (CanBloc.edit) {
    if (!confirm(msg + "\n\nVoulez-vous malgré tout l'annuler ?")) {
      return;
     }
  }
  else {
    alert(msg + "\n\nVeuillez vous adresser au responsable de bloc pour annuler cette intervention.");
    return;
  }
  {{/if}}     
  
  return true;
}

function cancelSejour() {
  var oForm = document.editSejour;
  var oElement = oForm.annule;
  
  // Annulation 
  if (oElement.value == "0") {
    if (checkCancelAlerts()) {
      $V(oElement, "1");
      oForm.submit();
      return;   
    }
  }
      
  // Rétablissement
  if (oElement.value == "1") {
    if (confirm("Voulez-vous vraiment rétablir le séjour ?")) {
      oElement.value = "0";
      oForm.submit();
      return;
    }
  }
}

function modifSejour() {
  var oForm = document.editSejour;
  {{if $conf.dPplanningOp.CSejour.entree_modifiee}}
  if (oForm.entree_preparee.value == '1') {
    oForm.entree_modifiee.value = '1';
    oForm.entree_preparee.value = '0';
  }
  {{/if}}
  canNullOK(oForm._date_entree_prevue);
  canNullOK(oForm._date_sortie_prevue);
}

function updateSortiePrevue() {
  var oForm = document.editSejour;
    
  if (!oForm._duree_prevue.value) {
    $V(oForm._duree_prevue, 0);
  }
  
  var sDate = oForm._date_entree_prevue.value;
  if (!sDate) {
    return;
  }
  
  // Add days
  var dDate = Date.fromDATE(sDate);
  var nDuree = parseInt(oForm._duree_prevue.value, 10);
    
  dDate.addDays(nDuree);

  // Update fields
  $V(oForm._date_sortie_prevue, dDate.toDATE());
  oView = getForm('editSejour')._date_sortie_prevue_da;
  $V(oView, dDate.toLocaleDate());
  updateHeureSortie();
  
  // Si meme jour, sortie apres entree
  if (nDuree == 0){
    oForm._hour_sortie_prevue.value = Math.max(oForm._hour_sortie_prevue.value, parseInt(oForm._hour_entree_prevue.value,10)+1);
  }
}

function updateDureePrevue() {
  var oForm = document.editSejour;
  
  if(oForm._date_entree_prevue.value) {
    var dEntreePrevue = Date.fromDATE(oForm._date_entree_prevue.value);
    var dSortiePrevue = Date.fromDATE(oForm._date_sortie_prevue.value);
    var iSecondsDelta = dSortiePrevue - dEntreePrevue;
    var iDaysDelta = iSecondsDelta / (24 * 60 * 60 * 1000);
    $V(oForm._duree_prevue, Math.round(iDaysDelta));
  }
}

function updateHeureSortie() {
  var oForm = document.editSejour

  duree_prevu  = oForm._duree_prevue; 
  heure_sortie = oForm._hour_sortie_prevue;
  min_sortie   = oForm._min_sortie_prevue;
  
  heure_sortie.value = duree_prevu.value < 1 ? "{{$heure_sortie_ambu}}" : "{{$heure_sortie_autre}}";
  min_sortie.value = "0";
}

function checkSejoursToReload() {
  if(!$("selectSejours")) {
    return;
  }
  
  var oForm = document.editSejour;
  if(window.bChangePat) {
    window.bChangePat = 0;
    if(window.bOldPat && oForm.sejour_id.value) {
      if (confirm('Voulez-vous créer un nouveau sejour pour ce patient ?')) {
        if($("selectSejours")) {
          reloadListSejours();
        }
      }
    } else {
    
      reloadListSejours();
    }
    window.bOldPat = 1;
  }
}

function reloadListSejours() {

  
  var oForm = document.editSejour;
  var iPatient_id = oForm.patient_id.value;
  var sejoursUrl = new Url("dPplanningOp", "httpreq_get_sejours");
  sejoursUrl.addParam("patient_id", iPatient_id);
  sejoursUrl.requestUpdate("selectSejours");
  
  // La liste des prescriptions doit etre rechargée
  if (PrescriptionEditor) {
    PrescriptionEditor.refresh($V(oForm.sejour_id), "CSejour", $V(oForm.praticien_id));
  }
}

function reloadSejour(sejour_id) {
  var oFormSejour    = document.editSejour;
  var oFormOp        = document.editOp;
  
  var sDP            = $V(oFormSejour.DP);
  var sDateEntree    = $V(oFormSejour._date_entree_prevue);
  var sHeureEntree   = $V(oFormSejour._hour_entree_prevue);
  var sMinutesEntree = $V(oFormSejour._min_entree_prevue);
  var sDateSortie    = $V(oFormSejour._date_sortie_prevue);
  var sHeureSortie   = $V(oFormSejour._hour_sortie_prevue);
  var sMinutesSortie = $V(oFormSejour._min_sortie_prevue);
  
  var sejoursUrl = new Url("dPplanningOp", "httpreq_vw_sejour");
  sejoursUrl.addParam("sejour_id", $V(oFormSejour.sejour_id));
  sejoursUrl.addParam("patient_id", $V(oFormSejour.patient_id));
  if(oFormOp) {
    sejoursUrl.addParam("mode_operation", 1);
  }
  sejoursUrl.requestUpdate('inc_form_sejour', { onComplete: function() { 
    checkNewSejour(sDP,  sDateEntree, sHeureEntree, sMinutesEntree, sDateSortie, sHeureSortie, sMinutesSortie);} 
  } );
}

function checkNewSejour(sDP,  sDateEntree, sHeureEntree, sMinutesEntree, sDateSortie, sHeureSortie, sMinutesSortie) {
  var oFormSejour       = getForm('editSejour');
  var oSejourChooserFrm = getForm('sejourChooserFrm');
  $V(oSejourChooserFrm.majDP    , 0);
  $V(oSejourChooserFrm.majEntree, 0);
  $V(oSejourChooserFrm.majSortie, 0);
  
  if(!$V(oFormSejour.DP)) {
    $V(oFormSejour.DP, sDP);
    $('chooseDiag').hide();
  } else if(sDP && sDP != $V(oFormSejour.DP)) {
    oSejourChooserFrm.elements.valueDiag[1].value =  sDP;
    $('chooseOldDiag').update(sDP);
    oSejourChooserFrm.elements.valueDiag[0].value = $V(oFormSejour.DP);
    $('chooseNewDiag').update($V(oFormSejour.DP));
    $V(oSejourChooserFrm.majDP, 1);
    $('chooseDiag').show();
  } else {
    $('chooseDiag').hide();
  }
  if(sDateEntree && sDateEntree+sHeureEntree+sMinutesEntree != $V(oFormSejour._date_entree_prevue)+$V(oFormSejour._hour_entree_prevue)+$V(oFormSejour._min_entree_prevue)) {
    var oEntreeOld = Date.fromDATETIME(sDateEntree+" "+sHeureEntree.pad('0', 2, false)+":"+sMinutesEntree.pad('0', 2, false)+":00");
    var oEntreeNew = Date.fromDATETIME($V(oFormSejour._date_entree_prevue)+" "+$V(oFormSejour._hour_entree_prevue).pad('0', 2, false)+":"+$V(oFormSejour._min_entree_prevue).pad('0', 2, false)+":00");
    oSejourChooserFrm.elements.valueAdm[1].value =  oEntreeOld.toDATETIME();
    $('chooseOldAdm').update(oEntreeOld.toLocaleDateTime());
    oSejourChooserFrm.elements.valueAdm[0].value = oEntreeNew.toDATETIME();
    $('chooseNewAdm').update(oEntreeNew.toLocaleDateTime());
    $V(oSejourChooserFrm.majEntree, 1);
    $('chooseAdm').show();
  } else {
    $('chooseAdm').hide();
  }
  if(sDateSortie && sDateSortie+sHeureSortie+sMinutesSortie != $V(oFormSejour._date_sortie_prevue)+$V(oFormSejour._hour_sortie_prevue)+$V(oFormSejour._min_sortie_prevue)) {
    var oSortieOld = Date.fromDATETIME(sDateSortie+" "+sHeureSortie.pad('0', 2, false)+":"+sMinutesSortie.pad('0', 2, false)+":00");
    var oSortieNew = Date.fromDATETIME($V(oFormSejour._date_sortie_prevue)+" "+$V(oFormSejour._hour_sortie_prevue).pad('0', 2, false)+":"+$V(oFormSejour._min_sortie_prevue).pad('0', 2, false)+":00");
    oSejourChooserFrm.elements.valueSortie[1].value =  oSortieOld.toDATETIME();
    $('chooseOldSortie').update(oSortieOld.toLocaleDateTime());
    oSejourChooserFrm.elements.valueSortie[0].value = oSortieNew.toDATETIME();
    $('chooseNewSortie').update(oSortieNew.toLocaleDateTime());
    $V(oSejourChooserFrm.majSortie, 1);
    $('chooseSortie').show();
  } else {
    $('chooseSortie').hide();
  }
  if($V(oSejourChooserFrm.majDP) == 1 || $V(oSejourChooserFrm.majEntree) == 1 || $V(oSejourChooserFrm.majSortie) == 1) {
    changeSejourModal = modal($('sejour-value-chooser'));
  }
}

function applyNewSejour() {
  var oFormSejour       = getForm('editSejour');
  var oSejourChooserFrm = getForm('sejourChooserFrm');
  if($V(oSejourChooserFrm.majDP) == 1) {
    $V(oFormSejour.DP, $V(oSejourChooserFrm.valueDiag));
  }
  if($V(oSejourChooserFrm.majEntree) == 1) {
    oEntree = Date.fromDATETIME($V(oSejourChooserFrm.valueAdm));
    $V(oFormSejour._date_entree_prevue   , oEntree.toDATE());
    $V(oFormSejour._date_entree_prevue_da, oEntree.toLocaleDate());
    $V(oFormSejour._hour_entree_prevue   , oEntree.getHours());
    $V(oFormSejour._min_entree_prevue    , oEntree.getMinutes());
  }
  if($V(oSejourChooserFrm.majSortie) == 1) {
    oSortie = Date.fromDATETIME($V(oSejourChooserFrm.valueSortie));
    $V(oFormSejour._date_sortie_prevue   , oSortie.toDATE());
    $V(oFormSejour._date_sortie_prevue_da, oSortie.toLocaleDate());
    $V(oFormSejour._hour_sortie_prevue   , oSortie.getHours());
    $V(oFormSejour._min_sortie_prevue    , oSortie.getMinutes());
  }
  changeSejourModal.close();
}

function changePat() {
  window.bChangePat = 1;
  checkSejoursToReload();
  checkCorrespondantMedical();
  checkAld();
}

checkCorrespondantMedical = function(){
  var oForm = getForm("editSejour");
  var url = new Url("dPplanningOp", "ajax_check_correspondant_medical");
  url.addParam("patient_id", $V(oForm.patient_id));
  url.addParam("object_id" , $V(oForm.sejour_id));
  url.addParam("object_class", '{{$sejour->_class}}');
  url.requestUpdate("correspondant_medical");
}

var OccupationServices =  {
  dateInitiale  : null,
  tauxOccupation: null,
  configBlocage : null,
  
  initOccupation: function() {
    var oForm = getForm("editSejour");
    this.dateInitiale = $V(oForm._date_entree_prevue);
    this.updateOccupation();
  },
  
  updateOccupation: function() {
    var oForm = getForm("editSejour");
    var occupationUrl = new Url("dPplanningOp", "httpreq_show_occupation_lits");
    occupationUrl.addElement(oForm.type, "type");
    occupationUrl.addElement(oForm._date_entree_prevue, "entree");
    occupationUrl.requestUpdate('occupation');
    if(document.editOp) {
      occupationUrl.requestUpdate('occupationeasy');
    }
  },
  
  testOccupation: function() {
    if(this.configBlocage != '1') {
      return true;
    }
    var oForm = getForm("editSejour");
    if(this.dateInitiale != $V(oForm._date_entree_prevue) && this.tauxOccupation >= 100) {
      alert("L'occupation des services est de "+this.tauxOccupation+"%.\nVeuillez contacter le responsable des services")
      return false;
    }
    return true;
  }
};

window.bChangePat = 0;
window.bOldPat = 0;

</script>
