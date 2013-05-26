{{* $Id: CProductStockService_view.tpl 6067 2009-04-14 08:04:15Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision: 6067 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{**
  * $stock ref|CProductStock
  *}}

{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{mb_include module=system template=CMbObject_view}}

{{assign var=colors value=","|explode:"critical,min,optimum,max"}}
{{assign var=zone value=$object->_zone}}

<table class="main form">
  <tr>
    <td class="legend">
      {{if $object->order_threshold_critical}}
        <div><div class="color {{$colors.0}}"></div>{{tr}}{{$object->_class}}-order_threshold_critical{{/tr}} : {{$object->order_threshold_critical}}</div>
      {{/if}}
      
      <div><div class="color {{$colors.1}}"></div>{{tr}}{{$object->_class}}-order_threshold_min{{/tr}} : {{$object->order_threshold_min}}</div>
      
      {{if $object->order_threshold_optimum}}
        <div><div class="color {{$colors.2}}"></div>{{tr}}{{$object->_class}}-order_threshold_optimum{{/tr}} : {{$object->order_threshold_optimum}}</div>
      {{/if}}
      
      {{if $object->order_threshold_max}}
        <div><div class="color {{$colors.3}}"></div>{{tr}}{{$object->_class}}-order_threshold_max{{/tr}} : {{$object->order_threshold_max}}</div>
      {{/if}}
    </td>
  </tr>
</table>