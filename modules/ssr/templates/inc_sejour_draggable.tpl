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
<tr class="{{$ssr_class}}">
  {{assign var=replacement value=$sejour->_ref_replacement}}
  
	<td class="text ssr-repartition {{if $replacement && $replacement->_id}} arretee {{/if}}">
    {{if $remplacement}} 
    <div>
      {{mb_include template=inc_sejour_repartition}}
    </div>
    {{else}}
	    <div class="draggable" id="{{$sejour->_guid}}">
	      <script type="text/javascript">Repartition.draggableSejour('{{$sejour->_guid}}')</script>
	      {{mb_include template=inc_sejour_repartition}}       
	    </div>
    {{/if}}
  </td>
</tr>