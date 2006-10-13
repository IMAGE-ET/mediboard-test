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
