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
