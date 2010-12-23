{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
{{assign var=prescription_line_mix_id value=$_prescription_line_mix->_id}}
<tr {{if $_prescription_line_mix->_fin < $now && !$_prescription_line_mix->_protocole}}class="hatching_red"{{/if}}>
  <td style="width: 5%;" class="text {{if $_prescription_line_mix->perop}}perop{{/if}}">
			     
    {{if $_prescription_line_mix->_can_delete_prescription_line_mix}}
     <form name="editPerf-{{$_prescription_line_mix->_id}}" action="" method="post">
          <input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
          <input type="hidden" name="prescription_line_mix_id" value="{{$_prescription_line_mix->_id}}" />
          <input type="hidden" name="del" value="1" />
          <button type="button" class="trash notext" onclick="return onSubmitFormAjax(this.form, { 
            onComplete: function(){
                Prescription.reloadPrescPerf('{{$_prescription_line_mix->prescription_id}}','{{$_prescription_line_mix->_protocole}}','{{$mode_pharma}}');
            }        
          } );"></button>
        </form>
      {{/if}}
							
			{{if $_prescription_line_mix->_ref_parent_line->_id}}
        {{assign var=parent_perf value=$_prescription_line_mix->_ref_parent_line}}
        <img src="images/icons/history.gif" title="Ligne possédant un historique" 
             onmouseover="ObjectTooltip.createEx(this, '{{$parent_perf->_guid}}')" />
      {{/if}}

  </td>
  <td style="width: 47%" class="text">
    {{foreach from=$_prescription_line_mix->_ref_lines item=_perf_line name=lines}}
      {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl" line=$_perf_line}}
	    {{if $_perf_line->_can_vw_livret_therapeutique}}
	      <img src="images/icons/livret_therapeutique_barre.gif" title="Produit non présent dans le livret Thérapeutique" />
	    {{/if}} 
	    {{if $_perf_line->stupefiant}}
	      <img src="images/icons/stup.png" title="Produit stupéfiant" />
	    {{/if}} 
	    {{if !$_perf_line->_ref_produit->inT2A}}
	      <img src="images/icons/T2A_barre.gif" title="Produit hors T2A" />
	    {{/if}}
	    {{if $_perf_line->_can_vw_generique}}
	      <img src="images/icons/generiques.gif" title="Produit générique" />
	    {{/if}} 
	    {{if $_perf_line->_ref_produit->_supprime}}
	      <img src="images/icons/medicament_barre.gif" title="Produit supprimé" />
	    {{/if}}
			
      <a href="#produit{{$_perf_line->_id}}" onclick="Prescription.viewProduit(null,'{{$_perf_line->code_ucd}}','{{$_perf_line->code_cis}}');" style="font-weight: bold; display: inline;">
        {{$_perf_line->_ucd_view}}
        
				{{if $_perf_line->_posologie}}
				: <span	{{if $_prescription_line_mix->_fin < $now && !$_prescription_line_mix->_protocole}}style="text-decoration:line-through"{{/if}}>
				{{$_perf_line->_posologie}}</span>
				
				{{/if}}
				 
      </a>
      {{if !$smarty.foreach.lines.last}}<br />{{/if}}
    {{/foreach}}
  </td> 
  <td style="width: 8%" class="text">
     {{if !$_prescription_line_mix->_protocole}}
     <div class="mediuser" style="border-color: #{{$_prescription_line_mix->_ref_praticien->_ref_function->color}};">
       <label title="{{$_prescription_line_mix->_ref_praticien->_view}}">{{$_prescription_line_mix->_ref_praticien->_shortview}}</label>
        {{if $_prescription_line_mix->signature_prat}}
	  		  <img src="images/icons/tick.png" title="Ligne signée par le praticien" />
		  	{{else}}
			    <img src="images/icons/cross.png" title="Ligne non signée par le praticien" />
			  {{/if}}
			  {{if $prescription->type != "externe"}}
				  {{if $_prescription_line_mix->signature_pharma}}
				    <img src="images/icons/signature_pharma.png" title="Signée par le pharmacien" />
				  {{else}}
					  <img src="images/icons/signature_pharma_barre.png" title="Non signée par le pharmacien" />
			  	{{/if}}	
		  	{{/if}}
     </div>
		 {{else}}
		   -
		 {{/if}}
  </td>
  
	{{if $_prescription_line_mix->type_line == "aerosol"}}
	  <td style="width: 20%">
	  	{{if $_prescription_line_mix->interface}}
	  	  {{tr}}CPrescriptionLineMix.interface.{{$_prescription_line_mix->interface}}{{/tr}}
			{{/if}}
	  </td>
	{{else}}
		<td style="width: 5%;" class="text">
		{{$_prescription_line_mix->_frequence}}
		</td>
	  <td style="width: 15%;" class="text">
		  <a href=# onmouseover="ObjectTooltip.createEx(this, '{{$_prescription_line_mix->_guid}}');" style="display: inline; font-weight: bold;">
        {{mb_value object=$_prescription_line_mix field=type}}
      </a>
		  <br />
			{{mb_value object=$_prescription_line_mix field=voie}}
		</td>
	{{/if}}
	{{if !$_prescription_line_mix->_protocole}}
  <td style="width: 10%;" class="text">
	  {{mb_value object=$_prescription_line_mix field=date_debut}}
	  {{if $_prescription_line_mix->time_debut}} 
	    à {{mb_value object=$_prescription_line_mix field=time_debut}}
	  {{/if}}
  </td>
  <td style="width: 10%;" class="text">
    <button style="float: right;" class="edit notext" onclick="Prescription.reloadLine('{{$_prescription_line_mix->_guid}}','{{$mode_protocole}}','{{$mode_pharma}}','{{$operation_id}}');"></button>
    {{if $_prescription_line_mix->duree}}
		  {{mb_value object=$_prescription_line_mix field=duree}} {{mb_value object=$_prescription_line_mix field="unite_duree"}}
		{{/if}}
  </td>  
	{{else}}
	<td style="width: 20%" class="text">
		<button style="float: right;" class="edit notext" onclick="Prescription.reloadLine('{{$_prescription_line_mix->_guid}}','{{$_prescription_line_mix->_protocole}}','{{$mode_pharma}}','{{$operation_id}}','{{$mode_substitution}}');"></button>
    {{if $_prescription_line_mix->decalage_interv}}
		A partir de I+{{mb_value object=$_prescription_line_mix field=decalage_interv}}h
		{{/if}}
		{{if $_prescription_line_mix->duree}}
		pendant {{$_prescription_line_mix->duree}} {{mb_value object=$_prescription_line_mix field="unite_duree"}}
		{{/if}}
	</td>
	{{/if}} 
</tr>
</table>