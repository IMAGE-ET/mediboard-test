window.Facture = {
  modal: null,
  url: null,
  reload: function(patient_id, consult_id, not_load_banque, facture_id) {
    var url = new Url('dPcabinet' , 'ajax_view_facture');
    url.addParam('patient_id'     , patient_id);
    url.addParam('consult_id'     , consult_id);
    url.addParam('not_load_banque', not_load_banque);
    url.addParam('facture_id'     , facture_id);
    url.requestUpdate('load_facture');
  },
  edit: function(facture_id, facture_class, show_button) {
    show_button = show_button || 1;
    var url = new Url('facturation', 'ajax_view_facture');
    url.addParam('facture_id'    , facture_id);
    url.addParam("object_class", facture_class);
    url.addParam("show_button", show_button);
    url.requestModal(1000);
    Facture.url = url;
  },
  modifCloture: function(form) {
    onSubmitFormAjax(form, {
      onComplete : function() {
        if (!$('load_facture')) {
          Facture.url.refreshModal();
        }
        else {
          var url = new Url('facturation' , 'ajax_view_facture');
          url.addElement(form.facture_id);
          url.addParam('object_class'  , form.facture_class.value);
          url.requestUpdate('load_facture');
        }
      }
    });
  },
  reloadReglement: function(facture_id, facture_class) {
    var url = new Url('facturation', 'ajax_refresh_reglement');
    url.addParam('facture_id'    , facture_id);
    url.addParam('facture_class' , facture_class);
    url.requestUpdate('reglements_facture');
  }
};