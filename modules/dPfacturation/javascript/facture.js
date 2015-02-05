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
  },

  printFacture: function(facture_id, facture_class, type_pdf) {
    var url = new Url('facturation', 'ajax_edit_bvr');
    url.addParam('facture_class', facture_class);
    url.addParam('facture_id'   , facture_id);
    url.addParam('type_pdf'     , type_pdf);
    url.addParam('suppressHeaders', '1');

    if (type_pdf == "impression") {
      var urlbis = new Url('facturation', 'ajax_edit_definitive');
      urlbis.addParam('facture_class', facture_class);
      urlbis.addParam('facture_id'   , facture_id);
      urlbis.requestModal(300, 150);
      urlbis.modalObject.observe('afterClose', function(){
        url.requestUpdate(SystemMessage.id);
      });
    }
    else {
      url.popup(1000, 600);
    }
  },

  printGestion: function(type_pdf, facture_class, oForm) {
    if(!oForm.chir.value) {
      alert('Vous devez choisir un praticien');
      return false;
    }
    var url = new Url('facturation', 'ajax_edit_bvr');
    url.addParam('facture_class'   , facture_class);
    url.addParam('type_pdf'        , type_pdf);
    url.addElement(oForm._date_min);
    url.addElement(oForm._date_max);
    url.addParam('prat_id'         , oForm.chir.value);
    url.addParam('suppressHeaders' , '1');
    url.popup(1000, 600);
  },

  viewInterv: function(operation_id, plageop_id) {
    if (plageop_id) {
      var url = new Url("planningOp", "vw_edit_planning", "tab");
    }
    else {
      var url = new Url("planningOp", "vw_edit_urgence", "tab");
    }
    url.addParam("operation_id", operation_id);
    url.redirect();
  },

  dossierBloc: function(operation_id, patient_id, facture_id, facture_class) {
    var url = new Url("salleOp", "ajax_vw_operation");
    url.addParam("op", operation_id);
    url.requestModal('90%', '90%');
    url.modalObject.observe("afterClose", function(){
      Facture.reload(patient_id, null, 0, facture_id, facture_class);
    });
  }

};

editRepartition = function(facture_id, facture_class){
  var url = new Url("facturation", "ajax_edit_repartition");
  url.addParam("facture_id"   , facture_id);
  url.addParam("facture_class", facture_class);
  url.requestModal();
};

editDateFacture = function(form){
  form.cloture.value = form.ouverture.value;
  return onSubmitFormAjax(form);
};

printFactureFR = function(facture_id, facture_class){
  var url = new Url("facturation", "print_facture");
  url.addParam("facture_id"   , facture_id);
  url.addParam("facture_class", facture_class);
  url.addParam('suppressHeaders', '1');
  url.pop();
};

reloadFactureModal = function(facture_id, facture_class){
  var url = new Url('facturation', 'ajax_view_facture');
  url.addParam('object_class'  , facture_class);
  url.addParam('facture_id'    , facture_id);
  url.requestUpdate('reload-'+facture_class+'-'+facture_id);
};
