{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{mb_include module=system template=CMbObject_view}}

<table class="tbl">
  {{assign var=allaitement value=$object->_ref_last_allaitement}}
  {{if $allaitement->_id}}
    <tr>
      <th class="category">Allaitement</th>
    </tr>
    <tr>
      <td>
        <strong>Date de début :</strong> {{mb_value object=$allaitement field=date_debut}}
      </td>
    </tr>
    {{if $allaitement->date_fin}}
      <tr>
        <td>
          <strong>Date de fin :</strong> {{mb_value object=$allaitement field=date_fin}}
        </td>
      </tr>
    {{/if}}
  {{/if}}
  <tr>
    <th class="category">Naissances</th>
  </tr>
  {{foreach from=$object->_ref_naissances item=_naissance}}
    {{assign var=sejour value=$_naissance->_ref_sejour_enfant}}
    {{assign var=patient value=$sejour->_ref_patient}}
    <tr>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
          {{$patient}}
        </span> né(e) le
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_naissance->_guid}}')">
          {{mb_value object=$patient field=naissance}}
        </span>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
         &mdash; ( {{$sejour->_view}} )
        </span>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty">
        {{tr}}CNaissance.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>