CCirconstance = {
  form: null,
  edit : function(id) {
    new Url("dPurgences", "ajax_edit_circonstance")
      .addParam("id", id)
      .requestModal();
  },

  searchMotifSFMU : function(form) {
    CCirconstance.form = form;
    new Url("dPurgences", "ajax_search_motif_sfmu")
      .requestModal(600, 500);
  },

  displayMotifFromCategorie : function(value) {
    new Url("dPurgences", "ajax_display_motif_sfmu_category")
      .addParam("categorie", value)
      .requestUpdate("motif_sfmu_by_category");
  },

  selectMotifSFMU : function(libelle, id) {
    var form = CCirconstance.form;
    $V(form["motif_sfmu_autocomplete_view"], libelle);
    $V(form["motif_sfmu"], id);
    Control.Modal.close();
  }
};