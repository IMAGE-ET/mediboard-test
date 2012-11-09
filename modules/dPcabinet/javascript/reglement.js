var Reglement = {
  consultation_id   : null,
  user_id : null,

  register: function(){
    document.write('<div id="reglement"></div>');
    Main.add(Reglement.reload.curry(false));
  },

  submit: function(oForm, reload_acts, callback) {
    submitFormAjax(oForm, 'systemMsg', {
      onComplete : function() {
        Reglement.reload(reload_acts, callback);
        if(Preferences.autoCloseConsult == "1"){
          reloadFinishBanner();
        }
      }
  } );
  }, 
  reload: function(reload_acts, callback) {
    var url = new Url("dPcabinet", "httpreq_vw_reglement");
    url.addParam("selConsult", document.editFrmFinish.consultation_id.value);
    url.requestUpdate('reglement', callback);
  
    // Rafraichissement des actes CCAM et NGAP
    if (reload_acts && Preferences.ccam_consultation == "1" && Preferences.MODCONSULT == "1"){
      ActesCCAM.refreshList(Reglement.consultation_id, Reglement.user_id);
      ActesNGAP.refreshList();
     
      if (reload_acts && Preferences.MODCONSULT == "1" && window.ActesTarmed){
        ActesTarmed.refreshList();
        ActesCaisse.refreshList();
      }
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
  },
  updateBanque: function(mode) {
    var banque_id = mode.form.banque_id;
    var num_bvr   = mode.form.num_bvr;
    
    switch($V(mode)) {
      case "cheque":
        $('choice_banque').show();
        $('numero_bvr').hide();
        $V(num_bvr, 0);
        break;
      case "BVR":
        $('numero_bvr').show();
        $('choice_banque').hide();
        $V(banque_id, "");
        break;
      default:
        $('numero_bvr').hide();
        $('choice_banque').hide();
        $V(banque_id, "");
        $V(num_bvr, 0);
    }
  },
  regBanque: function(form, mode) {
    Main.add(function() {
      var index = 0;
      if (mode == "BVR") {
        index = 4;
      }
      Reglement.updateBanque(getForm(form).mode[index]);
    });
  }
};