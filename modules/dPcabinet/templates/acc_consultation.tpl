{{assign var="chir_id" value=$consult->_ref_plageconsult->chir_id}}
{{assign var="object" value=$consult}}
{{assign var="module" value="dPcabinet"}}
{{assign var="do_subject_aed" value="do_consultation_aed"}}
{{include file="../../dPsalleOp/templates/js_gestion_ccam.tpl"}}

<script type="text/javascript">
function setField(oField, sValue) {
  oField.value = sValue;
}

Main.add(function () {
  var tabsConsult = Control.Tabs.create('tab-consult', false);
  var tabsActes = Control.Tabs.create('tab-actes', false);
});
</script>

<ul id="tab-consult" class="control_tabs">
  {{if $consult->sejour_id}}
  <li><a href="#rpuConsult">
     RPU 
    {{if $consult->_ref_sejour->_num_dossier}}
      [{{$consult->_ref_sejour->_num_dossier}}]
    {{/if}}</a>
  </li>
  {{/if}}
  
  <li><a href="#AntTrait">Antécédents</a></li>
  <li><a href="#Examens">Examens</a></li>
  
  {{if $app->user_prefs.ccam_consultation == 1}}
  <li><a href="#Actes">Actes</a></li>
  {{/if}}
  
  <li><a href="#fdrConsult">Docs et Règlements</a></li>
</ul>
<hr class="control_tabs" />

{{if $consult->sejour_id}}
{{assign var="rpu" value=$consult->_ref_sejour->_ref_rpu}}
<div id="rpuConsult" style="display: none;">{{include file="../../dPurgences/templates/inc_vw_rpu.tpl"}}</div>
{{/if}}

<div id="AntTrait" style="display: none;">{{include file="../../dPcabinet/templates/inc_ant_consult.tpl"}}</div>
<div id="Examens" style="display: none;">{{include file="../../dPcabinet/templates/inc_main_consultform.tpl"}}</div>

{{if $app->user_prefs.ccam_consultation == 1}}
<div id="Actes" style="display: none;">
  <ul id="tab-actes" class="control_tabs">
    <li><a href="#ccam">Actes CCAM</a></li>
    <li><a href="#ngap">Actes NGAP</a></li>
    {{if $consult->sejour_id}}
    <li><a href="#cim">Diagnostics</a></li>
    {{/if}}
  </ul>
  <hr class="control_tabs"/>
  
  <div id="ccam">
    {{assign var="module" value="dPcabinet"}}
    {{assign var="subject" value=$consult}}
    {{include file="../../dPsalleOp/templates/inc_gestion_ccam.tpl"}}
  </div>
  
  <div id="ngap">
    <div id="listActesNGAP">
      {{assign var="_object_class" value="CConsultation"}}
      {{include file="../../dPcabinet/templates/inc_acte_ngap.tpl"}}
    </div>
  </div>
  
  {{if $consult->sejour_id}}
  <div id="cim">
      {{assign var="sejour" value=$consult->_ref_sejour}}
      {{include file="../../dPsalleOp/templates/inc_diagnostic_principal.tpl" modeDAS="1"}}
  </div>
  {{/if}}
</div>
{{/if}}

<div id="fdrConsult" style="display: none;">{{include file="../../dPcabinet/templates/inc_fdr_consult.tpl"}}</div>