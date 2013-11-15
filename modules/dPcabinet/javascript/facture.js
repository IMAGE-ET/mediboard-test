window.Facture = {
  modal: null,
  reload: function(patient_id, consult_id, not_load_banque, facture_id) {
    var url = new Url('dPcabinet' , 'ajax_view_facture');
    url.addParam('patient_id'     , patient_id);
    url.addParam('consult_id'     , consult_id);
    url.addParam('not_load_banque', not_load_banque);
    url.addParam('facture_id'     , facture_id);
    url.requestUpdate('load_facture');
  }
};