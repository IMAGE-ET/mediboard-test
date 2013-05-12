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
  edit: function(motif_id) {
    var url = new Url('urgences', 'ajax_edit_chapitre_motif');
    url.addParam('motif_id', motif_id);
    url.requestModal(400);
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
  }
};
