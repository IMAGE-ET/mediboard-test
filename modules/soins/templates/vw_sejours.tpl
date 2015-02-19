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
{{mb_script module="files"     script="file"}}
{{mb_script module="system"      script="alert"}}

{{if "dPImeds"|module_active}}
  {{mb_script module="Imeds" script="Imeds_results_watcher"}}
{{/if}}

{{assign var=auto_refresh_frequency value="soins Sejour refresh_vw_sejours_frequency"|conf:"CGroups-$g"}}

<style type="text/css">
  tr + tr { /* Avoid page break before a TR following another TR */
    page-break-before: avoid;
  }
</style>

<script>
  showDossierSoins = function(sejour_id, date, default_tab){
    var url = new Url("soins", "ajax_vw_dossier_sejour");
    url.addParam("sejour_id", sejour_id);
    url.addParam("modal", 1);
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
    url.addParam("sejour_id"       , sejour_id);
    url.addParam("service_id"      , "{{$service_id}}");
    url.addParam("function_id"     , "{{$function->_id}}");
    url.addParam("praticien_id"    , "{{$praticien->_id}}");
    url.addParam("show_affectation", '{{$show_affectation}}');
    url.addParam("select_view"     , '{{$select_view}}');
    url.requestUpdate("line_sejour_"+sejour_id, { onComplete: function(){
      {{if "dPImeds"|module_active}}
      ImedsResultsWatcher.loadResults();
      {{/if}}

      {{if $app->user_prefs.show_file_view}}
        FilesCategory.iconInfoReadFilesGuid('CSejour', ['{{$sejour_id}}']);
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
  loadSuivi = function(sejour_id, user_id, cible, show_obs, show_trans, show_const, show_header) {
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
    if (!Object.isUndefined(show_header)) {
      urlSuivi.addParam("show_header", show_header);
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

    {{if $app->user_prefs.show_file_view}}
    FilesCategory.showUnreadFiles();
    {{/if}}

    {{if !$ecap && $auto_refresh_frequency != 'disabled'}}
      /* Utilisation d'un timeout pour éviter que la page soit rechargée après le 1er chargement */
      setTimeout(function() {
        var url = new Url('soins', 'vw_sejours');
        url.addParam('service_id', '{{$service_id}}');
        {{if $select_view}}
        url.addParam('praticien_id', '{{$praticien_id}}');
        url.addParam('function_id', '{{$function_id}}');
        {{/if}}
        url.addParam('sejour_id', '{{$sejour_id}}');
        url.addParam('show_affectation', '{{$show_affectation}}');
        url.addParam('only_non_checked', '{{$only_non_checked}}');
        url.addParam('print', '{{$print}}');
        url.addParam('select_view', '{{$select_view}}');
        url.addParam('mode', '{{$mode}}');
        url.addParam('date', '{{$date}}');
        url.addParam('refresh', true);
        url.periodicalUpdate('idx_sejours', {frequency: {{$auto_refresh_frequency}}});
      }, {{$auto_refresh_frequency}} * 1000);
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

<div id="idx_sejours">
  {{mb_include module=soins template=inc_vw_sejours_global}}
</div>