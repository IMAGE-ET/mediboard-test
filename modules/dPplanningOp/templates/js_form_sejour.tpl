<!-- $Id: $ -->

<script type="text/javascript">

function confirmAnnulation() {
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

function checkSejourToReload() {
  if(bChangePat) {
    reloadSejour();
    bChangePat = 0;
  }
}

function reloadSejour(sejour_id) {
  var sejoursUrl = new Url;
  var oForm = document.editSejour;
  var iPatient_id = oForm.patient_id.value;
  var iSejour_id = oForm.sejour_id.value;
  sejoursUrl.setModuleAction("dPplanningOp", "httpreq_vw_sejour");
  sejoursUrl.addParam("sejour_id", iSejour_id);
  sejoursUrl.addParam("patient_id", iPatient_id);
  sejoursUrl.requestUpdate('inc_form_sejour', { waitingText : null });
}

function incFormSejourMain() {
}

var bChangePat = 0;

</script>
