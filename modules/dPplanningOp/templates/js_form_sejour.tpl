<!-- $Id: $ -->

<script type="text/javascript">

function cancelSejour() {
  var oForm = document.editSejour;
  var oElement = oForm.annule;
  
  if (oElement.value == "0") {
    if (confirm("Voulez-vous vraiment annuler le séjour ?")) {
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
  
  if(oForm._date_entree_prevue.value) {
    var dDate = makeDateFromDATE(oForm._date_entree_prevue.value);
    var iDelta = parseInt(oForm._duree_prevue.value, 10);  
    dDate.setDate(dDate.getDate() + iDelta);
    oForm._date_sortie_prevue.value = makeDATEFromDate(dDate);
    oDiv = document.getElementById('editSejour__date_sortie_prevue_da');
    oDiv.innerHTML = makeLocaleDateFromDate(dDate);
  }
}

function updateDureePrevue() {
  var oForm = document.editSejour;
  
  if(oForm._date_entree_prevue.value) {
    var dEntreePrevue = makeDateFromDATE(oForm._date_entree_prevue.value);
    var dSortiePrevue = makeDateFromDATE(oForm._date_sortie_prevue.value);
    var iSecondsDelta = dSortiePrevue - dEntreePrevue;
    var iDaysDelta = iSecondsDelta / (24 * 60 * 60 * 1000);
    oForm._duree_prevue.value = iDaysDelta;
  }
}

function updateHeureSortie() {
  var oForm = document.editSejour
  
  if(oForm._date_entree_prevue.value == oForm._date_sortie_prevue.value) {
    oForm._hour_sortie_prevue.value = oForm._hour_entree_prevue.value;
    oForm._min_sortie_prevue.value = oForm._min_entree_prevue.value;
  }
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
