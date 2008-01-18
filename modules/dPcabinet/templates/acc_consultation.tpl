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
  
  {{if $app->user_prefs.ccam_consultation == 1 }}
  <div id="Actes">
    <div id="ActesHeader" class="accordionTabTitleBar">
      Gestion des actes
    </div>
    <div id="ActesContent"  class="accordionTabContentBox">
      <table class="form">
        <tr>
          <td colspan="2">
            <ul id="main_tab_group" class="control_tabs">
              <li><a href="#one">Actes CCAM</a></li>
              <li><a href="#two">Actes NGAP</a></li>
            </ul>
          </td>
        </tr>
        <tr id="one">
          <th class="category">Actes<br /><br />
            {{tr}}{{$consult->_class_name}}{{/tr}}
            {{if ($module=="dPplanningOp") || ($module=="dPsalleOp")}}
            <br />
            Côté {{tr}}COperation.cote.{{$consult->cote}}{{/tr}}
            <br />
            ({{$consult->temp_operation|date_format:"%Hh%M"}})
            {{/if}}
          </th>
          <td>
          {{if $consult->sejour_id}}
          <div id="cim">
              {{assign var="sejour" value=$consult->_ref_sejour}}
              {{include file="../../dPsalleOp/templates/inc_diagnostic_principal.tpl" modeDAS="0"}}
          </div>
          {{/if}}
          <div id="ccam">
            {{assign var="module" value="dPcabinet"}}
            {{assign var="subject" value=$consult}}
            {{include file="../../dPsalleOp/templates/inc_gestion_ccam.tpl"}}
          </div>
          </td>
        </tr>
        <tr id="two">
          <th class="category">Actes NGAP</th>
          <td id="listActesNGAP">
            {{assign var="_object_class" value="CConsultation"}}
            {{include file="../../dPcabinet/templates/inc_acte_ngap.tpl"}}
          </td>
        </tr>
      </table>
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
