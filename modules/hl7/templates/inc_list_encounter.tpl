{{*
 * $Id$
 *
 * @category test
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  Patselect = function(id) {
    new Url("hl7", "ajax_view_pat")
      .addParam("pat_id", id)
      .requestModal();
  }
</script>

{{mb_include module=system template=inc_pagination total=$nb_pat current=$page change_page="TestHL7.changePageListEncounterSupplier" step=30}}

<table class="tbl">
  <tr>
    <th style="width: 20%;">{{mb_title object=$patient field=nom}}</th>
    <th style="width: 20%;">{{mb_title object=$patient field=prenom}}</th>
    <th style="width: 20%;">{{mb_title object=$patient field=nom_jeune_fille}}</th>
    <th style="width: 10%;">{{mb_title object=$patient field=sexe}}</th>
    <th>{{mb_title object=$patient field=pays_insee}}</th>
    <th>Actions</th>
  </tr>

  {{foreach from=$patients item=_patient}}
    <tr>
      <td>
        <span  onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}')">
          {{mb_value object=$_patient field=nom}}
        </span>
      </td>
      <td>{{mb_value object=$_patient field=prenom}}</td>
      <td>{{mb_value object=$_patient field=nom_jeune_fille}}</td>
      <td>{{mb_value object=$_patient field=sexe}}</td>
      <td>{{mb_value object=$_patient field=pays}}</td>
      <td>
        <button type="button" class="compact change notext" onclick="Patselect('{{$_patient->_id}}')">
          {{tr}}Select{{/tr}}
        </button>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="6" class="empty">{{tr}}CPatient.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>