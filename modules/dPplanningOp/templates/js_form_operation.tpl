<!-- $Id: $ -->

<script type="text/javascript">
  
function putCCAM(code) {
  if(!code) {
    refreshListCCAM();
    return false;
  }
  aSpec = new Array();
  aSpec[0] = "code";
  aSpec[1] = "ccam";
  aSpec[2] = "notNull";
  oCode = new Object();
  oCode.value = code
  if(sAlert = checkElement(oCode, aSpec)) {
    refreshListCCAM();
    alert(sAlert);
    return false;
  }
  else {
    var oForm = document.editOp;
    aCcam = oForm.codes_ccam.value.split("|");
    // Si la chaine est vide, il crée un tableau à un élément vide donc :
    aCcam.removeByValue("");
    aCcam.push(code);
    oForm.codes_ccam.value = aCcam.join("|");
    oForm._codeCCAM.value = "";
    refreshListCCAM();
    modifOp();
    return true;
  }
}

function delCCAM(code) {
  var oForm = document.editOp;
  var aCcam = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCcam.removeByValue("");
  aCcam.removeByValue(code, true);
  oForm.codes_ccam.value = aCcam.join("|");
  refreshListCCAM();
  modifOp();
}

var periodicalTimeUpdater = new PeriodicalExecuter(updateTime, 1);

function refreshListCCAM() {
  oCcamNode = document.getElementById("listCodesCcam");
  var oForm = document.editOp;
  var aCcam = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCcam.removeByValue("");
  
  var aCodeNodes = new Array();
  var iCode = 0;
  while (sCode = aCcam[iCode++]) {
    var sCodeNode = sCode;
    sCodeNode += "<button class='cancel notext' type='button' onclick='delCCAM(\"" + sCode + "\")'>";
    sCodeNode += "<\/button>";
    aCodeNodes.push(sCodeNode);
  }
  oCcamNode.innerHTML = aCodeNodes.join(" &mdash; ");
  periodicalTimeUpdater.currentlyExecuting = false;
}

function updateTime() {
  var oForm = document.editOp;
  if(oForm.chir_id.value) {
    var timeUrl = new Url;
    timeUrl.setModuleAction("dPplanningOp", "httpreq_get_op_time");
    timeUrl.addElement(oForm.chir_id, "chir_id");
    timeUrl.addElement(oForm.codes_ccam, "codes");
    timeUrl.requestUpdate('timeEst', { waitingText : null });
  }
}

function checkFormOperation() {
  var oForm = document.editOp;
  
  if (!checkForm(oForm)) {
    return false;
  }

  if (!checkDuree()) {
    return false;
  }
  
  if(!checkCCAM()) {
    return false;
  }
  
  if(!checkCompatOpAdm()) {
    return false;
  }

  return true;
}

function checkCCAM() {
  var oForm = document.editOp;
  var sCcam = oForm._codeCCAM.value;
  if(sCcam != "") {
    if(!putCCAM(sCcam)) {
      return false;
    }
  }
  delCCAM("XXXXXX");
  var sCodesCcam = oForm.codes_ccam.value;
  var sLibelle = oForm.libelle.value;
  if(sCodesCcam == "" && sLibelle == "") {
    alert("Vous indiquez un acte ou remplir le libellé")
    oForm.libelle.focus();
    return false
  }
  return true;
}

function checkChir() {
  var oForm = document.editOp;
  var oField = null;
  
  if (oField = oForm.chir_id) {
    if (oField.value == 0) {
      alert("Chirurgien manquant");
      return false;
    }
  }
  return true;
}

function checkDuree() {
  var form = document.editOp;
  field1 = form._hour_op;
  field2 = form._min_op;
  if (field1 && field2) {
    if (field1.value == 0 && field2.value == 0) {
      alert("Temps opératoire invalide");
      field1.focus();
      return false;
    }
  }
  return true
}

function checkCompatOpAdm() {
  var oOpForm     = document.editOp;
  var oSejourForm = document.editSejour;
  // cas des urgences
  if(oOpForm.date.value && oSejourForm._date_entree_prevue.value) {
    if(oOpForm.date.value < oSejourForm._date_entree_prevue.value) {
      alert("Date d'admission superieure à la date d'opération");
      oSejourForm._date_entree_prevue.focus();
      return false;
    }
  }
  // cas normal
  if(oOpForm._date.value && oSejourForm._date_entree_prevue.value) {
    if(oOpForm._date.value < oSejourForm._date_entree_prevue.value) {
      alert("Date d'admission superieure à la date d'opération");
      oSejourForm._date_entree_prevue.focus();
      return false;
    }
  }
  return true;
}

function modifOp() {
  modifSejour();
}

function synchroPrat() {
  var oOpForm = document.editOp;
  var oSejourForm = document.editSejour;
  if(!oSejourForm.praticien_id.value) {
    oSejourForm.praticien_id.value = oOpForm.chir_id.value;
  }
  updateTime();
}

function updateEntreePrevue() {
  var oOpForm = document.editOp;
  var oSejourForm = document.editSejour;
    
  if(!oSejourForm._duree_prevue.value) {
    oSejourForm._duree_prevue.value = 0;
  }

  if(oOpForm.date.value) {
    oSejourForm._date_entree_prevue.value = oOpForm.date.value;
    var dDate = makeDateFromDATE(oOpForm.date.value);
    oDiv = document.getElementById('editSejour__date_entree_prevue_da');
    oDiv.innerHTML = makeLocaleDateFromDate(dDate);
  }
  
  updateSortiePrevue();
}

function popPlage() {
  var oForm = document.editOp;
  if (checkChir() && checkDuree()) {
    var url = new Url();
    url.setModuleAction("dPplanningOp", "plage_selector");
    url.addElement(oForm.chir_id, "chir");
    url.addElement(oForm._hour_op, "curr_op_hour");
    url.addElement(oForm._min_op, "curr_op_min");
    url.popup(400, 250, 'Plage');
  }
}

function setPlage(plage_id, sDate, bAdm) {
  var oOpForm     = document.editOp;
  var oSejourForm = document.editSejour;
    
  if(!oSejourForm._duree_prevue.value) {
    oSejourForm._duree_prevue.value = 0;
  }

  if (plage_id) {
    oOpForm.plageop_id.value = plage_id;
    oOpForm._datestr.value = sDate;
    var dAdm = makeDateFromLocaleDate(sDate);
    oOpForm._date.value = makeDATEFromDate(dAdm);
    
    // Initialize admission date according to operation date
    switch(bAdm) {
      case 0 :
        dAdm.setHours(17);
        dAdm.setDate(dAdm.getDate()-1);
        break;
      case 1 :
        dAdm.setHours(8);
        break;
    }
    
    if (bAdm != 2) {
      oSejourForm._hour_entree_prevue.value = dAdm.getHours();
      oSejourForm._min_entree_prevue.value = dAdm.getMinutes();
      oSejourForm._date_entree_prevue.value = makeDATEFromDate(dAdm);
      var div_rdv_adm = document.getElementById("editSejour__date_entree_prevue_da");
      div_rdv_adm.innerHTML = makeLocaleDateFromDate(dAdm);
    }
    
    updateSortiePrevue();
  }
}

function cancelOperation() {
  var oForm = document.editOp;
  var oElement = oForm.annulee;
  
  if (oElement.value == "0") {
    if (confirm("Voulez-vous vraiment annuler l'opération ?")) {
      oElement.value = "1";
      oForm.submit();
      return;
    }
  }
      
  if (oElement.value == "1") {
    var txtalert = "";
    if(document.editSejour.annule.value == 1){
      txtalert = "\n\n ATTENTION ! Cette opération va rétablir le séjour choisi.";
    }      
    if (confirm("Voulez-vous vraiment rétablir l'opération ?" + txtalert)) {
      oElement.value = "0";
      oForm.submit();
      return;
    }
  }
}
  
function incFormOperationMain() {
  regFieldCalendar("editOp", "date_anesth");
  refreshListCCAM();
  if({{$modurgence && !$op->operation_id}}) {
    updateEntreePrevue();
  }
}

</script>
