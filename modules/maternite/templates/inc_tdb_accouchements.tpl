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
        <label style="float: left;">
          <input type="checkbox" {{if $see_finished}}checked="checked"{{/if}} name="see_finished" value="1" onchange="Tdb.views.toggleFinished();" />
          Voir terminés
        </label>
        <button type="button" class="change notext" onclick="Tdb.views.listAccouchements();" style="float: right;">
          {{tr}}Refresh{{/tr}}
        </button>
        <a onclick="zoomViewport(this);">{{$ops|@count}} accouchements en cours au {{$date|date_format:$conf.date}}</a>
      </th>
    </tr>
    <tr>
      <th>{{tr}}CPatient{{/tr}}e</th>
      <th>Prat / anesth</th>
      <th class="narrow">{{mb_title class=COperation field=_datetime}}</th>
      <th class="narrow">{{tr}}CSalle{{/tr}}</th>
      <th>Tavail</th>
      <th>Op</th>
      <th class="narrow"></th>
    </tr>
  </thead>
  <tbody id="tbody_accouchements_tab">
    {{foreach from=$ops item=_op}}
      {{assign var=_grossesse value=$_op->_ref_sejour->_ref_grossesse}}

      <tr {{if $_grossesse->datetime_accouchement}}class="opacity-50"{{/if}}>
        <td class="text">
          <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$_grossesse->_ref_parturiente->_guid}}');">{{$_grossesse->_ref_parturiente}}</span><br/>
          <span class="compact" onmouseover="ObjectTooltip.createEx(this, '{{$_op->_ref_sejour->_ref_curr_affectation->_guid}}');">{{$_op->_ref_sejour->_ref_curr_affectation}}</span>
        </td>
        <td>
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_op->_ref_chir}}<br/>
          <select name="anesth_id" onchange="Tdb.changeAnesthFor('{{$_op->_id}}', $V(this));" style="width:6em;">
            <option value="">&mdash;</option>
            {{foreach from=$anesths item=_anesth}}
              <option value="{{$_anesth->_id}}" {{if $_op->anesth_id == $_anesth->_id}}selected="selected" {{/if}}>{{$_anesth}}</option>
            {{/foreach}}
          </select>
        </td>
        <td>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_op->_guid}}');">
            {{if $_op->date != $date}}{{mb_value object=$_op field=date}}<br/>{{/if}}
            {{$_op->_datetime|date_format:$conf.time}}
          </span>
        </td>
        <td>
            {{if $_op->_ref_salle->_id && !$_op->_ref_salle->_id|@in_array:$salles }}
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_op->_ref_salle->_guid}}');">{{$_op->_ref_salle}}</span>
          {{else}}
              <select name="salle_id" style="width:6em;" onchange="Tdb.changeSalleFor('{{$_op->_id}}', $V(this));">
                <option value="">&mdash;</option>
                {{foreach from=$blocs item=_bloc}}
                  <optgroup label="{{$_bloc}}">
                  {{foreach from=$_bloc->_ref_salles item=_salle}}
                    <option value="{{$_salle->_id}}" {{if $_op->_ref_salle->_id == $_salle->_id}}selected="selected" {{/if}}>{{$_salle->nom}}</option>
                  {{/foreach}}
                  </optgroup>
                {{/foreach}}
              </select>
            {{/if}}
        </td>
        <td>
          {{if $_grossesse->datetime_debut_travail}}
            Deb. {{mb_value object=$_grossesse field=datetime_debut_travail}}
          {{/if}}
          {{if $_grossesse->datetime_accouchement}}
            <br/>Fin {{mb_value object=$_grossesse field=datetime_accouchement}}
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
          <button class="edit notext" onclick="Tdb.editAccouchement('{{$_op->_id}}');"></button>
          <button onclick="Tdb.dossierAccouchement('{{$_op->_id}}');">Acc</button>

        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="8" class="empty">{{tr}}COperation.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </tbody>
</table>