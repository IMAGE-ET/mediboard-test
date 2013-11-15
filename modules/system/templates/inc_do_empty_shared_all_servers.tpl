{{*
 * $Id$
 *  
 * @category dPdeveloppement
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th>
      {{tr}}CMbFieldSpec.type.ipAddress{{/tr}}
    </th>
    <th>
      {{tr}}Result{{/tr}}
    </th>
  </tr>
  {{foreach from=$result_send key=ip item=_result}}
    <tr>
      <td>
        {{$ip}}
      </td>
      <td>
        {{$_result.body|smarty:nodefaults}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="2">
        {{tr}}No result{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>