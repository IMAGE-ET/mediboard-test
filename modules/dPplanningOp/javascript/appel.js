Appel = {
  modal: null,
  url: null,
  edit: function(appel_id, type, sejour_id) {
    var url = new Url('planningOp', 'ajax_edit_appel');
    url.addParam('appel_id' , appel_id);
    url.addParam('type'     , type);
    url.addParam('sejour_id', sejour_id);
    url.requestModal(500, null, { onClose: function() {
      if (type == 'admission') {
        reloadAdmissionLine(sejour_id);
      }
      else {
        reloadSortieLine(sejour_id);
      }
    } } );
    Appel.modal = url.modalObject;
  },

  changeEtat: function(form, new_etat) {
    $V(form.etat, new_etat);
    return Appel.submit(form);
  },

  submit: function(form) {
    return onSubmitFormAjax(form, {
      onComplete : function() {
        Appel.modal.close();
      }
    });
  }
};