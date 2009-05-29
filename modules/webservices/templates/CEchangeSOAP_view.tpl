{{* $Id: CMbObject_view.tpl 6069 2009-04-14 10:17:11Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl tooltip">
  <tr>
    <th>{{$object->_view}}</th>
  </tr>
  <tr>
    <td>
      <strong>{{mb_label object=$object field=type}}</strong> : {{mb_value object=$object field=type}}
    </td>
  </tr>
  <tr>
    <td>
      <strong>{{mb_label object=$object field=date_echange}}</strong> : {{mb_value object=$object field=date_echange}}
    </td>
  </tr>
  <tr>
    <td>
      <strong>{{mb_label object=$object field=destinataire}}</strong> : {{mb_value object=$object field=destinataire}}
    </td>
  </tr>
</table>