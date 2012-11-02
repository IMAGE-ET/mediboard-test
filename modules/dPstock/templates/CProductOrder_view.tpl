{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

<script type="text/javascript">
popupOrderForm = function(order_id, width, height) {
  width = width || 1000;
  height = height || 800;

  var url = new Url("dPstock", "vw_order_form");
  url.addParam("order_id", order_id);
  url.popup(width, height, "Bon de commande");
}
</script>

{{assign var=order value=$object}}
 
<table class="main form">
  <tr>
    <th class="title" colspan="5">
      <button type="button" class="print notext" style="float: left;" onclick="popupOrderForm({{$object->_id}})">{{tr}}Print{{/tr}}</button>
      {{$order->getLabel()}}
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$object field=date_ordered}}</th>
    <td>{{mb_value object=$object field=date_ordered}}</td>
    
    <th>{{mb_label object=$object field=order_number}}</th>
    <td>{{mb_value object=$object field=order_number}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$object field=societe_id}}</th>
    <td>{{mb_value object=$object field=societe_id}}</td>
    
    <th>{{mb_label object=$object field=comments}}</th>
    <td>{{mb_value object=$object field=comments}}</td>
  </tr>
  
  {{if $object->object_id}}
    {{$object->_ref_object->loadRefsFwd()}}
    <tr>
      <th>{{mb_label object=$object field=object_id}}</th>
      <td colspan="3">{{mb_value object=$object field=object_id}}</td>
    </tr>
  {{/if}}
</table>

{{mb_include module=stock template=inc_order_items_list screen=true}}
