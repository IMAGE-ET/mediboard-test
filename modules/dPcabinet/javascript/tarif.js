Tarif = {
  chir_id: null,
  function_id: null,
  group_id: null,
  modal: null,
  url: null,
  newCodable: function(codable_id, codable_class, prat_id) {
    var url = new Url('cabinet', 'ajax_edit_tarif');
    url.addParam('codable_id',    codable_id);
    url.addParam('codable_class', codable_class);
    url.addParam('prat_id',       prat_id);
    url.requestModal();
    Tarif.modal = url.modalObject;
  },

  edit: function(tarif_id, prat_id) {
    var url = new Url('cabinet', 'ajax_edit_tarif');
    url.addParam('tarif_id', tarif_id);
    url.addParam('prat_id', prat_id);
    url.requestModal();
    this.url = url;
  },

  submit: function(form) {
    return onSubmitFormAjax(form, {
      onComplete : function() {
        if (Tarif.modal) {
          Tarif.modal.close();
        }
        else {
          Control.Modal.close();
        }
      }
    });
  },

  recalcul: function(form) {
    return onSubmitFormAjax(form, {
      onComplete : function() {
        Tarif.url.refreshModal();
      }
    });
  },
  
  updateTotal: function () {
    var form = getForm("editFrm");
    if (!form.secteur1 || !form.secteur1) {
      return;
    }
    
    var secteur1 = form.secteur1.value;
    var secteur2 = form.secteur2.value; 
    var secteur3 = form.secteur3.value;
    var taux_tva = form.taux_tva.value;

    if (secteur1 == ""){
      secteur1 = 0;
    }
    if (secteur2 == ""){
      secteur2 = 0;
    }
    if (secteur3 == ""){
      secteur3 = 0;
    }

    $V(form._du_tva, (secteur3*(taux_tva)/100).toFixed(2));
    form._somme.value = parseFloat(secteur1) + parseFloat(secteur2) + parseFloat(secteur3) + parseFloat(form._du_tva.value);
    form._somme.value = Math.round(form._somme.value*100)/100;
  },
  
  updateSecteur2: function() {
    var form = getForm("editFrm");
    var secteur1 = form.secteur1.value;
    var secteur3 = form.secteur3.value;
    var du_tva = form._du_tva.value;
    var somme    = form._somme.value;
    
    if (somme == "") {
      somme = 0;
    }
    if (secteur1 == "") {
      secteur1 = 0;
    }
    if (secteur3 == "") {
      secteur3 = 0;
    }
    if (du_tva == "") {
      du_tva = 0;
    }
    
    form.secteur2.value = parseFloat(somme) - parseFloat(secteur1) - parseFloat(secteur3) - parseFloat(du_tva);
    form.secteur2.value = Math.round(form.secteur2.value*100)/100;
  },
  
  updateOwner: function() {
    var form = getForm("editFrm");
    var type = $V(form._type);

    if (type == "chir") {
      $V(form.chir_id, this.chir_id);
      $V(form.function_id, "");
      $V(form.group_id, "");
    }

    if (type == "function") {
      $V(form.chir_id, "");
      $V(form.function_id, this.function_id);
      $V(form.group_id, "");
    }
    if (type == "group") {
      $V(form.chir_id, "");
      $V(form.function_id, "");
      $V(form.group_id, this.group_id);
    }
  },
  
  forceRecompute: function() {
    $("force-recompute").show();
    var form = getForm("editFrm");
    form.save.disabled = true;
  }
};

Code = {
  modal: null,
  url: null,
  edit: function (tarif_id) {
    var url = new Url('dPcabinet', 'vw_codes_tarif');
    url.addParam('tarif_id'    , tarif_id);
    url.requestModal(700);
    this.modal = url.modalObject;
    Code.url = url;
  },
  addCode: function (form, code, quantite, type, code_ref) {
    form._add_code.value    = 1;
    form._dell_code.value   = 0;
    form._code.value        = code;
    form._quantite.value    = quantite;
    form._type_code.value   = type;
    form._code_ref.value    = code_ref;
    return onSubmitFormAjax(form, {
      onComplete : function() {
        Code.url.refreshModal();
      }
    });
  },
  dellCode: function (form, code, type) {
    form._add_code.value  = 0;
    form._dell_code.value = 1;
    form._code.value      = code;
    form._type_code.value = type;
    return onSubmitFormAjax(form, {
      onComplete : function() {
        Code.url.refreshModal();
      }
    });
  }
};