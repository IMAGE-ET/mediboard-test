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
{{assign var=ssr_class value=ssr-annule}}
{{elseif !$sejour->entree_reelle}}
{{assign var=ssr_class value=ssr-prevu}}
{{elseif $sejour->sortie_reelle}}
{{assign var=ssr_class value=ssr-termine}}
{{/if}}
<tr>
	<td class="text {{$ssr_class}}" style="border: 1px solid #aaa; border-width: 1px 0px; line-height: 120%;">
		<div class="draggable" id="{{$sejour->_guid}}">
			<script type="text/javascript">Repartition.draggableSejour('{{$sejour->_guid}}')</script>
			
			{{assign var=patient value=$sejour->_ref_patient}}
			<span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
			  {{$patient}}        
			</span> 
			
			<div style="text-indent: 1em; opacity: 0.8;">
				{{mb_value object=$sejour field=libelle}}
			</div>
			 
		</div>
  </td>
</tr>