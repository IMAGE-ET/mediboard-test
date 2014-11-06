{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<table class="tbl" id="accouchements_tab">
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
    <th>Patiente</th>
    <th>Prat / anesth</th>
    <th class="narrow">Horaire</th>
    <th>Salle</th>
    <th>Déb. Trav.</th>
    <th>Libellé</th>
    <th>Rques</th>
    <th class="narrow"></th>
  </tr>
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
          {{$_op->time_operation|date_format:$conf.time}}
        </span>
      </td>
      <td>
        {{$_op->_ref_salle}}
      </td>
      <td>
        {{$_op->_ref_sejour->_ref_grossesse->datetime_debut_travail}}
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
</table>