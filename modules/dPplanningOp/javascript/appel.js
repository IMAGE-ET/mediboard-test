Appel = {
  modal: null,
  modal_appel: null,
  url: null,
  edit: function(appel_id, type, sejour_id) {
    var url = new Url('planningOp', 'ajax_edit_appel');
    url.addParam('appel_id' , appel_id);
    url.addParam('type'     , type);
    url.addParam('sejour_id', sejour_id);
    url.requestModal(null, null, { onClose: function() {
      if (type == 'admission' && !appel_id) {
        reloadAdmissionLine(sejour_id);
      }
      else if (!appel_id) {
        reloadSortieLine(sejour_id);
      }
    } } );
    if (appel_id) {
      Appel.modal_appel = url.modalObject;
    }
    else {
      Appel.modal = url.modalObject;
      Appel.url = url;
    }
  },

  changeEtat: function(form, new_etat) {
    $V(form.etat, new_etat);
    return Appel.submit(form);
  },

  onDeletion: function(form) {
    return confirmDeletion(form, { typeName: 'l\'appel'},
      { onComplete: function(){
        Appel.modal_appel.close();
        Appel.url.refreshModal();
      }}
    );
  },
  submit: function(form) {
    return onSubmitFormAjax(form, {
      onComplete : function() {
        if ($V(form.appel_id)) {
          Appel.modal_appel.close();
          Appel.url.refreshModal();
        }
        else {
          Appel.modal.close();
        }
      }
    });
  }
};