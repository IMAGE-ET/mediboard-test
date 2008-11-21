<!-- Fermeture des tableaux -->
    </td>
  </tr>
</table>
  
<style type="text/css">

ul {
  padding-left: 11px;
}

.signature_ide {
  border: 1px solid #ccc;
}

@media screen {
  .footer, .header {
    display:none;
  }
}

@media print {
  .last_footer {
    display:none;
  }
}

</style>

<div class="plan_soin" {{if !$patient->_id}}style="overflow: auto; height: 500px;"{{/if}}>


<!-- Fin du header -->

<table style="border-collapse: collapse; border: 1px solid #ccc" class="tbl">
  {{include file="../../dPprescription/templates/inc_header_plan_soin.tpl" name="Médicaments" no_class=true}}
  
  <!-- Affichage des medicaments -->
  {{foreach from=$prescription->_ref_lines_med_for_plan item=_all_lines_unite_prise_cat}}
    {{foreach from=$_all_lines_unite_prise_cat item=_all_lines_unite_prise}}
      {{foreach from=$_all_lines_unite_prise key=unite_prise item=_line}}
        {{include file="../../dPprescription/templates/inc_vw_line_plan_soin.tpl" line=$_line suffixe=med}}
    {{/foreach}}
   {{/foreach}} 
  {{/foreach}}
  
  
  <!-- Affichage des perfusions -->
  {{foreach from=$prescription->_ref_perfusions_for_plan item=_perfusion}}
    {{include file="../../dPprescription/templates/inc_vw_perf_plan_soin.tpl"}}
  {{/foreach}}
  
  {{include file="../../dPprescription/templates/inc_footer_plan_soin.tpl" no_class=false last_screen_footer=false}}
  
  <!-- Séparation entre les medicaments et les elements -->
  <tr>
    <td colspan="1000" style="padding:0; height: 1px; border: 1px solid black;"></td>
  </tr>
   
  <!-- Affichage des elements -->
  {{foreach from=$prescription->_ref_lines_elt_for_plan key=name_chap item=elements_chap}}
    
    {{include file="../../dPprescription/templates/inc_header_plan_soin.tpl" name="CCategoryPrescription.chapitre.$name_chap" no_class=false}}
  
    {{foreach from=$elements_chap key=name_cat item=elements_cat}}
      {{assign var=categorie value=$categories.$name_chap.$name_cat}}
      {{foreach from=$elements_cat item=_element name="foreach_cat"}}
        {{foreach from=$_element key=unite_prise item=element name="foreach_elt"}}     
          {{include file="../../dPprescription/templates/inc_vw_line_plan_soin.tpl" line=$element suffixe=elt}}   
          {{if $smarty.foreach.foreach_elt.last && $smarty.foreach.foreach_cat.last}}
            <tr>
              <td colspan="1000" style="padding:0; height: 1px; border: 1px solid black;"></td>
            </tr>
          {{/if}} 
        {{/foreach}}
      {{/foreach}}
    {{/foreach}}
    {{include file="../../dPprescription/templates/inc_footer_plan_soin.tpl" no_class=false last_screen_footer=false}}     
  {{/foreach}}
  
  {{include file="../../dPprescription/templates/inc_footer_plan_soin.tpl" no_class=true last_screen_footer=true}}
</table>

<!-- Re-ouverture des tableaux -->
<table>
  <tr>
    <td>