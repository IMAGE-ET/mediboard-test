var Protocole = {
  // Ajout de protocole
  add : function(){
    var oFormPrat = document.selPrat;
    var oForm = document.addProtocolePresc;
    oForm.praticien_id.value = oFormPrat.praticien_id.value;
    return onSubmitFormAjax(oForm);
  },
  // Suppression de protocole
  remove : function(oForm){
    var oFormPrat = document.selPrat;
    submitFormAjax(oForm, 'systemMsg', {
      onComplete: function(){
        Protocole.refreshList(oFormPrat.praticien_id.value);
      } 
    } );
  },
  // Refresh de la liste des protocoles
  refreshList : function(praticien_id, protocoleSel_id) {
    var url = new Url;
    url.setModuleAction("dPprescription", "httpreq_vw_list_protocoles");
    url.addParam("praticien_id", praticien_id);
    url.addParam("protocoleSel_id", protocoleSel_id);
    url.requestUpdate("protocoles", { waitingText: null } );
  },
  // Edition d'un protocole
  edit : function(protocole_id, praticien_id) {
    Prescription.reload(protocole_id,"","","1");
    Protocole.refreshList(praticien_id, protocole_id);
  }
}