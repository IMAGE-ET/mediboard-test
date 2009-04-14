{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl tooltip">
  <tr>
    <th>{{$object->_view}}</th>
  </tr>
  <tr>
    <td>
      {{foreach from=$props key=name item=value}}
        {{if $value != ''}}
          <strong>{{mb_label object=$object field=$name}}</strong> :
          {{mb_value object=$object field=$name}}<br />
        {{/if}}
      {{/foreach}}
    </td>
  </tr>
</table>