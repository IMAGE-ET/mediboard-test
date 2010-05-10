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

{{foreach from=$remplacements item=_remplacement}}
<tr>
	<td style="text-align: center;">
		<strong onmouseover="ObjectTooltip.createEx(this, '{{$_remplacement->_guid}}')">
			Remplacement de '{{mb_value object=$_remplacement field=user_id}}'
		</strong>
	</td>
</tr>

{{foreach from=$_remplacement->_refs_sejours_remplaces item=_sejour}}
{{mb_include template=inc_sejour_draggable remplacement=1 sejour=$_sejour}}
{{foreachelse}}
<tr>
  <td >
    <em>{{tr}}CSejour.none{{/tr}}</em>
  </td>
</tr>
{{/foreach}}

{{/foreach}}