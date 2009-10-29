var Reglement = {
  consultation_id   : null,
  user_id : null,

  submit: function(oForm) {
	  submitFormAjax(oForm, 'systemMsg', { 
	    onComplete : 
	      function() {
	        Reglement.reload();
	        if(Preferences.autoCloseConsult == "1"){
	          reloadFinishBanner();
	        }
	      }
	  } );
  }, 
  reload: function(){
    var url = new Url("dPcabinet", "httpreq_vw_reglement");
    url.addParam("selConsult", document.editFrmFinish.consultation_id.value);
	  url.requestUpdate('reglement', { waitingText : null });
    
    if(Preferences.ccam_consultation == "1" && Preferences.MODCONSULT == "1"){
      // rafraichissement de la div ccam
      ActesCCAM.refreshList(Reglement.consultation_id, Reglement.user_id);
      ActesNGAP.refreshList();
    }
  },
  effectuer: function(){
    var oForm = getForm("tarifFrm");
    $V(oForm.patient_date_reglement, new Date().toDATE());
    Reglement.submit(oForm);
  },
  register: function(){
    document.write('<div id="reglement"></div>');
    Main.add( function() {
      Reglement.reload();
    } );
  },
  cancel: function (reglement_id) {
    var oForm = getForm('reglement-delete');
    $V(oForm.reglement_id, reglement_id);
    confirmDeletion(oForm, { ajax: true, typeName:'le règlement' }, { onComplete : Reglement.reload } );
    return false;
  }
};