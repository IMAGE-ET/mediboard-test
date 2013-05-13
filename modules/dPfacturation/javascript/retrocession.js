Retrocession = {
  modal: null,
  edit: function(retrocession_id) {
    var url = new Url('facturation', 'ajax_edit_retrocession');
    url.addParam('retrocession_id', retrocession_id);
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
