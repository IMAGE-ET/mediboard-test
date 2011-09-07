{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=prescription_line_mix_id value=$_prescription_line_mix->_id}}
<tr>
	{{if $conf.dPprescription.CPrescription.show_categories_plan_soins}}
  <td style="text-align: center">
   - 
  </td>
	{{/if}}
 	<td class="text">
 	  <div style="cursor: pointer; padding: 2px; font-weight: bold;"
				 onclick="addCibleTransmission('{{$_prescription_line_mix->_ref_prescription->object_id}}', 'CPrescriptionLineMix', '{{$_prescription_line_mix->_id}}')" 
	       class="{{if @$transmissions.CPrescriptionLineMix.$prescription_line_mix_id|@count}}transmission{{else}}transmission_possible{{/if}}"
				 onmouseover="ObjectTooltip.createEx(this, '{{$_prescription_line_mix->_guid}}')">

			 {{tr}}CPrescriptionLineMix.type.{{$_prescription_line_mix->type}}{{/tr}} 
    {{if $_prescription_line_mix->voie}}
      <div style="white-space: nowrap;">[{{$_prescription_line_mix->voie}}]</div>
    {{/if}}
    {{if $_prescription_line_mix->interface}}
          <div style="white-space: nowrap;">[{{tr}}CPrescriptionLineMix.interface.{{$_prescription_line_mix->interface}}{{/tr}}]</div>
    {{/if}}
		
	  </div>
	  </div>
	</td>

	<td style="width: 200px;" class="text compact">
	   {{foreach from=$_prescription_line_mix->_ref_lines item=_line}}
	     <div style="margin: 5px 0;">
	       <strong>{{$_line->_ucd_view}}</strong>
	       <div>
	         {{$_line->_posologie}}
	         {{if $_line->_unite_administration && $_line->_unite_administration != "ml"}}
	           [{{$_line->_unite_administration}}]
	         {{/if}}
	       </div>
	     </div>      
	   {{/foreach}}
	
	  <hr style="width: 70%; border-color: #aaa; margin: 1px auto;">
	  <div style="white-space: nowrap;">
	  {{if $_prescription_line_mix->_frequence}}
	    {{if $_prescription_line_mix->type_line == "perfusion"}}Débit initial: {{/if}}
	    {{$_prescription_line_mix->_frequence}}
	    {{if $_prescription_line_mix->volume_debit && $_prescription_line_mix->duree_debit}}
	      <br />
	      ({{mb_value object=$_prescription_line_mix field=volume_debit}} ml en {{mb_value object=$_prescription_line_mix field=duree_debit}} h)
	    {{/if}}
	  {{/if}}
	  </div> 
	</td>

	{{if !$_prescription_line_mix->signature_prat && $conf.dPprescription.CPrescription.show_unsigned_med_msg}}
	  <td colspan="7">
	  	<div class="small-warning">Ligne non signée</div>
	  </td>
	{{else}}
	  {{foreach from=$dates item=date name="foreach_date"}}
	    <td style="{{if $date < $_prescription_line_mix->_debut|iso_date ||  $date > $_prescription_line_mix->_fin|iso_date}}background-color: #ddd;{{/if}} text-align: center"> 		          
	    </td>
	  {{/foreach}}
	{{/if}}
	
	 <td style="text-align: center">
	   <div class="mediuser" style="border-color: #{{$_prescription_line_mix->_ref_praticien->_ref_function->color}}">
	     {{if $_prescription_line_mix->signature_prat}}
	     <img src="images/icons/tick.png" title="Signée le {{$_prescription_line_mix->_ref_log_signature_prat->date|date_format:$conf.datetime}} par {{$_prescription_line_mix->_ref_praticien->_view}}" />
	     {{else}}
	     <img src="images/icons/cross.png" title="Non signée par le praticien" />
	     {{/if}}
	   </div>
	 </td>
	 <td style="text-align: center">
	   {{if $_prescription_line_mix->signature_pharma}}
	   <img src="images/icons/tick.png" title="Signée par le pharmacien" />
	   {{else}}
	   <img src="images/icons/cross.png" title="Non signée par le pharmacien" />
	   {{/if}}
	 </td>
 
</tr>