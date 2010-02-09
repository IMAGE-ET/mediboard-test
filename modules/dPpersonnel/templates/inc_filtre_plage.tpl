{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
<form action="?" name="recherche" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <table class="form">
    <tr>
      <th  colspan="4" class="title">{{tr}}CPlageVacances-user-search{{/tr}}</th>
    </tr>
    <tr>
      <td>{{mb_label object=$filter field="user_id"}}</td>
      <td colspan="3">
      	<select name="user_id">
      		<option value="">{{tr}}CMediusers.all{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$mediusers selected=$filter->user_id}}
				</select>
      </td>
    </tr>
    <tr>
      <td>{{mb_label object=$filter field="date_debut"}}</td>
      <td>{{mb_field object=$filter field="date_debut" form="recherche" register="true"}}</td>
      <td>{{mb_label object=$filter field="date_fin"}}</td>
      <td>{{mb_field object=$filter field="date_fin" form="recherche" register="true"}}</td>
    </tr>
    <tr>
      <td colspan="4" style="text-align: center">
        <button type="submit" class="search">
          {{tr}}Filter{{/tr}}
        </button>
				<button type="button" onclick = "raz(this.form)" class="cancel">
          {{tr}}Reset{{/tr}}
        </button>
      </td>
    </tr>
  </table>
</form>