{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{**
  * $stock ref|CProductStockGroup
  *}}
{{assign var=colors value=","|explode:"critical,min,optimum,max"}}
{{assign var=zone value=$object->_zone}}
<div class="legend">
  <div class="value {{$colors.$zone}}">{{tr}}CProductStockGroup-quantity{{/tr}} : {{$object->quantity}}</div>
  
  {{if $object->order_threshold_critical}}
    <div><div class="color {{$colors.0}}"></div>{{tr}}CProductStockGroup-order_threshold_critical{{/tr}} : {{$object->order_threshold_critical}}</div>
  {{/if}}
  
  <div><div class="color {{$colors.1}}"></div>{{tr}}CProductStockGroup-order_threshold_min{{/tr}} : {{$object->order_threshold_min}}</div>
  
  {{if $object->order_threshold_optimum}}
    <div><div class="color {{$colors.2}}"></div>{{tr}}CProductStockGroup-order_threshold_optimum{{/tr}} : {{$object->order_threshold_optimum}}</div>
  {{/if}}
  
  <div><div class="color {{$colors.3}}"></div>{{tr}}CProductStockGroup-order_threshold_max{{/tr}} : {{$object->order_threshold_max}}</div>
</div>
