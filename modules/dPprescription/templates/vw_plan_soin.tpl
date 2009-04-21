{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

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

{{if $patient->_id}}
<form name="selChapitre" method="get" action="" class="not-printable">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="a" value="vw_plan_soin_pdf" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
  <input type="hidden" name="dialog" value="1" />		    
	<table class="form">
	  <tr>
	    <th class="category" colspan="4">Critères de calcul de la feuille de soin</th>
	  </tr>
	  <tr>
	    <th>
	      Sélection du chapitre
	    </th>
	    <td>
			  <select name="chapitre" class="select-tree">
			    <option value="">Tous les chapitres</option>
			    <optgroup label="Médicaments">
			      <option value="all_med" {{if $chapitre == "all_med"}}selected="selected"{{/if}}>Tous les médicaments</option>
						<option value="med" {{if $chapitre == "med"}}selected="selected"{{/if}}>Médicaments</option>
						<option value="inj" {{if $chapitre == "inj"}}selected="selected"{{/if}}>Injections</option>
						<option value="perf" {{if $chapitre == "perf"}}selected="selected"{{/if}}>Perfusions</option>
			    </optgroup>
				  <optgroup label="Elements">
			    {{assign var=specs_chapitre_elt value=$_category->_specs.chapitre}}
				  {{foreach from=$specs_chapitre_elt->_list item=_chapitre_elt}}
				    <option value="{{$_chapitre_elt}}" {{if $chapitre == $_chapitre_elt}}selected="selected"{{/if}}>{{$_chapitre_elt}}</option>
			    {{/foreach}}
				  </optgroup>
				</select>
	    </td>
	    <th>
	      Date d'affichage
	    </th>
	    <td class="date">
	      {{mb_field object=$prescription field=_date_plan_soin form="selChapitre" register=true}}
	    </td>
		</tr>
		<tr>
		  <td colspan="4" style="text-align: center;">
				<button class="tick" type="button" onclick="this.form.submit();">Générer la feuille de soin</button>
		  </td>
		</tr>
		<tr>
		  <th class="title" colspan="4">
		    Feuille de soin
		  </th>
		</tr>
	</table>
</form>
{{/if}}

<table style="border-collapse: collapse; border: 1px solid #ccc" class="tbl">

	{{if $prescription->_ref_lines_med_for_plan || $prescription->_ref_injections_for_plan || $prescription->_ref_perfusions_for_plan || $chapitre == ""}}
    {{include file="../../dPprescription/templates/inc_header_plan_soin.tpl" name="Médicaments" no_class=true}}
  {{/if}}
  
  <!-- Affichage des medicaments -->
  {{if $chapitre != "inj"}}
  {{foreach from=$prescription->_ref_lines_med_for_plan item=_all_lines_unite_prise_cat}}
    {{foreach from=$_all_lines_unite_prise_cat item=_all_lines_unite_prise}}
      {{foreach from=$_all_lines_unite_prise key=unite_prise item=_line}}
        {{include file="../../dPprescription/templates/inc_vw_line_plan_soin.tpl" line=$_line suffixe=med}}
    {{/foreach}}
   {{/foreach}} 
  {{/foreach}}
  {{/if}}

  <!-- Affichage des injections -->
  {{if $chapitre != "med"}}
	  {{foreach from=$prescription->_ref_injections_for_plan item=_all_lines_unite_prise_cat_inj}}
	    {{foreach from=$_all_lines_unite_prise_cat_inj item=_all_lines_unite_prise_inj}}
	      {{foreach from=$_all_lines_unite_prise_inj key=unite_prise item=_line_inj}}
	        {{include file="../../dPprescription/templates/inc_vw_line_plan_soin.tpl" line=$_line_inj suffixe=med}}
	    {{/foreach}}
	   {{/foreach}} 
	  {{/foreach}}
  {{/if}}
  <!-- Affichage des perfusions -->
  {{foreach from=$prescription->_ref_perfusions_for_plan item=_perfusion}}
    {{include file="../../dPprescription/templates/inc_vw_perf_plan_soin.tpl"}}
  {{/foreach}}
  
  {{if $prescription->_ref_lines_med_for_plan || $prescription->_ref_injections_for_plan || $prescription->_ref_perfusions_for_plan}}
    {{include file="../../dPprescription/templates/inc_footer_plan_soin.tpl" no_class=false last_screen_footer=false}}
  {{/if}}
  
  <!-- Séparation entre les medicaments et les elements -->
  <tr>
    <td colspan="1000" style="padding:0; height: 1px; border: 1px solid black;"></td>
  </tr>
   
   
  <!-- Affichage des elements -->
  {{foreach from=$prescription->_ref_lines_elt_for_plan key=name_chap item=elements_chap}}  
    {{if $chapitre}}
      {{include file="../../dPprescription/templates/inc_header_plan_soin.tpl" name="CCategoryPrescription.chapitre.$name_chap" no_class=true}}
    {{else}}
      {{include file="../../dPprescription/templates/inc_header_plan_soin.tpl" name="CCategoryPrescription.chapitre.$name_chap" no_class=false}}
    {{/if}}
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