window.Facture = {
  url: null,
  modal: null,
  reload: function(patient_id, consult_id ,not_load_banque, facture_id, object_class) {
    var url = new Url('facturation', 'ajax_view_facture');
    url.addParam('patient_id'      , patient_id);
    url.addParam('consult_id'      , consult_id);
    url.addParam('object_class'    , object_class);
    url.addParam('not_load_banque' , not_load_banque);
    url.addParam('facture_id'      , facture_id);
    if (!$('load_facture')) {
      Facture.url.refreshModal();
    }
    else {
      url.requestUpdate('load_facture');
    }
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
    if (!$('load_facture')) {
      Facture.url.refreshModal();
    }
  },
  cut: function(form) {
    onSubmitFormAjax(form, {
      onComplete : function() {
        var url = new Url('facturation', 'ajax_view_facture');
        url.addElement(form.facture_id);
        url.addParam('object_class'  , form.facture_class.value);
        url.requestUpdate("load_facture");
        Facture.modal.close();
      }
    });
  },
  edit: function(facture_id, facture_class, show_button) {
    show_button = show_button || 1;
    var url = new Url('facturation', 'ajax_view_facture');
    url.addParam('facture_id'    , facture_id);
    url.addParam("object_class", facture_class);
    url.addParam("show_button", show_button);
    url.requestModal(1000, 550);
    Facture.url = url;
  }
};

editRepartition = function(facture_id, facture_class){
  var url = new Url("facturation", "ajax_edit_repartition");
  url.addParam("facture_id"   , facture_id);
  url.addParam("facture_class", facture_class);
  url.requestModal();
}

editDateFacture = function(form){
  form.cloture.value = form.ouverture.value;
  return onSubmitFormAjax(form);
}

printFactureFR = function(facture_id, facture_class){
  var url = new Url("facturation", "print_facture");
  url.addParam("facture_id"   , facture_id);
  url.addParam("facture_class", facture_class);
  url.addParam('suppressHeaders', '1');
  url.pop();
}

reloadFactureModal = function(facture_id, facture_class){
  var url = new Url('facturation', 'ajax_view_facture');
  url.addParam('object_class'  , facture_class);
  url.addParam('facture_id'    , facture_id);
  url.requestUpdate('reload-'+facture_class+'-'+facture_id);
}