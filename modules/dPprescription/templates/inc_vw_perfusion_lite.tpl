{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
{{assign var=perfusion_id value=$_perfusion->_id}}
<tr>
  <td style="width: 8%;" class="text {{if $_perfusion->_fin < $now && !$_perfusion->_protocole}}arretee{{/if}}">
			{{if $_perfusion->_ref_parent_line->_id}}
        {{assign var=parent_perf value=$_perfusion->_ref_parent_line}}
        <img src="images/icons/history.gif" alt="Ligne possédant un historique" title="Ligne possédant un historique" 
             class="tooltip-trigger" 
             onmouseover="ObjectTooltip.createEx(this, '{{$parent_perf->_guid}}')"/>
      {{/if}}
      <a href=# onmouseover="ObjectTooltip.createEx(this, '{{$_perfusion->_guid}}');">
        {{mb_value object=$_perfusion field=type}}
      </a>
  </td>
  <td style="width: 44%" class="text">
    {{foreach from=$_perfusion->_ref_lines item=_perf_line name=lines}}
      {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl" line=$_perf_line}}
      <a href="#produit{{$_perf_line->_id}}" onclick="Prescription.viewProduit(null,'{{$_perf_line->code_ucd}}','{{$_perf_line->code_cis}}');" style="font-weight: bold; display: inline;">
        {{$_perf_line->_ucd_view}}
        
        {{if $_perf_line->quantite}}
	      ({{mb_value object=$_perf_line field=quantite size=4}} {{mb_value object=$_perf_line field=unite size=4}})
		    {{/if}}
		    <span style="font-size: 0.8em; opacity: 0.7">
         ({{$_perf_line->_forme_galenique}})
        </span>
      </a>
      {{if !$smarty.foreach.lines.last}}<br />{{/if}}
    {{/foreach}}
  </td> 
  <td style="width: 8%" class="text">
     {{if !$_perfusion->_protocole}}
     <div class="mediuser" style="border-color: #{{$_perfusion->_ref_praticien->_ref_function->color}};">
       <label title="{{$_perfusion->_ref_praticien->_view}}">{{$_perfusion->_ref_praticien->_shortview}}</label>
       {{if $_perfusion->signature_prat}}
	  		 <img src="images/icons/tick.png" alt="Ligne signée par le praticien" title="Ligne signée par le praticien" />
		  	{{else}}
			  	 <img src="images/icons/cross.png" alt="Ligne non signée par le praticien"title="Ligne non signée par le praticien" />
			  {{/if}}
			  {{if $prescription_reelle->type != "externe"}}
				  {{if $_perfusion->signature_pharma}}
				    <img src="images/icons/signature_pharma.png" alt="Signée par le pharmacien" title="Signée par le pharmacien" />
				  {{else}}
					  <img src="images/icons/signature_pharma_barre.png" alt="Non signée par le pharmacien" title="Non signée par le pharmacien" />
			  	{{/if}}	
		  	{{/if}}
     </div>
		 {{else}}
		   -
		 {{/if}}
  </td>
  <td style="width: 5%;" class="text">{{mb_value object=$_perfusion field=vitesse}} ml/h</td>
  <td style="width: 15%;" class="text">{{mb_value object=$_perfusion field=voie}}</td>
	{{if !$_perfusion->_protocole}}
  <td style="width: 10%;" class="text">
	  {{mb_value object=$_perfusion field=date_debut}}
	  {{if $_perfusion->time_debut}} 
	    à {{mb_value object=$_perfusion field=time_debut}}
	  {{/if}}
  </td>
  <td style="width: 10%;" class="text">
    <button style="float: right;" class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, true, true,'{{$_perfusion->_guid}}');"></button>
    {{mb_value object=$_perfusion field=duree}} heures
  </td>  
	{{else}}
	<td style="width: 20%" class="text">
		<button style="float: right;" class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, true, true,'{{$_perfusion->_guid}}');"></button>
    {{if $_perfusion->decalage_interv}}
		A partir de I + {{mb_value object=$_perfusion field=decalage_interv}}
		{{/if}}
		{{if $_perfusion->duree}}
		pendant {{$_perfusion->duree}} heures
		{{/if}}
	</td>
	{{/if}} 
</tr>
</table>