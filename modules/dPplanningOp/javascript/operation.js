// $Id: plage_selector.js 6447 2009-06-22 08:11:48Z phenxdesign $

Operation = {
  edit: function(operation_id, plage_id, callback) {
    new Url("planningOp", plage_id ? "vw_edit_planning" : "vw_edit_urgence", "tab").
      addParam("operation_id", operation_id).
      redirectOpener();
  },

  print: function(operation_id) {
    new Url("planningOp", "view_planning").
      addParam("operation_id", operation_id).
      popup(700, 550, "Admission");
  },

  modalCallback: function() {
    document.location.reload();
  },

  editModal: function(operation_id, plage_id, callback) {
    callback = callback || this.modalCallback;
    var url = new Url("planningOp", plage_id ? "vw_edit_planning" : "vw_edit_urgence", "action");
    url.addParam("operation_id", operation_id);
    url.addParam("dialog", 1);
    url.modal({
      width     : "95%",
      height    : "95%",
      afterClose: callback
    });
  },

  dossierBloc: function(operation_id, callback) {
    callback = callback || this.modalCallback;
    var url = new Url("salleOp", "ajax_vw_operation");
    url.addParam("operation_id", operation_id);
    url.modal({
      width     : "95%",
      height    : "95%",
      afterClose: callback
    });
  },

  showDossierSoins: function(sejour_id, default_tab, callback) {
    callback = callback || this.modalCallback;
    var url = new Url("soins", "ajax_vw_dossier_sejour");
    url.addParam("sejour_id", sejour_id);
    url.addParam("modal", "1");
    url.addParam("default_tab", default_tab);
    url.requestModal("95%", "90%", {
      onClose: callback
    });
    modalWindow = url.modalObject;
  },

  useModal: function() {
    this.edit = this.editModal;
  },

  switchOperationsFromSalles : function(salle1, salle2, date, callback) {
    var url = new Url("planningOp", "controllers/do_switch_operations_from_2_salles");
    url.addParam("salle_1", salle1);
    url.addParam("salle_2", salle2);
    url.addParam("date", date);
    if (confirm("Etes vous sur de vouloir échanger les interventions de ces deux salles")) {
      url.requestUpdate("systemMsg", {onComplete: callback});
    }
  }
};

Libelle = {
  modal: null,
  edit: function(libelle_id) {
    var url = new Url('dPplanningOp', 'ajax_edit_libelle');
    url.addParam('libelle_id', libelle_id);
    url.requestModal(500);
    url.modalObject.observe('afterClose', function() {
      getForm('search_libelle').onsubmit();
    })
  }
};


LiaisonOp = {
  modal: null,
  url: null,
  edit: function(operation_id) {
    var url = new Url('dPplanningOp', 'vw_libelles_op');
    url.addParam('operation_id', operation_id);
    url.requestModal(500, 300);
    this.url = url;
  },
  onDeletion: function(form) {
    if (!form.libelleop_id.value) {
      form.libelleop_id.value = 1;
    }
    return confirmDeletion(form, { typeName: 'l\'affectation de libellé'},
      { onComplete: function(){
        LiaisonOp.url.refreshModal();
      }}
    );
  },
  submit: function(form) {
    return onSubmitFormAjax(form, {
      onComplete : function() {
        LiaisonOp.url.refreshModal();
      }
    });
  }
};
