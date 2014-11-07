{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script>
  Main.add(function() {
    Tdb.views.filterByText('accouchements_tab');
  });
</script>

<table class="tbl" id="accouchements_tab">
  <thead>
    <tr>
      <th class="title" colspan="10">
        <button type="button" class="accouchement_create notext" onclick="Tdb.editAccouchement(null);" style="float: left;">
          {{tr}}CNaissance-title-create{{/tr}}
        </button>
        <button type="button" class="change notext" onclick="Tdb.views.listAccouchements();" style="float: right;">
          {{tr}}Refresh{{/tr}}
        </button>
        {{$ops|@count}} accouchements en cours au {{$date|date_format:$conf.date}}</th>
    </tr>
    <tr>
      <th>{{tr}}CPatient{{/tr}}e</th>
      <th>Prat / anesth</th>
      <th class="narrow">{{mb_title class=COperation field=_datetime}}</th>
      <th>{{tr}}CSalle{{/tr}}</th>
      <th>Tavail</th>
      <th>{{tr}}CIntervHorsPlage{{/tr}}</th>
      <th>Rques</th>
      <th class="narrow"></th>
    </tr>
  </thead>
  <tbody id="tbody_accouchements_tab">
    {{foreach from=$ops item=_op}}
      <tr>
        <td>
          <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$_op->_ref_sejour->_ref_grossesse->_ref_parturiente->_guid}}');">{{$_op->_ref_sejour->_ref_grossesse->_ref_parturiente}}</span>
        </td>
        <td>
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_op->_ref_chir}}
          {{if $_op->anesth_id}} <img src="" alt="/" />{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_op->_ref_anesth}}{{/if}}
        </td>
        <td>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_op->_guid}}');">
            {{if $_op->date != $date}}{{mb_value object=$_op field=date}}{{/if}}
            {{$_op->_datetime|date_format:$conf.time}}
          </span>
        </td>
        <td>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_op->_ref_salle->_guid}}');">
            {{$_op->_ref_salle}}
          </span>
        </td>
        <td>
          {{if $_op->_ref_sejour->_ref_grossesse->datetime_debut_travail}}
            {{mb_value object=$_op->_ref_sejour->_ref_grossesse field=datetime_debut_travail}}
          {{/if}}
          {{if $_op->_ref_sejour->_ref_grossesse->datetime_accouchement}}
           - {{mb_value object=$_op->_ref_sejour->_ref_grossesse field=datetime_accouchement}}
          {{/if}}
        </td>
        <td class="text compact">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_op->_guid}}');">
          {{if $_op->libelle}}
            <em>[{{$_op->libelle}}]</em><br />
          {{/if}}
            {{foreach from=$_op->_ext_codes_ccam item=_code}}
              <strong>{{$_code->code}}</strong> : {{$_code->libelleLong}}<br />
            {{/foreach}}
          </span>
        </td>
        <td>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_op->_ref_sejour->_ref_grossesse->_guid}}');">
            {{$_op->_ref_sejour->_ref_grossesse->rques}}
          </span>
        </td>
        <td>
          <button class="edit notext" onclick="Tdb.editAccouchement('{{$_op->_id}}');"></button>
        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="8" class="empty">{{tr}}COperation.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </tbody>
</table>