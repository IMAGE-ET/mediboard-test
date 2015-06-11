/**
 * $Id$
 *
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

Grossesse = {
  formTo: null,
  formFrom: null,
  duree_sejour: null,
  submit: false,
  parturiente_id: null,
  large_icon: 0,
  modify_grossesse: 1,
  show_checkbox: 1,

  viewGrossesses: function(parturiente_id, object_guid, form, show_checkbox) {
    Grossesse.show_checkbox = Object.isUndefined(show_checkbox) ? 1 : show_checkbox;
    var url = new Url("maternite", "ajax_bind_grossesse");
    if (parturiente_id == '') {
      url.addParam("parturiente_id", $V(form.patient_id));
    }
    else {
      url.addParam("parturiente_id", parturiente_id);
    }
    url.addNotNullParam("object_guid", object_guid);
    url.requestModal(900, 400, {onClose: function() {
      if (!Grossesse.modify_grossesse) {
        Grossesse.updateGrossesseArea();
      }
      Grossesse.updateEtatActuel();
    }});
  },
  toggleGrossesse: function(sexe, form) {
    form.select(".button_grossesse")[0].disabled = sexe == "f" ? "" : "disabled";
  },
  editGrossesse: function(grossesse_id, parturiente_id) {
    var url = new Url("maternite", "ajax_edit_grossesse");
    url.addParam("grossesse_id", grossesse_id);
    url.addNotNullParam("parturiente_id", parturiente_id);
    url.requestUpdate("edit_grossesse");
  },
  refreshList: function(parturiente_id, object_guid) {
    var url = new Url("maternite", "ajax_list_grossesses");
    url.addNotNullParam("parturiente_id", parturiente_id);
    url.addNotNullParam("object_guid", object_guid);
    url.addParam("show_checkbox", Grossesse.show_checkbox);
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
        html += "class='opacity-40' ";
      }
      if ($V(this.formTo._large_icon) == 1) {
        html += "style='width: 30px; background-color: rgb(255, 215, 247);'";
      }
      html += "/>";
      $("view_grossesse").update(html);
      this.formTo.select(".button_grossesse")[0].show();
      if (this.formTo.sejour_id) {
        $V(this.formTo.type_pec, 'O');
        $V(this.formTo._duree_prevue, this.duree_sejour);
      }

      // Pour une nouvelle DHE, on applique la date de terme prévu sur l'entrée prévue
      if (this.formTo._date_entree_prevue && !$V(this.formTo.sejour_id)) {
        var date = this.formFrom.unique_grossesse_id.get("date");
        $V(this.formTo._date_entree_prevue, date);
        $V(this.formTo._date_entree_prevue_da, new Date(date).format("dd/MM/yyyy"));
      }
    }
    else {
      $("view_grossesse").update("<div class='empty' style='display: inline'>"+$T("CGrossesse.none_linked")+"</div>");
    }
    if (this.submit == "1") {
      return onSubmitFormAjax(this.formTo);
    }
  },
  emptyGrossesses: function() {
    this.formFrom.select("input[name='unique_grossesse_id']").each(function(input) {
      input.checked = "";
    });
    this.bindGrossesse();
  },

  updateGrossesseArea: function() {
    if (!Grossesse.parturiente_id) {
      return;
    }

    var url = new Url("maternite", "ajax_update_grossesse_area");
    url.addParam("parturiente_id", Grossesse.parturiente_id);
    url.addParam("submit", Grossesse.submit);
    url.addParam("large_icon", Grossesse.large_icon);
    url.addParam("modify_grossesse", Grossesse.modify_grossesse);
    url.requestUpdate("view_grossesse");
  },

  updateEtatActuel: function() {
    if (!Grossesse.parturiente_id || !$("etat_actuel_grossesse")) {
      return;
    }

    var url = new Url("maternite", "ajax_update_fieldset_etat_actuel");
    url.addParam("patient_id", Grossesse.parturiente_id);
    url.requestUpdate($("etat_actuel_grossesse").up());
  }
}

