{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=hprim21     script=pat_hprim_selector}}
{{mb_script module=hprim21     script=sejour_hprim_selector}}
{{mb_script module=admissions  script=admissions}}
{{mb_script module=patients    script=antecedent}}
{{mb_script module=compteRendu script=document}}
{{mb_script module=compteRendu script=modele_selector}}
{{mb_script module=files       script=file}}
{{mb_script module=planningOp  script=sejour}}
{{mb_script module=planningOp  script=prestations}}
{{if "dPsante400"|module_active}}
  {{mb_script module=dPsante400  script=Idex}}
{{/if}}

{{if "web100T"|module_active}}
  {{mb_script module=web100T script=web100T}}
{{/if}}

<script>
  function printPlanning() {
    var form = getForm("selType");
    var url = new Url("admissions", "print_entrees");
    url.addParam("date"      , $V(form.date));
    url.addParam("type"      , $V(form._type_admission));
    url.addParam("service_id", [$V(form.service_id)].flatten().join(","));
    url.addParam("period"    , $V(form.period));
    url.addParam("group_by_prat", $V(form.group_by_prat));
    url.addParam("order_by", $V(form.order_print_way));
    url.popup(700, 550, "Entrees");
  }

  function reloadFullAdmissions() {
    var form = getForm("selType");
    var url = new Url("admissions", "httpreq_vw_all_admissions");
    url.addParam("date"      , $V(form.date));
    url.addParam("type"      , $V(form._type_admission));
    url.addParam("service_id", [$V(form.service_id)].flatten().join(","));
    url.addParam("prat_id"   , $V(form.prat_id));
    url.addParam("selAdmis"  , $V(form.selAdmis));
    url.addParam("selSaisis" , $V(form.selSaisis));
    url.requestUpdate('allAdmissions');
    reloadAdmission();
  }

  function reloadAdmission() {
    var form = getForm("selType");
    var url = new Url("admissions", "httpreq_vw_admissions");
    url.addParam("date"      , $V(form.date));
    url.addParam("type"      , $V(form ._type_admission));
    url.addParam("service_id", [$V(form .service_id)].flatten().join(","));
    url.addParam("prat_id"   , $V(form .prat_id));
    url.addParam("period"    , $V(form .period));
    url.addParam("order_way" , $V(form.order_way));
    url.addParam("order_col" , $V(form.order_col));
    url.addParam("selAdmis"  , $V(form.selAdmis));
    url.addParam("selSaisis" , $V(form.selSaisis));
    url.addParam("filterFunction", $V(form.filterFunction));
    url.requestUpdate('listAdmissions');
  }

  function reloadAdmissionLine(sejour_id) {
    var url = new Url("admissions", "ajax_admission_line");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate("CSejour-"+sejour_id);
  }

  function reloadAdmissionDate(elt, date) {
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
    if ($V(form.selSaisis) == 'n') {
      pos = 2;
    }
    else if ($V(form.selAdmis) == 'n') {
      pos = 3;
    }
    var td = elt_tr.down("td", pos);

    td.writeAttribute("style", "font-weight: bold");
    reloadAdmission();
  }

  function confirmation(form) {
    if (confirm('La date enregistrée d\'admission est différente de la date prévue, souhaitez vous confimer l\'admission du patient ?')) {
      submitAdmission(form);
    }
  }

  function submitCote(form, sejour_id) {
    return onSubmitFormAjax(form, reloadAdmissionLine.curry(sejour_id));
  }

  function submitMultiple(form) {
      return onSubmitFormAjax(form, reloadFullAdmissions);
  }

  function submitAdmission(form, bPassCheck) {
    {{if "dPsante400"|module_active && "CAppUI::conf"|static_call:"dPsante400 CIdSante400 admit_ipp_nda_obligatory":"CGroups-$g"}}
      var oIPPForm    = getForm("editIPP" + $V(form.patient_id));
      var oNumDosForm = getForm("editNumdos" + $V(form.sejour_id));
      if (!bPassCheck && oIPPForm && oNumDosForm && (!$V(oIPPForm.id400) || !$V(oNumDosForm.id400)) ) {
        Idex.edit_manually($V(oNumDosForm.object_class)+"-"+$V(oNumDosForm.object_id),
                           $V(oIPPForm.object_class)+"-"+$V(oIPPForm.object_id),
                           reloadAdmissionLine.curry($V(form.sejour_id)));
      }
      else {
        return onSubmitFormAjax(form, reloadAdmissionLine.curry($V(form.sejour_id)));
      }
    {{else}}
      return onSubmitFormAjax(form, reloadAdmissionLine.curry($V(form.sejour_id)));
    {{/if}}
  }

  function sortBy(order_col, order_way) {
    var form = getForm("selType");
    $V(form.order_col, order_col);
    $V(form.order_way, order_way);
    reloadAdmission();
  }

  function filterAdm(selAdmis, selSaisis) {
    var form = getForm("selType");
    $V(form.selAdmis, selAdmis);
    $V(form.selSaisis, selSaisis);
    reloadFullAdmissions();
  }

  function changeEtablissementId(oForm) {
    $V(oForm._modifier_entree, '0');
    submitAdmission(oForm);
  }

  updateModeEntree = function(select) {
    var selected = select.options[select.selectedIndex];
    var form = select.form;
    $V(form.elements.mode_entree, selected.get("mode"));
  }

  {{assign var=auto_refresh_frequency value=$conf.dPadmissions.auto_refresh_frequency_admissions}}

  Main.add(function() {
    Admissions.table_id = "listAdmissions";
    var form = getForm("selType");

    var totalUpdater = new Url("admissions", "httpreq_vw_all_admissions");
    var listUpdater = new Url("admissions", "httpreq_vw_admissions");
    {{if $auto_refresh_frequency != 'never'}}
      Admissions.totalUpdater = totalUpdater.periodicalUpdate('allAdmissions', {frequency: {{$auto_refresh_frequency}}});

      Admissions.listUpdater = listUpdater.periodicalUpdate('listAdmissions', {
        frequency: {{$auto_refresh_frequency}},
        onCreate: function() {
          WaitingMessage.cover($('listAdmissions'));
          Admissions.rememberSelection();
        }
      });
    {{else}}
      totalUpdater.requestUpdate('allAdmissions');
      listUpdater.requestUpdate('listAdmissions', {
        onCreate: function() {
          WaitingMessage.cover($('listAdmissions'));
          Admissions.rememberSelection();
        }});
    {{/if}}
  });
</script>

<div style="display: none" id="area_prompt_modele">
  {{mb_include module=admissions template=inc_prompt_modele type=admissions}}
</div>

<table class="main">
<tr>
  <td>
    <a href="#legend" onclick="Admissions.showLegend()" class="button search">Légende</a>
    {{if "astreintes"|module_active}}{{mb_include module=astreintes template=inc_button_astreinte_day date=$date}}{{/if}}
  </td>
  <td style="float: right">
    <form action="?" name="selType" method="get">
      <input type="hidden" name="date" value="{{$date}}" />
      <input type="hidden" name="filterFunction" value="{{$filterFunction}}" />
      <input type="hidden" name="selAdmis" value="{{$selAdmis}}" />
      <input type="hidden" name="selSaisis" value="{{$selSaisis}}" />
      <input type="hidden" name="order_col" value="{{$order_col}}" />
      <input type="hidden" name="order_way" value="{{$order_way}}" />
      <!-- print -->
      <input type="hidden" name="order_print_way" value="" />
      <input type="hidden" name="group_by_prat" value="1">


      <select name="period" onchange="reloadAdmission();">
        <option value=""      {{if !$period          }}selected{{/if}}>&mdash; Toute la journée</option>
        <option value="matin" {{if $period == "matin"}}selected{{/if}}>Matin</option>
        <option value="soir"  {{if $period == "soir" }}selected{{/if}}>Soir</option>
      </select>
      {{mb_field object=$sejour field="_type_admission" emptyLabel="CSejour.all" onchange="reloadFullAdmissions()"}}
      <select name="service_id" onchange="reloadFullAdmissions();" {{if $sejour->service_id|@count > 1}}size="5" multiple="true"{{/if}}>
        <option value="">&mdash; Tous les services</option>
        {{foreach from=$services item=_service}}
          <option value="{{$_service->_id}}" {{if in_array($_service->_id, $sejour->service_id)}}selected{{/if}}>{{$_service}}</option>
        {{/foreach}}
      </select>
      <input type="checkbox" onclick="Admissions.toggleMultipleServices(this)" {{if $sejour->service_id|@count > 1}}checked{{/if}}/>
      <select name="prat_id" onchange="reloadFullAdmissions();">
        <option value="">&mdash; Tous les praticiens</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$prats selected=$sejour->praticien_id}}
      </select>
    </form>

    <button type="button" onclick="Modal.open('preparePrintPlanning', {width: '500px', height: '150px'});" class="button print">{{tr}}Print{{/tr}}</button>

    <div id="preparePrintPlanning" style="display: none;">
      <form name="print_filter_option" method="get" style="text-align: center">
        <h2>Options de l'impression</h2>
        <p>
            Grouper par praticien
          <label><input type="radio" name="group_by_prat" value="1" checked="checked" onchange="$V(getForm('selType').group_by_prat, this.value);"/>Oui</label>
          <label><input type="radio" name="group_by_prat" value="0" onchange="$V(getForm('selType').group_by_prat, this.value);"/>Non</label>
        </p>
        <p>
          <label>
            Ordonner par :
            <select name="order_by" onchange="$V(getForm('selType').order_print_way, this.value);">
              <option value="">Nom du Praticien</option>
              <option value="patient_name">Nom du patient</option>
              <option value="entree_prevue">Heure d'admission prévue</option>
              <option value="entre_reelle">Heure d'admission réelle</option>
            </select>
          </label>
        </p>
        <p><button type="button" onclick="printPlanning()" class="button print">{{tr}}Print{{/tr}}</button><button class="cancel" type="button" onclick="Control.Modal.close();">{{tr}}Cancel{{/tr}}</button> </p>
      </form>
    </div>

    <a href="#" onclick="Admissions.choosePrintForSelection()" class="button print">{{tr}}CCompteRendu-print_for_select{{/tr}}</a>
    {{if "web100T"|module_active}}
      {{mb_include module=web100T template=inc_button_send_all_prestations type=admissions}}
    {{/if}}
  </td>
</tr>
  <tr>
    <td id="allAdmissions" style="width: 250px">
    </td>
    <td id="listAdmissions" style="width: 100%">
    </td>
  </tr>
</table>