{{*
 * $Id$
 *  
 * @category Astreintes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th colspan="2" class="title">
      Liste des téléphones disponibles
    </th>
  </tr>
  {{foreach from=$phones key=type item=_phone}}
    <tr>
      <td><button type="button" onclick="setPhone('{{$_phone}}');">{{$_phone}}</button></td>
      <th>{{tr}}{{$type}}{{/tr}}</th>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="2" class="empty">
        {{tr}}Phone.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>