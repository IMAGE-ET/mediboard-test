<!-- $Id: $ -->

<script type="text/javascript">

function checkDureeHospi() {
  var form = document.editSejour;
  field1 = form.type;
  field2 = form._duree_prevue;
  if (field1 && field2) {
    if (field1[0].checked && (field2.value == 0 || field2.value == '')) {
      field2.value = prompt("Veuillez saisir une durée prévue d'hospitalisation d'au moins 1 jour", "1");
      field2.onchange();
      field2.focus();
      return false;
    }
    if (field1[1].checked && field2.value != 0 && field2.value != '') {
      alert('Pour une admission de type ambulatoire, la durée du séjour doit être de 0 jour.');
      field2.focus();
      return false;
    }
  }
  return true;
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
      oFormOp._datestr.value = "";
      oFormOp.plageop_id.value = "";
      oFormOp._date.value = "";
      oFormOp.date.value = "";
    }
  }
}

function cancelSejour() {
  var oForm = document.editSejour;
  var oElement = oForm.annule;
  
  if (oElement.value == "0") {
    if (confirm("Voulez-vous vraiment annuler le séjour ?\nTous les placements dans les lits seront supprimés.\n{{$msg_alert|smarty:nodefaults|escape:"javascript"}}")) {
      oElement.value = "1";
      oForm.submit();
      return;
    }
  }
      
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
  if (oForm.saisi_SHS.value == 'o') {
    oForm.modif_SHS.value = 1;
    oForm.saisi_SHS.value = 'n';
  }
}

function updateSortiePrevue() {
  var oForm = document.editSejour;
    
  if(!oForm._duree_prevue.value) {
    oForm._duree_prevue.value = 0;
  }
  
  if(oForm._date_entree_prevue.value) {
    var dDate = makeDateFromDATE(oForm._date_entree_prevue.value);
    var iDelta = parseInt(oForm._duree_prevue.value, 10);
    if(iDelta) {
      dDate.setDate(dDate.getDate() + iDelta);
    }
    oForm._date_sortie_prevue.value = makeDATEFromDate(dDate);
    oForm._date_sortie_prevue.onchange();
    oDiv = document.getElementById('editSejour__date_sortie_prevue_da');
    oDiv.innerHTML = makeLocaleDateFromDate(dDate);
    updateHeureSortie();
  }
}

function updateDureePrevue() {
  var oForm = document.editSejour;
  
  if(oForm._date_entree_prevue.value) {
    var dEntreePrevue = makeDateFromDATE(oForm._date_entree_prevue.value);
    var dSortiePrevue = makeDateFromDATE(oForm._date_sortie_prevue.value);
    var iSecondsDelta = dSortiePrevue - dEntreePrevue;
    var iDaysDelta = iSecondsDelta / (24 * 60 * 60 * 1000);
    oForm._duree_prevue.value = Math.floor(iDaysDelta);
  }
}

function updateHeureSortie() {
  var oForm = document.editSejour

  duree_prevu  = oForm._duree_prevue; 
  heure_sortie = oForm._hour_sortie_prevue;
  min_sortie   = oForm._min_sortie_prevue;
  if(duree_prevu.value <= 1){
    heure_sortie.value = "18";
  }else{
    heure_sortie.value = "10";
  }
  min_sortie.value = "00";
}

function popPat() {
  var url = new Url();
  url.setModuleAction("dPpatients", "pat_selector");
  url.popup(800, 500, "Patient");
}

function setPat(patient_id, _patient_view, childWindow) {
  var oForm = document.editSejour;

  if (patient_id) {
    oForm.patient_id.value = patient_id;
    oForm._patient_view.value = _patient_view;
    bChangePat = 1;
  }
  
  oForm.patient_id.onchange();
}

function checkSejoursToReload() {
  if(bChangePat) {
    reloadListSejours();
    bChangePat = 0;
  }
}

function reloadListSejours() {
  var sejoursUrl = new Url;
  var oForm = document.editSejour;
  var iPatient_id = oForm.patient_id.value;
  sejoursUrl.setModuleAction("dPplanningOp", "httpreq_get_sejours");
  sejoursUrl.addParam("patient_id", iPatient_id);
  sejoursUrl.requestUpdate('selectSejours', { waitingText : null });
}

function reloadSejour(sejour_id) {
  var sejoursUrl = new Url;
  var oForm = document.editSejour;
  var iPatient_id = oForm.patient_id.value;
  var iSejour_id = oForm.sejour_id.value;
  sejoursUrl.setModuleAction("dPplanningOp", "httpreq_vw_sejour");
  sejoursUrl.addParam("sejour_id", iSejour_id);
  sejoursUrl.addParam("patient_id", iPatient_id);
  if(document.editOp) {
    sejoursUrl.addParam("mode_operation", 1);
  }
  sejoursUrl.requestUpdate('inc_form_sejour', { waitingText : null });
}

function incFormSejourMain() {
}

var bChangePat = 0;

</script>
