<div style="margin-top:6px; border-top-width:1px; border-top-style:solid;" id="accordionConsult">

  <div id="panel1">
    <div id="panel1Header" class="accordionTabTitleBar" style="background-color:#f00;">
      Informations sur le patient
    </div>
    <div id="panel1Content"  class="accordionTabContentBox">
      {{include file="inc_patient_infos_accord.tpl"}}
    </div>
  </div>
  
  <div id="panel2">
    <div id="panel2Header" class="accordionTabTitleBar">
      Antécédents / Traitements
    </div>
    <div id="panel2Content"  class="accordionTabContentBox">
      {{include file="inc_ant_consult.tpl"}}
    </div>
  </div>

  <div id="panel3">
    <div id="panel3Header" class="accordionTabTitleBar">
      Examens
    </div>
    <div id="panel3Content"  class="accordionTabContentBox">
      {{include file="inc_main_consultform.tpl"}}
    </div>
  </div>

  <div id="panel4">
    <div id="panel4Header" class="accordionTabTitleBar">
      Evaluation des conditions d'intubations et Intervention
    </div>
    <div id="panel4Content"  class="accordionTabContentBox">
      {{include file="inc_intubation.tpl"}}
      <div id="choixAnesth">
      {{include file="inc_type_anesth.tpl"}}
      </div>
    </div>
  </div>
  
  <div id="panel5">
    <div id="panel5Header" class="accordionTabTitleBar">
      Documents et Réglements
    </div>
    <div id="panel5Content"  class="accordionTabContentBox">
      <div id="fdrConsult">
      {{include file="inc_fdr_consult.tpl"}}
      </div>
    </div>
  </div>

</div>

<script language="Javascript" type="text/javascript">
new Rico.Accordion( $('accordionConsult'), {panelHeight:310} );
</script>
