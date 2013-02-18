AffectationUf  = {
  modal: null,
  
  edit: function(object_guid) {
    var url = new Url('hospi'  , 'ajax_affectation_uf');
    url.addParam('object_guid'  , object_guid);
    url.requestModal(450, 200);
    this.modal = url.modalObject;
  },
  
  affecter: function(curr_affectation_guid, lit_guid, callback) {
    var url = new Url('hospi'        , 'ajax_vw_association_uf');
    url.addParam('curr_affectation_guid'  , curr_affectation_guid);
    url.addParam('lit_guid'  , lit_guid);
    url.addParam('callback' , callback);
    url.requestModal(600, 400);
    this.modal = url.modalObject;
  },
  
  onSubmit: function(form) {
    Control.Modal.close();
    return onSubmitFormAjax(form);  
  },
  onSubmitRefresh: function(form, object_guid, lit_guid, see_validate) {
    onSubmitFormAjax(form);
    var url = new Url('hospi'  , 'ajax_vw_association_uf');
    url.addParam('curr_affectation_guid'  , object_guid);
    url.addParam('lit_guid'  , lit_guid);
    url.addParam('see_validate'  , see_validate);
    url.requestUpdate("affecter_uf");
    return false;
  },
  onDeletion: function(form) {
    return confirmDeletion(form,
      { typeName: 'l\'affectation d\'UF'},
      { onComplete: function(){
          Control.Modal.close();
      }});
  }
};