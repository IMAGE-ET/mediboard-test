Naissance = {
  edit : function(naissance_id, operation_id) {
    var url = new Url("maternite", "ajax_edit_naissance");
    url.addParam("naissance_id", naissance_id);
    url.addParam("operation_id", operation_id);
    url.requestModal(400);
  },
  reloadNaissances : function(operation_id) {
    var url = new Url('maternite', 'ajax_vw_naissances');
    url.addParam('operation_id', operation_id);
    url.requestUpdate('naissance_area');
  }
}
