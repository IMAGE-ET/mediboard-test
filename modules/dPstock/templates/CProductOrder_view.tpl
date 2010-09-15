{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

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

<table class="layout">
  <tr>
    <td>
      <button type="button" class="print notext" onclick="popupOrderForm({{$object->_id}})">{{tr}}Print{{/tr}}</button>
      {{mb_value object=$object field=order_number}}
    </td>
    <td>{{mb_value object=$object->_ref_societe field=name}}</td>
  </tr>
  {{if $object->object_id}}
  <tr>
    <td colspan="2">
      {{$object->_ref_object->loadRefsFwd()}}
      {{$object->_ref_object}}
    </td>
  </tr>
  {{/if}}
  
  <tr>
    <td colspan="2">
      {{mb_include module=dPstock template=inc_order_items_list screen=true}}
    </td>
  </tr>
</table>
