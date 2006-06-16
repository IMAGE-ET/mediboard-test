<!-- $Id: $ -->

<script type="text/javascript">
  
function putCCAM(code) {
  aSpec = new Array();
  aSpec[0] = "code";
  aSpec[1] = "ccam";
  aSpec[2] = "notNull";
  oCode = new Object();
  oCode.value = code
  if(sAlert = checkElement(oCode, aSpec)) {
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
    sCodeNode += "<button type='button' onclick='delCCAM(\"" + sCode + "\")'>";
    sCodeNode += "<img src='modules/dPplanningOp/images/cross.png' />";
    sCodeNode += "<\/button>";
    aCodeNodes.push(sCodeNode);
  }
  oCcamNode.innerHTML = aCodeNodes.join(" &mdash; ");
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
    return false
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
      popChir();
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

function modifOp() {
  modifSejour();
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
  var form = document.editOp;

  if (plage_id) {
    form.plageop_id.value = plage_id;
    form.date.value = sDate;
    
    // Initialize adminission date according to operation date
    var dAdm = makeDateFromLocaleDate(sDate);
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
      form._hour_adm.value = dAdm.getHours();
      form._min_adm.value = dAdm.getMinutes();
      form.date_adm.value = makeDATEFromDate(dAdm);
    

      var div_rdv_adm = document.getElementById("editOp_date_adm_da");
      div_rdv_adm.innerHTML = makeLocaleDateFromDate(dAdm);
    }
  }
}
  
function incFormOperationMain() {
  regFieldCalendar("editOp", "date_anesth");
  refreshListCCAM();
}

</script>
