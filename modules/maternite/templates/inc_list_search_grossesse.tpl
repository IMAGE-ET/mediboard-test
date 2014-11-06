{{*
 * $Id$
 *  
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{foreach from=$grossesses item=_grossesse}}
  <tr>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_grossesse->_ref_parturiente->_guid}}');">
        {{mb_value object=$_grossesse->_ref_parturiente field=nom}} {{mb_value object=$_grossesse->_ref_parturiente field=prenom}}
      </span>
    </td>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_grossesse->_guid}}');">
        {{mb_value object=$_grossesse field=terme_prevu}}
      </span>
    </td>
    <td {{if !$_grossesse->multiple}}class="empty"{{/if}}>
      {{mb_value object=$_grossesse field=multiple}}
    </td>
    <td {{if !$_grossesse->fausse_couche}}class="empty"{{/if}}>
      {{$_grossesse->fausse_couche}}
    </td>
    <td>
      <button class="edit notext" onclick="Tdb.editGrossesse('{{$_grossesse->_id}}')"></button>
    </td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="5" class="empty">{{tr}}CGrossesse.none{{/tr}}</td>
  </tr>
{{/foreach}}
