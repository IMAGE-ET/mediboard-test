var Reglement = {
  consultation_id   : null,
  user_id : null,

  register: function(){
    document.write('<div id="reglement"></div>');
    Main.add(Reglement.reload.curry(false));
  },
  submit: function(oForm, reload_acts) {
	  submitFormAjax(oForm, 'systemMsg', {
	    onComplete : 
	      function() {
	        Reglement.reload(reload_acts);
	        if(Preferences.autoCloseConsult == "1"){
	          reloadFinishBanner();
	        }
	      }
	  } );
  }, 
  reload: function(reload_acts) {
	  var url = new Url("dPcabinet", "httpreq_vw_reglement");
    url.addParam("selConsult", document.editFrmFinish.consultation_id.value);
	  url.requestUpdate('reglement');
    
    // Rafraichissement des actes CCAM et NGAP
    if (reload_acts && Preferences.ccam_consultation == "1" && Preferences.MODCONSULT == "1"){
      ActesCCAM.refreshList(Reglement.consultation_id, Reglement.user_id);
      ActesNGAP.refreshList();
    }
  },
  effectuer: function(){
    var oForm = getForm("tarifFrm");
    $V(oForm.patient_date_reglement, new Date().toDATE());
    Reglement.submit(oForm);
  },
  cancel: function (reglement_id) {
    var oForm = getForm('reglement-delete');
    $V(oForm.reglement_id, reglement_id);
    confirmDeletion(oForm, { ajax: true, typeName:'le règlement' }, { onComplete : Reglement.reload.curry(false) } );
    return false;
  }
};