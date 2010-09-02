{{* $Id: inc_order.tpl 7667 2009-12-18 16:49:15Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7667 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
closeOrder = function(form) {
  if (confirm('Etes-vous sûr de vouloir clôturer cette commande ?\nCeci aura pour effet de marquer cette commande comme reçue, sans recevoir les articles non encore reçus.')) {
    onSubmitFormAjax(form, {onComplete: function(){window.close()}});
  }
  return false;
}
</script>

{{mb_include module=system template=CMbObject_view object=$order}}

<hr />

<form name="closeOrder-{{$order->_id}}" method="post" action="?" onsubmit="return closeOrder(this)">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_order_aed" />
  <input type="hidden" name="received" value="1" />
  {{mb_key object=$order}}
  
  <button type="submit" class="cancel" style="float: right;">
    Clôturer la réception de cette commande
  </button>
</form>

<table class="tbl">
  <tr>
    <th style="width: 50%;"></th>
    <th style="width: 0.1%; text-align: center;">
    {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
      {{mb_title class=CProductOrderItem field=_unit_quantity}}
    {{else}}
      {{mb_title class=CProductOrderItem field=quantity}}
    {{/if}}
    </th>
    <th style="width: 0.1%; text-align: center;">{{mb_title class=CProductOrderItem field=unit_price}}</th>
    <th style="width: 0.1%; text-align: center;">{{mb_title class=CProductOrderItem field=_price}}</th>
    <th style="width: 0.1%; text-align: center;">Déjà reçu</th>
    <th style="width: 0.1%; text-align: right;"></th>
  </tr>
  {{foreach from=$order->_ref_order_items item=curr_item}}
    <tbody id="order-item-{{$curr_item->_id}}" class="hoverable">
    {{include file="inc_order_to_receive_item.tpl"}}
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CProductOrderItem.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
