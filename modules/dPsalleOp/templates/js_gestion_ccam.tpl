{{mb_include_script module="dPplanningOp" script="ccam_selector"}}

<script type="text/javascript">

function addCode(subject_id, chir_id) {
  var oForm = document.manageCodes;
  var aCCAM = oForm.codes_ccam.value.split("|");
  // Si la chaine est vide, il crée un tableau à un élément vide donc :
  aCCAM.removeByValue("");
  if(oForm._newCode.value != '')
    aCCAM.push(oForm._newCode.value);
  aCCAM.sort();
  oForm.codes_ccam.value = aCCAM.join("|");
  submitFormAjax(oForm, 'systemMsg', { onComplete: function() { loadActes(subject_id, chir_id) } } );
}

function loadActes(subject_id, chir_id) {
  PairEffect.initGroup("acteEffect");

  url_actes = new Url;
  url_actes.addParam("chir_id", chir_id);  
  url_actes.addParam("module","{{$module}}");
  url_actes.addParam("do_subject_aed","{{$do_subject_aed}}");
  url_actes.addParam("object_class", "{{$object->_class_name}}");
  url_actes.addParam("object_id", subject_id);
  url_actes.setModuleAction("dPsalleOp", "httpreq_ccam");
  url_actes.requestUpdate('ccam', { waitingText: null } );
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