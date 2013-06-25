Relance = {
  modal: null,
  create: function(form) {
    return onSubmitFormAjax(form, {
      onComplete : function() {
        var url = new Url('facturation', 'ajax_view_facture');
        url.addParam('object_id'    , form.object_id.value);
        url.addParam('object_class' , form.object_class.value);
        url.requestUpdate("load_facture");
      }
    });
  },
  pdf: function(relance_id) {
    var url = new Url('facturation', 'ajax_edit_bvr');
    url.addParam('suppressHeaders' , '1');
    url.addParam('relance_id'      , relance_id);
    url.popup(1000, 600);
  },
  modify: function(relance_id) {
    var url = new Url('facturation', 'ajax_edit_relance');
    url.addParam('relance_id', relance_id);
    url.requestModal(500, 300);
  }
};

ListeFacture = {
  load: function(facture_class, type_relance) {
    var form = document.printFrm;
    var url = new Url('facturation', 'ajax_vw_list_facture');
    url.addElement(form._date_min);
    url.addElement(form._date_max);
    url.addElement(form.chir);
    url.addParam('type_relance'	, type_relance);
    url.addParam("facture_class", facture_class);
    url.requestModal(1200, 550);
  },
  view: function(facture_class, type_relance, etat_relance) {
    var form = document.printFrm;
    var url = new Url('facturation', 'ajax_vw_relances');
    url.addElement(form._date_min);
    url.addElement(form._date_max);
    url.addElement(form.chir);
    url.addParam('relance'		, '1');
    url.addParam('type_relance'	, type_relance);
    url.addParam("facture_class", facture_class);
    url.addParam("etat_relance", etat_relance);
    url.requestModal(1200, 550);
  }
};