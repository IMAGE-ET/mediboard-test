<!-- $Id$ -->

<script type="text/javascript">

var oCcamField = null;
function updateTokenCcam(){
  refreshListCCAM("expert");
  refreshListCCAM("easy");
  document.editOp._codeCCAM.value="";
  modifOp();
}



function refreshListCCAM(mode) {
  if(mode=="expert"){
    oCcamNode = $("listCodesCcam");
  }
  if(mode=="easy"){
    oCcamNode = ("listCodesCcamEasy");
  }
  var oForm = document.editOp;
  var aCcam = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCcam = aCcam.without("");
  
  var aCodeNodes = new Array();
  var iCode = 0;
  while (sCode = aCcam[iCode++]) {
    var sCodeNode = sCode;
    if(mode=="expert"){
      sCodeNode += "<button class='cancel notext' type='button' onclick='oCcamField.remove(\"" + sCode + "\")'>";
      sCodeNode += "<\/button>";
    }
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
    
    var dureeUrl = new Url;
    timeUrl.setModuleAction("dPplanningOp", "httpreq_get_hospi_time");
    timeUrl.addElement(oForm.chir_id, "chir_id");
    timeUrl.addElement(oForm.codes_ccam, "codes");
    timeUrl.requestUpdate('dureeEst', { waitingText : null });
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
    if(!oCcamField.add(sCcam,true)) {
      return false;
    }
  }
  oCcamField.remove("XXXXXX");
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
      alert("Date d'admission superieure à la date d'intervention");
      oSejourForm._date_entree_prevue.focus();
      return false;
    }
  }
  // cas normal
  if(oOpForm._date.value && oSejourForm._date_entree_prevue.value) {
    if(oOpForm._date.value < oSejourForm._date_entree_prevue.value) {
      alert("Date d'admission superieure à la date d'intervention");
      oSejourForm._date_entree_prevue.focus();
      return false;
    }
  }
  return true;
}

function modifOp() {
  modifSejour();
}

var Value = {
  // Synchronize elements value between Easy and Expert forms
  synchronize: function(element) {
    var oOtherForm = element.form.name == "editOp" ?
      document.editOpEasy :
      document.editOp;
    $V(oOtherForm[element.name], element.value);
  }
}

function setMinVoulu(oForm) {
  if(oForm._hour_voulu.value && !oForm._min_voulu.value) {
    oForm._min_voulu.value = "00";
    oForm._min_voulu.onchange();
  } else if(!oForm._hour_voulu.value) {
    oForm._min_voulu.value = "";
    oForm._min_voulu.onchange();
  }
}

function synchroPrat() {
  var oOpForm = document.editOp;
  var oSejourForm = document.editSejour;
  if (!oSejourForm.sejour_id.value) {
    $V(oSejourForm.praticien_id, oOpForm.chir_id.value);
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
    if(!oSejourForm._date_entree_prevue.value) {
      oSejourForm._date_entree_prevue.value = oOpForm.date.value;
      oDiv = $('editSejour__date_entree_prevue_da');
      oDiv.innerHTML = Date.fromDATE(oOpForm.date.value).toLocaleDate();
    }
  }
  
  updateSortiePrevue();
}

CanBloc = {{$modules.dPbloc->_can|json}};	

function cancelOperation() {  
  var oForm = document.editOp;
  var oElement = oForm.annulee;
  
  if (oElement.value == "0") {
	  // Tester supérieur à 0 semble obligatoire
	  if (oForm._count_actes.value > 0) {
	    var msg = "Attention, l'intervention a probablement déjà eu lieu.\n\n";
	
	  	if (CanBloc.edit) {
	  	  if (!confirm(msg + "Voulez-vous malgré tout l'annuler ?")) {
	  	  	return;
	  	 	}
	  	}
	  	else {
	  	  alert(msg + "Veuillez vous adresser au responsable de bloc pour annuler cette intervention");
	  	  return;
	  	}
	  }
	  
    if (confirm("Voulez-vous vraiment annuler l'intervention ?")) {
      if (confirm("Souhaitez-vous annuler le Séjour correspondant ?\n\nATTENTION, cette action annulera toutes les interventions de ce séjour !")) {
        if (checkCancelAlerts()) {
         	document.editSejour.annule.value = 1;
        }
      }
      
      oElement.value = "1";
      submitForms();
      return;
    }
  }
      
  if (oElement.value == "1") {
    var txtalert = "";
    if(document.editSejour.annule.value == 1){
      txtalert = "\n\n ATTENTION ! Cette intervention va rétablir le séjour choisi.";
    }      
    if (confirm("Voulez-vous vraiment rétablir l'intervention ?" + txtalert)) {
      oElement.value = "0";
      oForm.submit();
      return;
    }
  }
}
  
var periodicalTimeUpdater = null;
  
function incFormOperationMain() {
  periodicalTimeUpdater = new PeriodicalExecuter(updateTime, 1);

  refreshListCCAM("expert");
  refreshListCCAM("easy");
  
  if({{$modurgence && !$op->operation_id}}) {
    updateEntreePrevue();
  }
    
  oCcamField = new TokenField(document.editOp.codes_ccam, { 
    onChange : updateTokenCcam,
    sProps : "notNull code ccam"
  } );
}

Main.add(incFormOperationMain);

</script>
