Retrocession = {
  modal: null,
  edit: function(retrocession_id) {
    var url = new Url('facturation', 'ajax_edit_retrocession');
    url.addParam('retrocession_id', retrocession_id);
    url.requestModal(500, 500);
    Retrocession.modal = url.modalObject;
  },
  reload: function() {
    var url = new Url('facturation', 'vw_retrocession_regles', 'tab');
    url.redirect();
  },
  submit: function(form) {
    return onSubmitFormAjax(form, {
      onComplete : function() {
        Retrocession.modal.close();
        Retrocession.reload();
      }}
    );
  }
};
