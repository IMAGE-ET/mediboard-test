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
    url.addParam('type_pdf'        , 'relance');
    url.addParam('relance_id'      , relance_id);
    url.popup(1000, 600);
  },
  modify: function(relance_id) {
    var url = new Url('facturation', 'ajax_edit_relance');
    url.addParam('relance_id', relance_id);
    url.requestModal(500, 300);
  },
  printRelance: function(facture_class, facture_id, type_pdf, relance_id) {
    var url = new Url('facturation', 'ajax_edit_bvr');
    url.addParam('facture_class', facture_class);
    url.addParam('facture_id'   , facture_id);
    url.addParam('relance_id'   , relance_id);
    url.addParam('type_pdf'     , type_pdf);
    url.addParam('suppressHeaders', '1');
    url.popup(1000, 600);
  },
  checkBills: function(facture_class) {
    var form = getForm("printFrm");
    var url = new Url('tarmed', 'ajax_send_file_http');
    url.addParam('prat_id'    , form.chir.value);
    url.addParam('date_min'   , $V(form._date_min));
    url.addParam('date_max'   , $V(form._date_max));
    url.addParam('facture_class'  , facture_class);
    url.addParam('relance'    , 1);
    url.addParam('check'      ,true);
    url.requestUpdate('check_bill_relance', { onComplete:function() {
      var suppressHeaders = 0;
      if ($('check_bill_relance_ok')) {
        suppressHeaders = 1;
      }
      Relance.downloadBills(facture_class, suppressHeaders,form);
    }}
    );
  },
  downloadBills: function(facture_class, suppressHeaders, form) {
    var url = new Url('tarmed', 'ajax_send_file_http');
    url.addParam('prat_id'  , form.chir.value);
    url.addParam('date_min' , $V(form._date_min));
    url.addParam('date_max' , $V(form._date_max));
    url.addParam('facture_class'  , facture_class);
    url.addParam('relance'  , 1);
    url.addParam('check'    , suppressHeaders ? 0 : 1 );
    url.addParam('suppressHeaders', suppressHeaders);
    url.popup(1000, 600);
  },

  checkRelance: function(form) {
    var url = new Url('tarmed', 'ajax_send_file_http');
    url.addParam('prat_id'      , $V(form.prat_id));
    url.addParam('relance_id'   , $V(form.relance_id));
    url.addParam('facture_class', $V(form.object_class));
    url.addParam('relance'      , 1);
    url.addParam('check'        ,true);
    url.requestUpdate('check_bill_relance', { onComplete:function() {
      var suppressHeaders = 0;
      if ($('check_bill_relance_ok')) {
        suppressHeaders = 1;
      }
      Relance.downloadRelanceXML(suppressHeaders,form);
    }}
    );
  },
  downloadRelanceXML: function(suppressHeaders, form) {
    var url = new Url('tarmed', 'ajax_send_file_http');
    url.addParam('prat_id'      , $V(form.prat_id));
    url.addParam('relance_id'   , $V(form.relance_id));
    url.addParam('facture_class', $V(form.object_class));
    url.addParam('relance'      , 1);
    url.addParam('check'        , suppressHeaders ? 0 : 1 );
    url.addParam('suppressHeaders', suppressHeaders);
    url.popup(1000, 600);
    reloadFactureModal($V(form.object_id), $V(form.object_class));
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