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
	<td class="text {{$ssr_class}}" style="border: 1px solid #aaa; border-width: 1px 0px; line-height: 110%;">
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