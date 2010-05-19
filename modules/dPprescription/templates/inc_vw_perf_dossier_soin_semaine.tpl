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
  <td style="text-align: center">
   - 
  </td>
 	<td class="text">
 	  <div onclick='addCibleTransmission("CPrescriptionLineMix","{{$_prescription_line_mix->_id}}","{{$_prescription_line_mix->_view}}");' 
	       class="{{if @$transmissions.CPrescriptionLineMix.$prescription_line_mix_id|@count}}transmission{{else}}transmission_possible{{/if}}">
	    <a href="#{{$_prescription_line_mix->_guid}}" onmouseover="ObjectTooltip.createEx(this, '{{$_prescription_line_mix->_guid}}')">
	      {{$_prescription_line_mix}} 
	    </a>
	  </div>
	  </div>
	</td>
 	<td class="text" style="font-size: 1em;">
 	  <ul>
 	   {{foreach from=$_prescription_line_mix->_ref_lines item=_line}}
 	     <li>{{$_line->_view}}</li>
 	   {{/foreach}}
 	  </ul>
 	</td>
	
	{{if !$_prescription_line_mix->signature_prat && $dPconfig.dPprescription.CPrescription.show_unsigned_med_msg}}
	  <td colspan="5">
	  	<div class="small-warning">Ligne non signée</div>
	  </td>
	{{else}}
	  {{foreach from=$dates item=date name="foreach_date"}}
	    <td style="{{if $date < $_prescription_line_mix->_debut|date_format:'%Y-%m-%d' ||  $date > $_prescription_line_mix->_fin|date_format:'%Y-%m-%d'}}background-color: #ddd;{{/if}} text-align: center"> 		          
	    </td>
	  {{/foreach}}
	{{/if}}
	
	 <td style="text-align: center">
	   <div class="mediuser" style="border-color: #{{$_prescription_line_mix->_ref_praticien->_ref_function->color}}">
	     {{if $_prescription_line_mix->signature_prat}}
	     <img src="images/icons/tick.png" title="Signée le {{$_prescription_line_mix->_ref_log_signature_prat->date|date_format:$dPconfig.datetime}} par {{$_prescription_line_mix->_ref_praticien->_view}}" />
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