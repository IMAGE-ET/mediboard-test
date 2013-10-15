{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="soins" script="plan_soins"}}

{{if "dPprescription"|module_active}}
  {{mb_script module="prescription" script="prescription"}}
  {{mb_script module="prescription" script="element_selector"}}
{{/if}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="medicament" script="medicament_selector"}}
  {{mb_script module="medicament" script="equivalent_selector"}}
{{/if}}

{{mb_script module="planningOp"  script="cim10_selector"}}
{{mb_script module="compteRendu" script="document"}}
{{mb_script module="compteRendu" script="modele_selector"}}
{{mb_script module="cabinet"     script="file"}}
{{mb_script module="system"      script="alert"}}

{{if "dPImeds"|module_active}}
  {{mb_script module="Imeds" script="Imeds_results_watcher"}}
{{/if}}

<style type="text/css">
  tr + tr { /* Avoid page break before a TR following another TR */
    page-break-before: avoid;
  }
</style>

<script>
  showDossierSoins = function(sejour_id, date, default_tab){
    var url = new Url("soins", "ajax_vw_dossier_sejour");
    url.addParam("sejour_id", sejour_id);
    if(default_tab){
      url.addParam("default_tab", default_tab);
    }
    url.requestModal("95%", "90%", {
      showClose: false
    });
    modalWindow = url.modalObject;
  }

  refreshLineSejour = function(sejour_id) {
    var url = new Url("soins", "vw_sejours");
    url.addParam("sejour_id", sejour_id);
    url.addParam("service_id", "{{$service_id}}");
    url.addParam("function_id", "{{$function->_id}}");
    url.addParam("praticien_id", "{{$praticien->_id}}");
    url.addParam("show_affectation", '{{$show_affectation}}');
    url.requestUpdate("line_sejour_"+sejour_id, { onComplete: function(){
      {{if "dPImeds"|module_active}}
      ImedsResultsWatcher.loadResults();
      {{/if}}
    } });
  }

  showTasks = function(element, tooltip_id, sejour_id) {
    var url = new Url("soins", "ajax_vw_tasks_sejour");
    url.addParam("sejour_id", sejour_id);
    url.addParam("mode_realisation", true);
    url.requestUpdate(tooltip_id, { onComplete: function(){
      ObjectTooltip.createDOM(element, tooltip_id, {duration: 0});
    } });
  }

  showTasksNotCreated = function(element, tooltip_id, sejour_id) {
    var url = new Url("soins", "ajax_vw_lines_vithout_task");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate(tooltip_id, { onComplete: function(){
      ObjectTooltip.createDOM(element, tooltip_id, {duration: 0});
    } });
  }

  // Cette fonction est dupliquée
  loadSuivi = function(sejour_id, user_id, cible, show_obs, show_trans, show_const) {
    if(!sejour_id) return;

    updateNbTrans(sejour_id);
    var urlSuivi = new Url("hospi", "httpreq_vw_dossier_suivi");

    urlSuivi.addParam("sejour_id", sejour_id);
    urlSuivi.addParam("user_id", user_id);
    urlSuivi.addParam("cible", cible);

    if (!Object.isUndefined(show_obs)) {
      urlSuivi.addParam("_show_obs", show_obs);
    }

    if (!Object.isUndefined(show_trans)) {
      urlSuivi.addParam("_show_trans", show_trans);
    }

    if (!Object.isUndefined(show_const)) {
      urlSuivi.addParam("_show_const", show_const);
    }

    urlSuivi.requestUpdate("dossier_suivi");
  }

  // Cette fonction est dupliquée
  function updateNbTrans(sejour_id) {
    var url = new Url("hospi", "ajax_count_transmissions");
    url.addParam("sejour_id", sejour_id);
    url.requestJSON(function(count)  {
      Control.Tabs.setTabCount('dossier_suivi', count);
    });
  }

  Main.add(function() {
    {{if "dPImeds"|module_active}}
      ImedsResultsWatcher.loadResults();
    {{/if}}

    {{if $print}}
    window.print();
    {{/if}}
  });


  printSejours = function(){
    var url = new Url("soins", "vw_sejours");
    url.addParam("service_id"       , "{{$service_id}}");
    url.addParam("praticien_id"     , "{{$praticien->_id}}");
    url.addParam("function_id"      , "{{$function->_id}}");
    url.addParam("sejour_id"        , "{{$sejour_id}}");
    url.addParam("show_affectation" , "{{$show_affectation}}");
    url.addParam("only_non_checked" , "{{$only_non_checked}}");
    url.addParam("print"            , true);
    url.popup(800, 600);
  }

</script>

{{if $print}}
  {{mb_include style=mediboard template=open_printable}}
{{else}}

  {{if $select_view}}
    <form name="editPrefVueSejour" method="post">
      <input type="hidden" name="m" value="admin" />
      <input type="hidden" name="dosql" value="do_preference_aed" />
      <input type="hidden" name="user_id" value="{{$app->user_id}}" />
      <input type="hidden" name="pref[vue_sejours]" value="standard" />
      <input type="hidden" name="postRedirect" value="m=soins&tab=vw_idx_sejour" />
      <button type="submit" class="change notext">Vue par défaut</button>
    </form>
  {{/if}}

  <form name="TypeHospi" method="get" action="?">
    <input type="hidden" name="m" value="soins" />

    {{if $select_view}}

      <input type="hidden" name="tab" value="vw_sejours" />
    {{else}}
      <input type="hidden" name="a" value="vw_sejours" />
    {{/if}}

    <input type="hidden" name="show_affectation" value="{{$show_affectation}}" />
    <input type="hidden" name="only_non_checked" value="{{$only_non_checked}}" />

    {{if $select_view}}
      <input type="hidden" name="select_view" value="{{$select_view}}" />
      <select name="service_id" style="width: 200px;" onchange="this.form.praticien_id.value = ''; this.form.function_id.value = ''; this.form.submit();">
        <option value="">&mdash; Service</option>
        {{foreach from=$services item=_service}}
          <option value="{{$_service->_id}}" {{if $_service->_id == $service_id}}selected{{/if}}>{{$_service->_view}}</option>
        {{/foreach}}
      </select>

      <select name="praticien_id" style="width: 200px;" onchange="this.form.service_id.value = ''; this.form.function_id.value = ''; this.form.submit();">
        <option value="">&mdash; Praticien</option>
        {{foreach from=$praticiens item=_praticien}}
          <option value="{{$_praticien->_id}}" {{if $_praticien->_id == $praticien_id}}selected{{/if}}>{{$_praticien->_view}}</option>
        {{/foreach}}
      </select>

      <select name="function_id" style="width: 200px;" onchange="this.form.praticien_id.value = ''; this.form.service_id.value = ''; this.form.submit();">
        <option value="">&mdash; Cabinet</option>
        {{foreach from=$functions item=_function}}
          <option value="{{$_function->_id}}" {{if $_function->_id == $function_id}}selected{{/if}}>{{$_function->_view}}</option>
        {{/foreach}}
      </select>
      <br />
    {{else}}
      <input type="hidden" name="service_id" value="{{$service_id}}" />
      <input type="hidden" name="praticien_id" value="{{$praticien->_id}}" />
      <input type="hidden" name="function_id" value="{{$function->_id}}" />
    {{/if}}

    {{mb_label class="CSejour" field="_type_admission"}}
    {{mb_field object=$_sejour field="_type_admission" typeEnum="radio" onclick="this.form.submit()"}}
  </form>
{{/if}}

<table class="main tbl">
  <tr>
    <th class="title" colspan="14" {{if $print}}onclick="window.print();"{{/if}}>
      {{if !$print}}
      <button type="button" class="print notext" style="float: right;" onclick="printSejours();">{{tr}}Print{{/tr}}</button>
      {{/if}}
      {{if $service->_id}}
        Séjours du service {{$service}}
      {{elseif $function->_id}}
        Séjours du cabinet {{$function}}
      {{elseif $praticien->_id}}
        Séjours  du praticien {{$praticien}}
      {{else}}
        Patients non placés
      {{/if}}
      ({{$sejours|@count}})
    </th>
  </tr>

  {{if !$print}}
  <tr>
    {{if $service->_id || $function->_id || $praticien->_id || $show_affectation}}
      <th rowspan="2">{{mb_title class=CLit field=chambre_id}}</th>
    {{/if}}
    <th colspan="2" rowspan="2">{{mb_title class=CPatient field=nom}}<br />({{mb_title class=CPatient field=nom_jeune_fille}})</th>
    {{if "dPImeds"|module_active}}
      <th rowspan="2">Labo</th>
    {{/if}}
    <th colspan="5">Alertes</th>
    <th rowspan="2" class="narrow">{{mb_title class=CSejour field=entree}}</th>
    <th rowspan="2">{{mb_title class=CSejour field=libelle}}</th>
    <th rowspan="2">Prat.</th>
    <th rowspan="2">Projet de soin<br />Demandes particulières</th>
  </tr>
  <tr>
    <th><label title="Modification de prescriptions">Presc.</label></th>
    <th><label title="Prescriptions urgentes">Urg.</label></th>
    <th>Attentes</th>
    <th>Allergies</th>
    <th><label title="Antécédents">Atcd</label></th>
  </tr>
  {{/if}}

  {{foreach from=$sejours item=sejour}}

  {{if $print}}
    {{mb_include module=soins template=inc_vw_print_sejour}}

  {{else}}
    <tr id="line_sejour_{{$sejour->_id}}">
      {{mb_include module=soins template=inc_vw_sejour}}
    </tr>
  {{/if}}

  {{foreachelse}}
  <tr>
    <td colspan="15" class="empty">
      {{tr}}CSejour.none{{/tr}}
    </td>
  </tr>
  {{/foreach}}
</table>

{{if $print}}
  {{mb_include style=mediboard template=close_printable}}
{{/if}}