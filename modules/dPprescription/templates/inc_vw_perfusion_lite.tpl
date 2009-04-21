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
  <td style="width: 13%;" {{if $_perfusion->_fin < $now && !$_perfusion->_protocole}}class="arretee"{{/if}}>
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
  <td style="width: 7%;">
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
  </td>
  <td style="width: 7%;">{{mb_value object=$_perfusion field=vitesse}} ml/h</td>
  <td style="width: 15%;">{{mb_value object=$_perfusion field=voie}}</td>
  <td style="width: 10%;">{{mb_value object=$_perfusion field=date_debut}}</td>
  <td style="width: 8%;">{{mb_value object=$_perfusion field=duree}} heures</td>
  <td style="width: 50%;" class="text">
    <button style="float: right;" class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, true, true,'{{$_perfusion->_guid}}');"></button>
      
    {{foreach from=$_perfusion->_ref_lines item=_perf_line name=lines}}
      {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl" line=$_perf_line}}
      {{$_perf_line->_ucd_view}}
      {{if $_perf_line->quantite}}
      ({{mb_value object=$_perf_line field=quantite size=4}}{{mb_value object=$_perf_line field=unite size=4}}
	     {{if $_perf_line->nb_tous_les}}
	       toutes les {{$_perf_line->nb_tous_les}} heures
	    {{/if}})
	     {{/if}}{{if !$smarty.foreach.lines.last}},{{/if}}
    {{/foreach}}
  </td>    
</tr>
</table>