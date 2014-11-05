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
    <th class="title" colspan="10">Accouchements en cours au {{$date|date_format:$conf.date}}</th>
  </tr>
  <tr>
    <th>Patiente</th>
    <th>Prat / anesth</th>
    <th>Horaire</th>
    <th>Salle</th>
    <th>Déb. Trav.</th>
    <th>Libellé</th>
    <th>Rques</th>
  </tr>
  {{foreach from=$ops item=_op}}
    <tr>
      <td><span class="CPatient-view">{{$_op->_ref_sejour->_ref_grossesse->_ref_parturiente}}</span></td>
      <td>{{$_op->_ref_chir}} {{if $_op->anesth_id}}/ {{$_op->_ref_anesth}}{{/if}}</td>
      <td>{{$_op->time_operation}}</td>
      <td>{{$_op->_ref_salle}}</td>
      <td>{{$_op->_ref_sejour->_ref_grossesse->datetime_debut_travail}}</td>
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
        {{$_op->_ref_sejour->_ref_grossesse->rques}}
      </td>
    </tr>
  {{/foreach}}
</table>