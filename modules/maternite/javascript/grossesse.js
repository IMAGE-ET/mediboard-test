Grossesse = {
  formTo: null,
  formFrom: null,
  duree_sejour: null,
  submit: false,
  viewGrossesses: function(parturiente_id, object_guid, grossesse_id_form, form) {
    var url = new Url("maternite", "ajax_bind_grossesse");
    if (parturiente_id == '') {
      url.addParam("parturiente_id", $V(form.patient_id));
    }
    else {
      url.addParam("parturiente_id", parturiente_id);
    }
    url.addParam("object_guid", object_guid);
    if (grossesse_id_form) {
      url.addParam("grossesse_id_form", grossesse_id_form);
    }
    url.requestModal(700, 225);
  },
  toggleGrossesse: function(sexe, form) {
    form.select(".button_grossesse")[0].disabled = sexe == "f" ? "" : "disabled";
  },
  editGrossesse: function(grossesse_id, parturiente_id) {
    var url = new Url("maternite", "ajax_edit_grossesse");
    url.addParam("grossesse_id", grossesse_id);
    url.addParam("parturiente_id", parturiente_id);
    url.requestUpdate("edit_grossesse");
  },
  refreshList: function(patient_id, object_guid, grossesse_id_form) {
    var url = new Url("maternite", "ajax_list_grossesses");
    if (patient_id) {
      url.addParam("patient_id", patient_id);
    }
    if (object_guid) {
      url.addParam("object_guid", object_guid);
    }
    if (grossesse_id_form) {
      url.addParam("grossesse_id_form", grossesse_id_form);
    }
    url.requestUpdate("list_grossesses");
  },
  afterEditGrossesse: function(grossesse_id) {
    Grossesse.editGrossesse(grossesse_id);
    Grossesse.refreshList();
  },
  bindGrossesse: function() {
    $V(this.formTo.grossesse_id, $V(this.formFrom.unique_grossesse_id));
    $("view_grossesse").update(this.formFrom.down("input[name='unique_grossesse_id']:checked").get("view_grossesse"));
    this.formTo.select(".button_grossesse")[0].show();
    if (this.formTo.sejour_id) {
      $V(this.formTo.type_pec, 'O');
      $V(this.formTo._duree_prevue, this.duree_sejour);
    }
    if (this.submit) {
      return onSubmitFormAjax(this.formTo);
    }
  }
}

