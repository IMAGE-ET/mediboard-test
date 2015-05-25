Chapitre = {
  modal: null,
  edit: function(chapitre_id) {
    var url = new Url('urgences', 'ajax_edit_chapitre_motif');
    url.addParam('chapitre_id', chapitre_id);
    url.requestModal(400);
    this.modal = url.modalObject;
  },

  onSubmit: function(form) {
    return onSubmitFormAjax(form, { 
      onComplete: function() {
        Chapitre.refreshList();
        Chapitre.modal.close();
      }
    })
  },

  confirmDeletion: function(form) {
    var options = {
      typeName:'chapitre', 
      objName: $V(form.nom),
      ajax: 1
    };

    var ajax = {
      onComplete: function() {
        Chapitre.refreshList();
        Chapitre.modal.close();
      }
    };

    confirmDeletion(form, options, ajax);
  },

  refreshList: function() {
    var url = new Url('urgences', 'vw_motifs');
    url.addParam('liste', 'chapitre');
    url.requestUpdate('chapitres');
  }
};

Motif= {
  modal: null,
  edit: function(motif_id, readonly) {
    var url = new Url('urgences', 'ajax_edit_chapitre_motif');
    url.addParam('motif_id', motif_id);
    if (!Object.isUndefined(readonly)) {
      url.addParam('readonly', readonly);
      url.requestModal();
    }
    else {
      url.requestModal(800);
    }
    this.modal = url.modalObject;
  },

  onSubmit: function(form) {
    return onSubmitFormAjax(form, { 
      onComplete: function() {
      Motif.refreshList();
      Motif.modal.close();
    }
    })
  },

  confirmDeletion: function(form) {
    var options = {
      typeName:'motif',
      objName: $V(form.nom),
      ajax: 1
    };
    var ajax = {
      onComplete: function() {
        Motif.refreshList();
        Motif.modal.close();
      }
    };
    confirmDeletion(form, options, ajax);
  },

  refreshList: function() {
    var url = new Url('urgences', 'vw_motifs');
    url.addParam('liste', 'motif');
    url.requestUpdate('motifs');
  },

  searchMotif: function() {
    var form = getForm("searchMotif");
    var url = new Url("urgences", "vw_search_motif");
    url.addParam('reload'       , true);
    url.addFormData(form);
    url.requestUpdate('reload_search_motif');
    return false;
  },

  refreshComplement: function() {
    var form = getForm("editRPUtri");
    var url = new Url("urgences", "ajax_form_complement");
    url.addParam('rpu_id'       , form.rpu_id.value);
    url.requestUpdate('form-edit-complement');
  },

  selectDiag: function(code_diag) {
    var form = getForm("choiceMotifRPU");
    $V(form.code_diag, code_diag);
    return onSubmitFormAjax(form, {
      onComplete: function() {
        Control.Modal.close();
        Motif.refreshComplement();
        Motif.loadQuestionsRpu();
      }
    });
  },

  deleteDiag: function(form, see_reload) {
    return onSubmitFormAjax(form, {
      onComplete: function() {
        if (see_reload && !form.echelle_tri_id.value) {
          Motif.reloadComplementEchelle(form);
        }
        Motif.refreshComplement();
        Motif.loadQuestionsRpu();
      }
    });
  },

  loadQuestionsRpu: function() {
    var form = getForm("editRPUtri");
    var url = new Url("urgences", "ajax_form_questions_motif");
    url.addParam('rpu_id'       , form.rpu_id.value);
    url.requestUpdate('form-question_motif');
  },

  submitReponse: function(form) {
    return onSubmitFormAjax(form, {
      onComplete: function() {
        Motif.refreshComplement();
      }
    });
  },

  seeTraitements: function(form) {
    return onSubmitFormAjax(form, {
      onComplete: function() {
        if (!form.echelle_tri_id.value) {
          Motif.reloadComplementEchelle(form);
        }
        form.antidiabetique.hidden = 'hidden';
        form.anticoagulant.hidden = 'hidden';
        if (form.antidiabet_use.value == 'oui') {
          form.antidiabetique.hidden = '';
        }
        if (form.anticoagul_use.value == 'oui') {
          form.anticoagulant.hidden = '';
        }
      }
    });
  },

  reloadComplementEchelle: function(form) {
    var url = new Url("urgences", "ajax_echelle_tri");
    url.addParam('rpu_id'       , form.rpu_id.value);
    url.requestUpdate('form-echelle_tri');
  },

  setReactivite: function(cote, new_value) {
    form = getForm('formEchelleTri');
    var value_change = new_value;
    if (cote == 'reactivite_gauche') {
      if ($V(form.reactivite_gauche) == new_value) {
        value_change = '';
      }
      $V(form.reactivite_gauche, value_change);
    }
    else {
      if ($V(form.reactivite_droite) == value_change) {
        value_change = '';
      }
      $V(form.reactivite_droite, value_change);
    }
    return onSubmitFormAjax(form, {
      onComplete: function() {
        $(cote+'_reactif').setStyle({"font-weight": "normal"});
        $(cote+'_non_reactif').setStyle({"font-weight": "normal"});
        if (new_value == value_change) {
          $(cote+'_'+new_value).setStyle({"font-weight": "bold"});
        }
      }
    });
  },

  setPupilles: function(cote, add) {
    form = getForm('formEchelleTri');
    var niveau = form.pupille_droite.value;
    if (cote == 'pupille_gauche') {
      niveau = form.pupille_gauche.value;
    }

    if (add == 0) {
      niveau = niveau-1;
      if (niveau == -1) niveau = 3;
    }
    var new_niveau = 0;
    switch (parseInt(niveau)) {
      case 0:
        new_niveau = 1;
        $(cote+'_circle').style.border = "2px solid black";
        $(cote+'_circle').style.margin = "8px";
        break;
      case 1:
        new_niveau = 2;
        $(cote+'_circle').style.border = "5px solid black";
        $(cote+'_circle').style.margin = "5px";
        break;
      case 2:
        new_niveau = 3;
        $(cote+'_circle').style.border = "8px solid black";
        $(cote+'_circle').style.margin = "2px";
        break;
      default :
        new_niveau = 0;
        $(cote+'_circle').style.border = "0px solid black";
        $(cote+'_circle').style.margin = "1px";
        break;
    }
    if (add) {
      if (cote == 'pupille_gauche') {
        $V(form.pupille_gauche, new_niveau);
      }
      else {
        $V(form.pupille_droite, new_niveau);
      }
      Motif.deleteDiag(form, 1);
    }
  },

  saveGlasgow: function(form, context_guid) {
    return onSubmitFormAjax(form, {
      onComplete: function() {
        Motif.refreshComplement();
        refreshConstantesMedicalesTri(context_guid);
        refreshConstantesMedicales(context_guid);
      }
    });
  }

};

Question = {
  modal: null,
  edit: function(question_id, motif_id) {
    var url = new Url('urgences', 'ajax_edit_question_motif');
    url.addParam('question_id', question_id);
    if (!Object.isUndefined(motif_id)) {
      url.addParam('motif_id', motif_id);
    }
    url.requestModal(800);
    this.modal = url.modalObject;
  },

  onSubmit: function(form) {
    return onSubmitFormAjax(form, {
      onComplete: function() {
        Motif.modal.close();
        Motif.edit($V(form.motif_id));
        Question.modal.close();
      }
    });
  },

  remove: function(question_id, nom){
    var form = getForm('question-delete');
    form.question_id.value = question_id;
    Question.confirmDeletion(form, nom);
  },

  confirmDeletion: function(form, nom) {
    var options = {
      typeName:'question',
      objName: nom,
      ajax: 1
    };
    var ajax = {
      onComplete: function() {
        Motif.modal.close();
        Motif.edit($V(form.motif_id));
        Question.modal.close();
      }
    };
    confirmDeletion(form, options, ajax);
  }

};
