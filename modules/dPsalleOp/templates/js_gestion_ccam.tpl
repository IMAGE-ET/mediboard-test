{{mb_include_script module="dPplanningOp" script="ccam_selector"}}

<script type="text/javascript">

ActesCCAM = {
  refreshList: function(subject_id, chir_id){
    PairEffect.initGroup("acteEffect");
    //rafraichissement du inc_fdr
    if("{{$module}}" == "dPcabinet"){
      refreshFdr(subject_id);
    }
    url_actes = new Url;
    url_actes.addParam("chir_id", chir_id);  
    url_actes.addParam("module","{{$module}}");
    url_actes.addParam("do_subject_aed","{{$do_subject_aed}}");
    url_actes.addParam("object_class", "{{$object->_class_name}}");
    url_actes.addParam("object_id", subject_id);
    url_actes.setModuleAction("dPsalleOp", "httpreq_ccam");
    url_actes.requestUpdate('ccam', {
      waitingText: null
    });
  },
  
  add: function(subject_id, chir_id, oOptions){
    var oDefaultOptions = { 
      onComplete: function() { ActesCCAM.refreshList(subject_id, chir_id) }
    }
    Object.extend(oDefaultOptions, oOptions);
    var oForm = document.manageCodes;
    var oCcamField = new TokenField(oForm.codes_ccam, {
      sProps : "notNull code ccam"
    } );
    if(oCcamField.add(oForm._newCode.value)){
      submitFormAjax(oForm, 'systemMsg', oDefaultOptions);
    }
  },
  
  remove: function(subject_id, oOptions){
    var oDefaultOptions = { 
      onComplete: function() { ActesCCAM.refreshList(subject_id) } 
    }
    Object.extend(oDefaultOptions, oOptions);
    var oForm = document.manageCodes;
    var oCcamField = new TokenField(oForm.codes_ccam);
    if(oForm._selCode.value == 0){
      alert("Aucun code selectionné");
      return false;
    }
    if(oCcamField.remove(oForm._selCode.value)){
      submitFormAjax(oForm, 'systemMsg', oDefaultOptions);
    }
  }
}

function setCodeTemp(code){
  var oForm = document.manageCodes;
  oForm._newCode.value = code; 
}

function setAssociation(association, oForm, subject_id, chir_id, oOptions) {
  var oDefaultOptions = { 
    onComplete: function() { ActesCCAM.refreshList(subject_id, chir_id) } 
  }
  Object.extend(oDefaultOptions, oOptions);
  oForm.code_association.value = association;
  submitFormAjax(oForm, 'systemMsg', oDefaultOptions)
}

function refreshFdr(consult_id) {
  // reload pour mettre a jour le champ codes_ccam dans la gestion des tarifs
  var url = new Url;
  url.setModuleAction("dPcabinet", "httpreq_vw_fdr_consult");
  url.addParam("selConsult", consult_id);
  url.requestUpdate('fdrConsultContent', { waitingText : null }) ;
}

</script>