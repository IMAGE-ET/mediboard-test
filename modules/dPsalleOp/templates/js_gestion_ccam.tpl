<script type="text/javascript">

function popCodeCCAM(chir_id) {
  var url = new Url();
  url.setModuleAction("dPplanningOp", "code_selector");
  url.addParam("chir", chir_id);  
  url.addParam("type", "ccam");
  url.popup(600, 500, "ccam");
}


function setCodeCCAM( key, type ) {
  if (key) {
    var oForm = document.manageCodes;
    oForm._newCode.value = key;
  }
}

function addCode(subject_id) {
  var oForm = document.manageCodes;
  var aCCAM = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCCAM.removeByValue("");
  if(oForm._newCode.value != '')
    aCCAM.push(oForm._newCode.value);
  aCCAM.sort();
  oForm.codes_ccam.value = aCCAM.join("|");
  submitFormAjax(oForm, 'systemMsg', {onComplete: function(){loadActes(subject_id)}});
}

function loadActes(subject_id) {
  PairEffect.initGroup("acteEffect");

  url_actes = new Url;
  url_actes.addParam("module","{{$module}}");
  url_actes.addParam("do_subject_aed","{{$do_subject_aed}}");
  url_actes.addParam("object_class", "{{$object->_class_name}}");
  url_actes.addParam("object_id", subject_id);
  url_actes.setModuleAction("dPsalleOp", "httpreq_ccam");
  url_actes.requestUpdate('ccam');
}

function delCode(subject_id) {
  var oForm = document.manageCodes;
  var aCCAM = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCCAM.removeByValue("");
  if (oForm._selCode.value != '') {
    aCCAM.removeByValue(oForm._selCode.value, true);
  }
  aCCAM.sort();
  oForm.codes_ccam.value = aCCAM.join("|");
  submitFormAjax(oForm, 'systemMsg', {onComplete: function(){loadActes(subject_id)}});
}

</script>