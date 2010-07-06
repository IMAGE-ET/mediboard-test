{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{foreach from=$sejours item=_sejour}}
{{mb_include template=inc_sejour_draggable remplacement=0 sejour=$_sejour}}
{{foreachelse}}
<tr>
  <td >
    <em>{{tr}}CSejour.none{{/tr}}</em>
  </td>
</tr>
{{/foreach}}


{{if count($replacements)}}
<tr>
  <th>{{tr}}CReplacement{{/tr}}s</th>
</tr>
{{/if}}
{{foreach from=$replacements item=_replacement}}
<tr>
	<td>
    {{assign var=conge value=$_replacement->_ref_conge}}
    {{assign var=replaced  value=$conge->_ref_user}}
		<span onmouseover="ObjectTooltip.createEx(this, '{{$conge->_guid}}')">
			{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$replaced}}
		</span>
	</td>
</tr>

{{mb_include template=inc_sejour_draggable remplacement=1 sejour=$_replacement->_ref_sejour}}
{{/foreach}}
