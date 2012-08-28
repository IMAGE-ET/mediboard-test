Grossesse = {
  formTo: null,
  formFrom: null,
  duree_sejour: null,
  submit: false,
  viewGrossesses: function(parturiente_id, object_guid, form) {
    var url = new Url("maternite", "ajax_bind_grossesse");
    if (parturiente_id == '') {
      url.addParam("parturiente_id", $V(form.patient_id));
    }
    else {
      url.addParam("parturiente_id", parturiente_id);
    }
    url.addParam("object_guid", object_guid);
    url.requestModal(700, 280);
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
  refreshList: function(patient_id, object_guid) {
    var url = new Url("maternite", "ajax_list_grossesses");
    if (patient_id) {
      url.addParam("patient_id", patient_id);
    }
    if (object_guid) {
      url.addParam("object_guid", object_guid);
    }
    url.requestUpdate("list_grossesses");
  },
  afterEditGrossesse: function(grossesse_id) {
    Grossesse.editGrossesse(grossesse_id);
    Grossesse.refreshList();
  },
  bindGrossesse: function() {
    var grossesse_id = $V(this.formFrom.unique_grossesse_id);
    $V(this.formTo.grossesse_id, grossesse_id);
    if (grossesse_id) {
      var input = this.formFrom.down("input[name='unique_grossesse_id']:checked");
      var html = "<img src='style/mediboard/images/icons/grossesse.png' ";
      html += "onmouseover=\"ObjectTooltip.createEx(this, 'CGrossesse-"+grossesse_id+"')\" ";
      if (input.get("active") == 0) {
        html += "class='opacity-40'";
      }
      html += "/>";
      $("view_grossesse").update(html);
      this.formTo.select(".button_grossesse")[0].show();
      if (this.formTo.sejour_id) {
        $V(this.formTo.type_pec, 'O');
        $V(this.formTo._duree_prevue, this.duree_sejour);
      }
    }
    else {
      $("view_grossesse").update("<div class='empty' style='display: inline'>"+$T("CGrossesse.none_linked")+"</div>");
    }
    if (this.submit) {
      return onSubmitFormAjax(this.formTo);
    }
  },
  emptyGrossesses: function() {
    this.formFrom.select("input[name='unique_grossesse_id']").each(function(input) {
      input.checked = "";
    });
    this.bindGrossesse();
  }
}

