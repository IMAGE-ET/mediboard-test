{{* $Id: vw_idx_admission.tpl 14901 2012-03-15 09:49:05Z flaviencrochard $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 14901 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=hprim21     script=pat_hprim_selector}}
{{mb_script module=hprim21     script=sejour_hprim_selector}}
{{mb_script module=admissions  script=admissions}}
{{mb_script module=patients    script=antecedent}}
{{mb_script module=compteRendu script=document}}
{{mb_script module=compteRendu script=modele_selector}}
{{mb_script module=files     script=file}}
{{mb_script module=planningOp  script=sejour}}
{{if "web100T"|module_active}}
  {{mb_script module=web100T script=web100T}}
{{/if}}

<script>
  function printPlanning() {
    var oForm = getForm("selType");
    var url = new Url("dPadmissions", "print_entrees");
    url.addParam("date"      , "{{$date}}");
    url.addParam("type"      , $V(oForm._type_admission));
    url.addParam("service_id", [$V(oForm.service_id)].flatten().join(","));
    url.popup(700, 550, "Entrees");
  }

  function reloadFullPresents(filterFunction) {
    var oForm = getForm("selType");
    var url = new Url("dPadmissions", "httpreq_vw_all_presents");
    url.addParam("date"      , "{{$date}}");
    url.addParam("type"      , $V(oForm._type_admission));
    url.addParam("service_id", [$V(oForm.service_id)].flatten().join(","));
    url.addParam("prat_id", $V(oForm.prat_id));
    url.addParam("active_filter_services" , $V(oForm.elements['active_filter_services']));
    url.requestUpdate('allPresents');
    reloadPresent(filterFunction);
  }

  function reloadPresent(filterFunction) {
    var oForm = getForm("selType");
    var url = new Url("dPadmissions", "httpreq_vw_presents");
    url.addParam("date"      , "{{$date}}");
    url.addParam("type"      , $V(oForm._type_admission));
    url.addParam("service_id", [$V(oForm.service_id)].flatten().join(","));
    url.addParam("prat_id", $V(oForm.prat_id));
    url.addParam("active_filter_services" , $V(oForm.elements['active_filter_services']));
    if(!Object.isUndefined(filterFunction)){
      url.addParam("filterFunction", filterFunction);
    }
    url.requestUpdate('listPresents');
  }

  function confirmation(oForm) {
    if(confirm('La date enregistrée d\'admission est différente de la date prévue, souhaitez vous confimer l\'admission du patient ?')){
      submitAdmission(oForm);
    }
  }

  function submitCote(oForm) {
    return onSubmitFormAjax(oForm, reloadAdmission);
  }

  function submitAdmission(oForm, bPassCheck) {
    {{if "dPsante400"|module_active && "CAppUI::conf"|static_call:"dPsante400 CIdSante400 admit_ipp_nda_obligatory":"CGroups-$g"}}
      var oIPPForm = getForm("editIPP" + $V(oForm.patient_id));
      var oNumDosForm = getForm("editNumdos" + $V(oForm.sejour_id));
      if(!bPassCheck && oIPPForm && oNumDosForm && (!$V(oIPPForm.id400) || !$V(oNumDosForm.id400)) ) {
        Idex.edit_manually($V(oNumDosForm.object_class)+"-"+$V(oNumDosForm.object_id),
                           $V(oIPPForm.object_class)+"-"+$V(oIPPForm.object_id),
                            reloadAdmission.curry());
      } else {
        return onSubmitFormAjax(oForm, reloadAdmission);
      }
    {{else}}
      return onSubmitFormAjax(oForm, reloadAdmission);
    {{/if}}
  }

  Main.add(function () {
    Admissions.table_id = "listPresents";

    var totalUpdater = new Url("admissions", "httpreq_vw_all_presents");
    totalUpdater.addParam("date", "{{$date}}");
    Admissions.totalUpdater = totalUpdater.periodicalUpdate('allPresents', { frequency: 120 });

    var listUpdater = new Url("admissions", "httpreq_vw_presents");
    listUpdater.addParam("date", "{{$date}}");
    Admissions.listUpdater = listUpdater.periodicalUpdate('listPresents', {
      frequency: 120,
      onCreate: function() {
        WaitingMessage.cover($('listPresents'));
        Admissions.rememberSelection();
      } });
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
      {{mb_field object=$sejour field="_type_admission" emptyLabel="CSejour.all" onchange="reloadFullPresents()"}}

      <input type="checkbox" name="_active_filter_services" title="Prendre en compte le filtre sur les services"
             onclick="$V(this.form.active_filter_services, this.checked ? 1 : 0); this.form.filter_services.disabled = !this.checked;"
             {{if $enabled_service == 1}}checked{{/if}} />
      <input type="hidden" name="active_filter_services" onchange="reloadFullPresents();" value="{{$enabled_service}}"/>
      <button type="button" name ="filter_services" onclick="Admissions.selectServices('listPresents');" class="search" {{if $enabled_service == 0}}disabled{{/if}}>Services</button>

      <select name="prat_id" onchange="reloadFullPresents();">
        <option value="">&mdash; Tous les praticiens</option>
        {{foreach from=$prats item=_prat}}
          <option value="{{$_prat->_id}}"{{if $_prat->_id == $sejour->praticien_id}}selected="selected"{{/if}}>{{$_prat}}</option>
        {{/foreach}}
      </select>
    </form>
    <a href="#" onclick="printPlanning()" class="button print" style="display: none;">Imprimer</a>
    <a href="#" onclick="Admissions.choosePrintForSelection()" class="button print">{{tr}}CCompteRendu-print_for_select{{/tr}}</a>
    {{if "web100T"|module_active}}
      {{mb_include module=web100T template=inc_button_send_all_prestations type=admissions}}
    {{/if}}
  </td>
</tr>
  <tr>
    <td id="allPresents" style="width: 250px">
    </td>
    <td id="listPresents" style="width: 100%">
    </td>
  </tr>
</table>