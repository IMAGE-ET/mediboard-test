{{assign var="chir_id" value=$consult->_ref_plageconsult->chir_id}}
{{assign var="object" value=$consult}}
{{assign var="module" value="dPcabinet"}}
{{assign var="do_subject_aed" value="do_consultation_aed"}}
{{include file="../../dPsalleOp/templates/js_gestion_ccam.tpl"}}

<div class="accordionMain" id="accordionConsult">
  
  <div id="AntTrait">
    <div id="AntTraitHeader" class="accordionTabTitleBar">
      Antécédents / Traitements
    </div>
    <div id="AntTraitContent"  class="accordionTabContentBox">
      {{include file="inc_ant_consult.tpl"}}
    </div>
  </div>

  <div id="Examens">
    <div id="ExamensHeader" class="accordionTabTitleBar">
      Examens
    </div>
    <div id="ExamensContent"  class="accordionTabContentBox">
      <div id="mainConsult">
      {{include file="inc_main_consultform.tpl"}}
      </div>
    </div>
  </div>
  
  {{if $app->user_prefs.ccam == 1 }}
  <div id="Actes">
    <div id="ActesHeader" class="accordionTabTitleBar">
      Actes CCAM
    </div>
    <div id="ActesContent"  class="accordionTabContentBox">
      <table class="tbl"> 
        <tr>
          <th>Actes<br /><br />
            {{tr}}{{$consult->_class_name}}{{/tr}}
            {{if ($module=="dPplanningOp") || ($module=="dPsalleOp")}}
            <br />
            Côté {{tr}}COperation.cote.{{$consult->cote}}{{/tr}}
            <br />
            ({{$consult->temp_operation|date_format:"%Hh%M"}})
            {{/if}}
          </th>
          <td>
            <div id="ccam">
              {{assign var="module" value="dPcabinet"}}
              {{assign var="subject" value=$consult}}
              {{include file="../../dPsalleOp/templates/inc_gestion_ccam.tpl"}}
            </div>
          </td>
        </tr>
      </table>
    </div>
  </div>
  {{/if}}  
  
  <div id="fdrConsult">
    <div id="fdrConsultHeader" class="accordionTabTitleBar">
      Documents et Réglements
    </div>
    <div id="fdrConsultContent"  class="accordionTabContentBox">
    {{include file="inc_fdr_consult.tpl"}}
    </div>
  </div>

</div>

<script language="Javascript" type="text/javascript">
var oAccord = new Rico.Accordion( $('accordionConsult'), { 
  panelHeight: 320, 
  showDelay:50, 
  showSteps:3 
} );
</script>
