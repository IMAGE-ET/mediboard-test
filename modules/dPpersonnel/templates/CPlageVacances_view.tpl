{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
{{include file=CMbObject_view.tpl}}
{{assign var=plage value=$object}}
{{if $can->edit}}
<table class="tbl">
	<tr>
		<td class="button">
			<form name="editplage" action="?">
				<input type="hidden" name="m" value="dPpersonnel"/>
				<input type="hidden" name="tab" value="vw_idx_plages_vac"/>
			  <input type="hidden" name="plage_id" value="{{$plage->_id}}"/">
				<input type="hidden" name="user_id" value="{{$plage->user_id}}"/">
				<button type="submit" class="edit">
	        {{tr}}Modify{{/tr}}
	      </button>
			</form>
    </td>
  </tr>
</table>
{{/if}}