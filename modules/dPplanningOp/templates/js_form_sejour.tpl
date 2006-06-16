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

function checkPatToReload() {
  if(bChangePat) {
    reloadSelectSejours();
    bChangePat = 0;
  }
}

function reloadSelectSejours() {
  var sejoursUrl = new Url;
  var iPatient_id = document.editSejour.patient_id.value;
  sejoursUrl.setModuleAction("dPplanningOp", "httpreq_get_sejours");
  sejoursUrl.addParam("patient_id", iPatient_id);
  sejoursUrl.addParam("sejour_id", "{{$sejour->sejour_id}}");
  sejoursUrl.requestUpdate('selectSejours', { waitingText : null });
}

function incFormSejourMain() {
  regFieldCalendar("editSejour", "_date_entree_prevue");
  regFieldCalendar("editSejour", "_date_sortie_prevue");
  reloadSelectSejours();
}

var bChangePat = 0;

</script>
