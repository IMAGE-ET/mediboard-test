{{mb_script module="bloodSalvage" script="bloodSalvage"}}
{{mb_script module="soins" script="plan_soins"}}
{{mb_script module="planningOp" script="operation"}}

{{if @$modules.brancardage->_can->read}}
  {{mb_script module=brancardage script=creation_brancardage ajax=true}}
{{/if}}

{{if "dPprescription"|module_active}}
  {{mb_script module="prescription" script="prescription"}}
  {{mb_script module="prescription" script="element_selector"}}
{{/if}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="medicament" script="medicament_selector"}}
  {{mb_script module="medicament" script="equivalent_selector"}}
{{/if}}

{{mb_script module="planningOp" script="cim10_selector"}}
{{mb_script module="compteRendu" script="document"}}
{{mb_script module="compteRendu" script="modele_selector"}}
{{mb_script module="files" script="file"}}

{{if $isImedsInstalled}}
  {{mb_script module="Imeds" script="Imeds_results_watcher"}}
{{/if}}

<style>
  input.seek_patient {
    float:right;
  }
</style>

<script>
  Main.add(function () {
    ObjectTooltip.modes.allergies = {
      module: "patients",
      action: "ajax_vw_allergies",
      sClass: "tooltip"
    };

    Control.Tabs.create('reveil_tabs', true);

    var url = new Url("salleOp", "httpreq_reveil");

    url.addParam("bloc_id", "{{$bloc->_id}}");
    url.addParam("date", "{{$date}}");

    url.addParam("type", "preop");
    url.periodicalUpdate("preop", { frequency: 90 });

    url.addParam("type", "encours");
    url.periodicalUpdate("encours", { frequency: 90 });

    url.addParam("type", "ops");
    url.periodicalUpdate("ops", { frequency: 90 });

    url.addParam("type", "reveil");
    url.requestUpdate("reveil");

    url.addParam("type", "out");
    url.requestUpdate("out");
  });

  EditCheckList = {
    url: null,
    edit: function (bloc_id, date, type) {
      var url = new Url('salleOp', 'ajax_edit_checklist');
      url.addParam('date', date);
      url.addParam('bloc_id' , bloc_id);
      url.addParam('salle_id', 0);
      url.addParam('type', type);
      url.requestModal();
      url.modalObject.observe("afterClose", function () {
        location.reload();
      });
    }
  };

  function refreshTabsReveil() {
    refreshTabReveil("preop");
    refreshTabReveil("encours");
    refreshTabReveil("ops");
    refreshTabReveil("reveil");
    refreshTabReveil("out");
  }

  function refreshTabReveil(type) {
    var url = new Url("dPsalleOp", "httpreq_reveil");
    url.addParam("bloc_id", "{{$bloc->_id}}");
    url.addParam("date", "{{$date}}");
    url.addParam("type", type);
    url.requestUpdate(type);
  }

  orderTabReveil = function(col, way, type) {
    var url = new Url("dPsalleOp", "httpreq_reveil");
    url.addParam("bloc_id", "{{$bloc->_id}}");
    url.addParam("date", "{{$date}}");
    url.addParam("type", type);
    url.addParam("order_col", col);
    url.addParam("order_way", way);
    url.requestUpdate(type);
  };

  showDossierSoins = function(sejour_id, operation_id, default_tab) {
    {{if "dPprescription"|module_active}}
      var url = new Url("soins", "ajax_vw_dossier_sejour");
      url.addParam("sejour_id", sejour_id);
      url.addParam("operation_id", operation_id);
      url.addParam("modal", 0);
      if(default_tab){
        url.addParam("default_tab", default_tab);
      }
      url.modal({width: "95%", height: "95%"});
      modalWindow = url.modalObject;
    {{/if}}
  };

  printDossier = function(sejour_id, operation_id) {
    var url = new Url("hospi", "httpreq_documents_sejour");
    url.addParam("sejour_id", sejour_id);
    url.addParam("operation_id", operation_id);
    url.requestModal(700, 400);
  };

  callbackSortie = function(user_id) {
    if (!window.current_form) {
      return;
    }
    var form = window.current_form;
    $V(form.sortie_locker_id, form.sortie_reveil_possible.value ? user_id : '');
    submitReveilForm(form);
    Control.Modal.close();
  };

  seekPatient = function(input) {
    var value = $V(input);
    var field = $(input).up('table').select('span.CPatient-view');

    field.each(function(e) {
      if (!value) {
        e.up('tr').show();
      }
      else {
        if (!e.getText().like(value)) {
          e.up('tr').hide();
        }
        else {
          e.up('tr').show();
        }
      }
    });
  };
</script>

<ul id="reveil_tabs" class="control_tabs">
  <li onmousedown="refreshTabReveil('preop')">
    <a class="empty" href="#preop"  >{{tr}}SSPI.Preop{{/tr}}  <small>(&ndash;)</small></a>
  </li>
  <li onmousedown="refreshTabReveil('encours')">
    <a class="empty" href="#encours">{{tr}}SSPI.Encours{{/tr}}<small>(&ndash;)</small></a>
  </li>
  <li onmousedown="refreshTabReveil('ops')">
    <a class="empty" href="#ops"    >{{tr}}SSPI.Attente{{/tr}}<small>(&ndash;)</small></a>
  </li>
  <li onmousedown="refreshTabReveil('reveil')">
    <a class="empty" href="#reveil" >{{tr}}SSPI.Reveil{{/tr}} <small>(&ndash;)</small></a>
  </li>
  <li onmousedown="refreshTabReveil('out')">
    <a class="empty" href="#out"    >{{tr}}SSPI.Sortie{{/tr}} <small>(&ndash;)</small></a>
  </li>

  <li style="float:right; font-weight: bold;">
    {{mb_include template=inc_filter_reveil}}
  </li>
</ul>

<div id="preop"   style="display:none"></div>
<div id="encours" style="display:none"></div>
<div id="ops"     style="display:none"></div>
<div id="reveil"  style="display:none"></div>
<div id="out"     style="display:none"></div>