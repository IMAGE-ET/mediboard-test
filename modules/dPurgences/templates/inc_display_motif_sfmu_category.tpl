{{*
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th>
      {{tr}}CMotifSFMU-libelle{{/tr}}
    </th>
    <th>
      {{tr}}CMotifSFMU-code{{/tr}}
    </th>
    <th></th>
  </tr>
  {{foreach from=$list_motif_sfmu item=motif_sfmu}}
    <tr>
      <td>
        {{$motif_sfmu->libelle}}
      </td>
      <td>
        {{$motif_sfmu->code}}
      </td>
      <td>
        <button type="button" class="tick notext"
                onclick="CCirconstance.selectMotifSFMU('{{$motif_sfmu->libelle|smarty:nodefaults|JSAttribute}}', '{{$motif_sfmu->_id}}')">
          {{tr}}Select{{/tr}}
        </button>
      </td>
    </tr>
  {{foreachelse}}
    <tr><td class="empty">{{tr}}CMotifSFMU.one{{/tr}}</td></tr>
  {{/foreach}}
</table>