{{assign var="chir_id" value=$consult->_ref_plageconsult->chir_id}}
{{assign var="object" value=$consult}}
{{assign var="module" value="dPcabinet"}}
{{assign var="do_subject_aed" value="do_consultation_aed"}}
{{include file="../../dPsalleOp/templates/js_gestion_ccam.tpl"}}

<div class="accordionMain" id="accordionConsult">

  {{if $consult->sejour_id}}
  {{assign var="rpu" value=$consult->_ref_sejour->_ref_rpu}}
  <div id="rpuConsult">
    <div id="rpuHeader" class="accordionTabTitleBar">
      RPU 
      {{if $consult->_ref_sejour->_num_dossier}}
        [{{$consult->_ref_sejour->_num_dossier}}]
      {{/if}}
    </div>
    <div id="rpuContent"  class="accordionTabContentBox">
     {{include file="../../dPurgences/templates/inc_vw_rpu.tpl"}}
    </div>
  </div>
  {{/if}}
  
  <div id="AntTrait">
    <div id="AntTraitHeader" class="accordionTabTitleBar">
      Antécédents / Traitements
    </div>
    <div id="AntTraitContent"  class="accordionTabContentBox">
      {{include file="../../dPcabinet/templates/inc_ant_consult.tpl"}}
    </div>
  </div>

  <div id="Examens">
    <div id="ExamensHeader" class="accordionTabTitleBar">
      Examens
    </div>
    <div id="ExamensContent"  class="accordionTabContentBox">
      <div id="mainConsult">
      {{include file="../../dPcabinet/templates/inc_main_consultform.tpl"}}
      </div>
    </div>
  </div>
  
  {{if $app->user_prefs.ccam_consultation == 1}}
  <div id="Actes">
    <div id="ActesHeader" class="accordionTabTitleBar">
      Gestion des actes
    </div>
    <div id="ActesContent"  class="accordionTabContentBox">

      <ul id="main_tab_group" class="control_tabs">
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

      {{if $consult->sejour_id}}
      <div id="cim">
          {{assign var="sejour" value=$consult->_ref_sejour}}
          {{include file="../../dPsalleOp/templates/inc_diagnostic_principal.tpl"}}
      </div>
      {{/if}}

      <div id="ngap">
        <div id="listActesNGAP">
          {{assign var="_object_class" value="CConsultation"}}
          {{include file="../../dPcabinet/templates/inc_acte_ngap.tpl"}}
        </div>
      </div>

      <script type="text/javascript">new Control.Tabs('main_tab_group');</script>
    </div>
  </div>
  {{/if}}  
  
  <div id="fdrConsult">
    <div id="fdrConsultHeader" class="accordionTabTitleBar">
      Documents et Réglements
    </div>
    <div id="fdrConsultContent"  class="accordionTabContentBox">
    {{include file="../../dPcabinet/templates/inc_fdr_consult.tpl"}}
    </div>
  </div>
</div>
