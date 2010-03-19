{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=perfusion_id value=$_perfusion->_id}}
<tr>
  <td style="text-align: center">
   - 
  </td>
 	<td class="text">
 	  <div onclick='addCibleTransmission("CPerfusion","{{$_perfusion->_id}}","{{$_perfusion->_view}}");' 
	       class="{{if @$transmissions.CPerfusion.$perfusion_id|@count}}transmission{{else}}transmission_possible{{/if}}">
	    <a href="#{{$_perfusion->_guid}}" onmouseover="ObjectTooltip.createEx(this, '{{$_perfusion->_guid}}')">
	      {{$_perfusion}} 
	    </a>
	  </div>
	  </div>
	</td>
 	<td class="text" style="font-size: 1em;">
 	  <ul>
 	   {{foreach from=$_perfusion->_ref_lines item=_line}}
 	     <li>{{$_line->_view}}</li>
 	   {{/foreach}}
 	  </ul>
 	</td>
	
	{{if !$_perfusion->signature_prat && $dPconfig.dPprescription.CPrescription.show_unsigned_med_msg}}
	  <td colspan="5">
	  	<div class="small-warning">Ligne non signée</div>
	  </td>
	{{else}}
	  {{foreach from=$dates item=date name="foreach_date"}}
	    <td style="{{if $date < $_perfusion->_debut|date_format:'%Y-%m-%d' ||  $date > $_perfusion->_fin|date_format:'%Y-%m-%d'}}background-color: #ddd;{{/if}} text-align: center"> 		          
	    </td>
	  {{/foreach}}
	{{/if}}
	
	 <td style="text-align: center">
	   <div class="mediuser" style="border-color: #{{$_perfusion->_ref_praticien->_ref_function->color}}">
	     {{if $_perfusion->signature_prat}}
	     <img src="images/icons/tick.png" title="Signée le {{$_perfusion->_ref_log_signature_prat->date|date_format:$dPconfig.datetime}} par {{$_perfusion->_ref_praticien->_view}}" />
	     {{else}}
	     <img src="images/icons/cross.png" title="Non signée par le praticien" />
	     {{/if}}
	   </div>
	 </td>
	 <td style="text-align: center">
	   {{if $_perfusion->signature_pharma}}
	   <img src="images/icons/tick.png" title="Signée par le pharmacien" />
	   {{else}}
	   <img src="images/icons/cross.png" title="Non signée par le pharmacien" />
	   {{/if}}
	 </td>
 
</tr>