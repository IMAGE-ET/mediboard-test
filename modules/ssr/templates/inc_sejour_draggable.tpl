{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=ssr_class value=""}}
{{if $sejour->annule == "1"}}
{{assign var=ssr_class value=ssr_annule}}
{{elseif !$sejour->entree_reelle}}
{{assign var=ssr_class value=ssr_non_debute}}
{{elseif $sejour->sortie_reelle}}
{{assign var=ssr_class value=ssr_fini}}
{{/if}}
<tr>
	<td class="text {{$ssr_class}}" style="border: 1px solid #aaa; border-width: 1px 0px;">
                   
		<div class="draggable" id="{{$sejour->_guid}}">
		<script type="text/javascript">Repartition.draggableSejour('{{$sejour->_guid}}')</script>
		
		{{assign var=bilan value=$sejour->_ref_bilan_ssr}}
		{{assign var=prescription value=$sejour->_ref_prescription_sejour}}
    
		<div style="float: right; text-align: right;">
			{{if $bilan->_id}} 
			  <span onmouseover="ObjectTooltip.createEx(this, '{{$bilan->_guid}}')">
				  Bilan
				</span> 
			{{/if}}
			
		  {{if $prescription->_id}}
				{{if $bilan->_id}}
				<br />
				{{/if}}
				<span onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')">
	        Presc.
	      </span> 
			{{/if}}
    </div>
		
		{{assign var=patient value=$sejour->_ref_patient}}
		<span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
		  {{$patient}}        
		</span> 
		
		<br/> 
		<span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
			<strong>pour {{$sejour->_sortie_relative}}j</strong> 
		  (arrivée {{$sejour->_entree_relative}}j)
		</span>
		</div>
  </td>
</tr>