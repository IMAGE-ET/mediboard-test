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
new Rico.Accordion( $('accordionConsult'), { panelHeight:340, showDelay:50, showSteps:3 } );
</script>