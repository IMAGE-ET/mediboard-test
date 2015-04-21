Commande = {
  edit: function(commande_id) {
    var url = new Url('planningOp', 'ajax_edit_commande_mat');
    url.addParam('commande_id', commande_id);
    url.requestModal(500, 300, {
    onClose : function() {
      refreshLists();
    }});
  },
  changeEtat: function(form, etat_name) {
    $V(form.etat, etat_name);
    return onSubmitFormAjax(form, {onComplete: refreshLists});
  }
};