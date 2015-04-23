{{* $Id: *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=admissions  script=admissions}}
{{mb_script module=compteRendu script=document}}
{{mb_script module=compteRendu script=modele_selector}}
{{mb_script module=files     script=file}}
{{mb_script module=planningOp  script=sejour}}
{{mb_script module=planningOp  script=prestations}}
{{if "web100T"|module_active}}
  {{mb_script module=web100T script=web100T}}
{{/if}}

<script>
  var sejours_enfants_ids;

  function printAmbu(type) {
    var form = getForm("selType");
    var url = new Url("admissions", "print_ambu");
    url.addParam("date", $V(form.date));
    url.addParam("type", type);
    url.popup(800, 600, "Ambu");
  }

  function printPlanning() {
    var form = getForm("selType");
    var url = new Url("admissions", "print_sorties");
    url.addParam("date"      , $V(form.date));
    url.addParam("type"      , $V(form._type_admission));
    url.addParam("service_id", [$V(form.service_id)].flatten().join(","));
    url.addParam("period"    , $V(form.period));
    url.popup(700, 550, "Sorties");
  }

  function printDHE(type, object_id) {
    var url = new Url("planningOp", "view_planning");
    url.addParam(type, object_id);
    url.popup(700, 550, "DHE");
  }

  function changeEtablissementId(form) {
    $V(form._modifier_sortie, '0');
    var type = $V(form.type);
    submitSortie(form, type);
  };

  function submitMultiple(form) {
    return onSubmitFormAjax(form, reloadFullSorties);
  }

  sortie_preparee = function(sejour_id, value) {
    var form = getForm("edit_sejour_sortie_preparee");
    $V(form.sejour_id, sejour_id);
    $V(form.sortie_preparee, ''+value);
    form.onsubmit();
  };

  function reloadFullSorties() {
    var form = getForm("selType");
    var url = new Url("admissions", "httpreq_vw_all_sorties");
    url.addParam("date"      , $V(form.date));
    url.addParam("selSortis" , $V(form.selSortis));
    url.addParam("type"      , $V(form._type_admission));
    url.addParam("service_id", [$V(form.service_id)].flatten().join(","));
    url.addParam("prat_id"   , $V(form.prat_id));
    url.addParam("only_confirmed", $V(form.only_confirmed));
    url.requestUpdate('allSorties');
    reloadSorties();
  }

  function reloadSorties() {
    var form = getForm("selType");
    var url = new Url("admissions", "httpreq_vw_sorties");
    url.addParam("date"      , $V(form.date));
    url.addParam("selSortis" , $V(form.selSortis));
    url.addParam("order_col" , $V(form.order_col));
    url.addParam("order_way" , $V(form.order_way));
    url.addParam("type"      , $V(form._type_admission));
    url.addParam("service_id", [$V(form.service_id)].flatten().join(","));
    url.addParam("prat_id"   , $V(form.prat_id));
    url.addParam("only_confirmed", $V(form.only_confirmed));
    url.addParam("period"    , $V(form.period));
    url.addParam("filterFunction" , $V(form.filterFunction));
    url.requestUpdate("listSorties");
  }

  function reloadSortiesDate(elt, date) {
    var form = getForm("selType");
    $V(form.date, date);
    var old_selected = elt.up("table").down("tr.selected");
    old_selected.select('td').each(function(td) {
      // Supprimer le style appliqué sur le nombre d'admissions
      var style = td.readAttribute("style");
      if (/bold/.match(style)) {
        td.writeAttribute("style", "");
      }
    });
    old_selected.removeClassName("selected");

    // Mettre en gras le nombre d'admissions
    var elt_tr = elt.up("tr");
    elt_tr.addClassName("selected");
    var pos = 1;
    if ($V(form.selSortis) == 'np') {
      pos = 2;
    }
    else if ($V(form.selSortis) == 'n') {
      pos = 3;
    }
    var td = elt_tr.down("td", pos);
    td.writeAttribute("style", "font-weight: bold");

    reloadSorties();
  }

  function reloadSortieLine(sejour_id) {
    var url = new Url("admissions", "ajax_sortie_line");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate("CSejour-"+sejour_id);
  }

  function submitSortie(form) {
    if (!Object.isUndefined(form.elements["_sejours_enfants_ids"]) && $V(form._modifier_sortie) == 1) {
      sejours_enfants_ids = $V(form._sejours_enfants_ids);
      sejours_enfants_ids.split(",").each(function(elt) {
        var formSejour = getForm("editFrmCSejour-"+elt);
        if (!Object.isUndefined(formSejour) && formSejour.down("button.tick")) {
          if (confirm('Voulez-vous effectuer dans un même temps la sortie de l\'enfant ' + formSejour.get("patient_view"))) {
            formSejour.down("button.tick").onclick();
          }
        }
      });

      sejours_enfants_ids = undefined;
      return onSubmitFormAjax(form, reloadSortieLine.curry($V(form.sejour_id)));
    }

    if (!Object.isUndefined(sejours_enfants_ids) && sejours_enfants_ids.indexOf($V(form.sejour_id)) != -1) {
      return onSubmitFormAjax(form);
    }

    return onSubmitFormAjax(form, reloadSortieLine.curry($V(form.sejour_id)));
  }

  function confirmation(form, type) {
     if (!checkForm(form)) {
       return false;
     }
     if (confirm('La date de sortie enregistrée est différente de la date prévue, souhaitez-vous confimer la sortie du patient ?')) {
       submitSortie(form, type);
     }
     else {
       sejours_enfants_ids = undefined;
     }
  }

  function confirmation(date_actuelle, date_demain, sortie_prevue, entree_reelle, form) {
    if (entree_reelle == "") {
      if (!confirm('Attention, ce patient ne possède pas de date d\'entrée réelle, souhaitez-vous confirmer la sortie du patient ?')) {
        sejours_enfants_ids = undefined;
        return false;
      }
    }
    if (date_actuelle > sortie_prevue || date_demain < sortie_prevue) {
      if (!confirm('La date de sortie enregistrée est différente de la date prévue, souhaitez-vous confimer la sortie du patient ?')) {
        sejours_enfants_ids = undefined;
        return false;
      }
    }
    submitSortie(form);
  }

  function updateModeSortie(select) {
    var selected = select.options[select.selectedIndex];
    var form = select.form;
    $V(form.elements.mode_sortie, selected.get("mode"));
  };

  function sortBy(order_col, order_way) {
    var form = getForm("selType");
    $V(form.order_col, order_col);
    $V(form.order_way, order_way);
    reloadSorties();
  }

  function filterAdm(selSortis) {
    var form = getForm("selType");
    $V(form.selSortis, selSortis);
    reloadFullSorties();
  }
  Main.add(function() {
    Admissions.table_id = "listSorties";

    var totalUpdater = new Url("admissions", "httpreq_vw_all_sorties");
    Admissions.totalUpdater = totalUpdater.periodicalUpdate('allSorties', { frequency: 120 });

    var listUpdater = new Url("admissions", "httpreq_vw_sorties");
    Admissions.listUpdater = listUpdater.periodicalUpdate('listSorties', {
      frequency: 120,
      onCreate: function() {
        WaitingMessage.cover($('listSorties'));
        Admissions.rememberSelection();
      }
    });
  });
</script>

<div style="display: none" id="area_prompt_modele">
  {{mb_include module=admissions template=inc_prompt_modele type=sortie}}
</div>

<form name="edit_sejour_sortie_preparee" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: reloadFullSorties})">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="sejour_id" value="" />
  <input type="hidden" name="sortie_preparee" value="" />
</form>

<table class="main">
  <tr>
    <td>
      <a href="#legend" onclick="Admissions.showLegend()" class="button search">Légende</a>
      {{if "astreintes"|module_active}}{{mb_include module=astreintes template=inc_button_astreinte_day date=$date}}{{/if}}
    </td>
    <td style="float: right">
      <form action="?" name="selType" method="get">
        <input type="hidden" name="date" value="{{$date}}" />
        <input type="hidden" name="selSortis" value="{{$selSortis}}" />
        <input type="hidden" name="order_col" value="{{$order_col}}" />
        <input type="hidden" name="order_way" value="{{$order_way}}" />
        <input type="hidden" name="filterFunction" value="{{$filterFunction}}" />
        <select name="period" onchange="reloadSorties();">
          <option value=""      {{if !$period          }}selected{{/if}}>&mdash; Toute la journée</option>
          <option value="matin" {{if $period == "matin"}}selected{{/if}}>Matin</option>
          <option value="soir"  {{if $period == "soir" }}selected{{/if}}>Soir</option>
        </select>
        {{mb_field object=$sejour field="_type_admission" emptyLabel="CSejour.all" onchange="reloadFullSorties();" style="max-width: 15em;"}}

        <button type="button" onclick="Admissions.selectServices('sortie');" class="search">Services</button>

        <select name="prat_id" onchange="reloadFullSorties();" style="max-width: 15em;">
          <option value="">&mdash; Tous les praticiens</option>
          {{mb_include module=mediusers template=inc_options_mediuser list=$prats selected=$sejour->praticien_id}}
        </select>
        <select name="only_confirmed" onchange="reloadFullSorties();" style="max-width: 12em;">
          <option value="">&mdash; Toutes les sorties</option>
          <option value="1" {{if $only_confirmed}}selected{{/if}}>Confirmées seulement</option>
        </select>
      </form>
      <a href="#" onclick="printPlanning()" class="button print">Imprimer</a>
      <a href="#" onclick="Admissions.choosePrintForSelection()" class="button print">{{tr}}CCompteRendu-print_for_select{{/tr}}</a>
      {{if "web100T"|module_active}}
        {{mb_include module=web100T template=inc_button_send_all_prestations type=sortie}}
      {{/if}}
    </td>
  </tr>
    <tr>
      <td id="allSorties" style="width: 250px">
      </td>
      <td id="listSorties" style="width: 100%">
      </td>
    </tr>
</table>