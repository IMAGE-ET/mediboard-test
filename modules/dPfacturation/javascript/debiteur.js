Debiteur = {
  modal: null,
  edit: function(debiteur_id) {
    var url = new Url('facturation', 'ajax_edit_debiteur');
    url.addParam('debiteur_id', debiteur_id);
    url.requestModal(500);
  },
  submit: function(form) {
    return onSubmitFormAjax(this, {
        onComplete : function() {
          this.modal.close();
        }}
    );
  }
};
