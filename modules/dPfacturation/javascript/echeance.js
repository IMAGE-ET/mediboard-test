Echeance = {
  modal: null,
  refresh: function() {
    var url = new Url('facturation', 'vw_echeancier');
    url.requestUpdate('echeances');
  },
  loadList: function(facture_id, facture_class) {
    var url = new Url('facturation', 'vw_echeancier');
    url.addParam('facture_id'   , facture_id);
    url.addParam('facture_class', facture_class);
    url.requestUpdate("echeances");
  },

  create: function(facture_id, facture_class) {
    var url = new Url('facturation', 'ajax_edit_echeance');
    url.addParam('facture_id'   , facture_id);
    url.addParam('facture_class', facture_class);
    url.requestModal(500);
    Echeance.modal = url.modalObject;
  },
  edit: function(echeance_id) {
    var url = new Url('facturation', 'ajax_edit_echeance');
    url.addParam('echeance_id', echeance_id);
    url.requestModal(500);
    Echeance.modal = url.modalObject;
  },

  submit: function(form) {
    return onSubmitFormAjax(form, {
        onComplete : function() {
          Echeance.modal.close();
          Echeance.refresh();
        }}
    );
  }
};
