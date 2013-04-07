{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{mb_include template=CMbObject_view}}

{{assign var=element_to_csarr value=$object}}
{{assign var=activite         value=$element_to_csarr->_ref_activite_csarr}}
{{assign var=hierarchie       value=$activite->_ref_hierarchie}}

<table class="tooltip tbl">
  <tr>
  	<td class="text">
  		{{mb_include module=system template=inc_field_view object=$activite  prop=libelle}}
      <strong>
        {{mb_label object=$activite field=hierarchie}}
        {{mb_value object=$activite field=hierarchie}}
      </strong>:
      {{mb_value object=$hierarchie field=libelle}}
		</td>
	</tr>
	<tr>
	  <td class="button">
	    {{mb_script module=ssr script=csarr ajax=1}}
	    <button class="search" onclick="CsARR.viewActivite('{{$activite->code}}')">Détails sur le code</button>
	  </td>
	</tr>
</table>
