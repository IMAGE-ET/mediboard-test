{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul>
  {{foreach from=$locations item=_location}}
    <li class="{{$_location->_guid}}">
      <span class="view">{{$_location->_shortview}}</span>
    </li>
  {{foreachelse}}
    <li>{{tr}}CProductStockLocation.none{{/tr}}</li>
  {{/foreach}}
</ul>