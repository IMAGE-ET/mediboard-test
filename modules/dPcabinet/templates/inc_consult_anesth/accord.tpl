{{assign var="chir_id" value=$consult->_ref_plageconsult->_ref_chir->_id}}
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
  
  <div id="Exams">
    <div id="ExamsHeader" class="accordionTabTitleBar">
      Examens Clinique
    </div>
    <div id="ExamsContent"  class="accordionTabContentBox">
      {{include file="inc_consult_anesth/acc_examens_clinique.tpl"}}
    </div>
  </div>
  
  {{if $app->user_prefs.ccam == 1 }}
  <div id="Actes">
    <div id="ActesHeader" class="accordionTabTitleBar">
      Actes CCAM
    </div>
    <div id="ActesContent"  class="accordionTabContentBox">
    <table class="tbl">
      <tbody id="ccam">
      {{assign var="module" value="dPcabinet"}}
      {{assign var="subject" value=$consult}}
      {{include file="../../dPsalleOp/templates/inc_gestion_ccam.tpl"}}
      </tbody>
    </table>
    </div>
  </div>
  {{/if}}
   
  <div id="ExamsComp">
    <div id="ExamsCompHeader" class="accordionTabTitleBar">
      Examens Complémentaires
    </div>
    <div id="ExamsCompContent"  class="accordionTabContentBox">
      {{include file="inc_consult_anesth/acc_examens_complementaire.tpl"}}
    </div>
  </div>
 
  <div id="InfoAnesth">
    <div id="InfoAnesthHeader" class="accordionTabTitleBar">
      Informations Anesthésie
    </div>
    <div id="InfoAnesthContent"  class="accordionTabContentBox">
      {{include file="inc_consult_anesth/acc_infos_anesth.tpl"}}      
    </div>
  </div>
  
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
  panelHeight: fHeight, 
  showDelay:50, 
  showSteps:3 
} );
</script>