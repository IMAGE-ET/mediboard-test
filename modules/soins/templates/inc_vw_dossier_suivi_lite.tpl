{{*
 * $Id$
 *  
 * @category soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th>Type</th>
    <th>Util.</th>
    <th>Date</th>
    <th>Heure</th>
    <th>Contenu</th>
  </tr>
  {{foreach from=$suivi item=_suivi}}
    <tr>
      <td class="narrow">
      {{if $_suivi instanceof CObservationMedicale}}
        Obs
      {{else}}
        TC
      {{/if}}
      </td>
      <td class="narrow">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_suivi->_ref_user initials=border}}
      </td>
      <td style="text-align: center;" class="narrow">
        {{mb_ditto name=date value=$_suivi->date|date_format:$conf.date}}
      </td>
      <td class="narrow">{{$_suivi->date|date_format:$conf.time}}</td>
      <td>
        {{mb_value object=$_suivi field=text}}
      </td>
    </tr>
  {{foreachelse}}
  <tr>
    <td class="empty" colspan="5">
      {{tr}}CTransmissionMedicale.none{{/tr}}
    </td>
  </tr>
  {{/foreach}}
</table>