{{* $Id: vw_aed_order.tpl 7662 2009-12-18 13:35:39Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7662 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPstock script=order_manager}}
{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
Main.add(function () {
  {{if $order->_id && !$order->date_ordered}}
  filterFields = ["category_id", "keywords", "order_id", "societe_id", "limit"];
  referencesFilter = new Filter("filter-references", "{{$m}}", "httpreq_vw_references_list", "list-references", filterFields, "societe_id");
  referencesFilter.submit();
  {{/if}}

  {{if $order->_id}}
  refreshOrder({{$order->_id}}, {refreshLists: false});
  {{/if}}
});

function cancelReception(reception_id, on_complete) {
  var form = getForm("cancel-reception");
  $V(form.order_item_reception_id, reception_id);
  return onSubmitFormAjax(form, {onComplete: on_complete});
}

function makeReception(form, order_id) {
	form.getElements().each(
	  function(element) {
			if (element.name == 'barcode_printed') element.disabled = true;
		}
	);
  return onSubmitFormAjax(form, {onComplete: function() {refreshOrder(order_id)} });
}

function barcodePrintedReception(reception_id, value) {
  var form = getForm("barcode_printed-reception");
  $V(form.order_item_reception_id, reception_id);
  $V(form.barcode_printed, value ? '1' : '0');
  return onSubmitFormAjax(form);
}
</script>

<form name="cancel-reception" action="" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_order_item_reception_aed" />
  <input type="hidden" name="order_item_reception_id" value="" />
  <input type="hidden" name="_cancel" value="1" />
</form>

<form name="barcode_printed-reception" action="" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_order_item_reception_aed" />
  <input type="hidden" name="order_item_reception_id" value="" />
  <input type="hidden" name="barcode_printed" value="" />
</form>

<div style="float: right">
  <form name="order-receive-{{$order->_id}}" action="?" method="post" onsubmit="return checkForm(this);">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="dosql" value="do_order_aed" />
    <input type="hidden" name="order_id" value="{{$order->_id}}" />
    <input type="hidden" name="del" value="0" />
    
  {{if $order->date_ordered}}
    <input type="hidden" name="_receive" value="1" />
    <button type="button" class="tick" onclick="submitOrder(this.form, {close: true})">{{tr}}CProductOrder-_receive{{/tr}}</button>
    
  {{else if !$order->_received}}
    <input type="hidden" name="_autofill" value="1" />
    <button type="button" class="change" onclick="submitOrder(this.form, {refreshLists: true})">{{tr}}CProductOrder-_autofill{{/tr}}</button>
  {{/if}}
  </form>
  
  <form name="order-cancel-{{$order->_id}}" action="?" method="post" onsubmit="return checkForm(this);">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="dosql" value="do_order_aed" />
    <input type="hidden" name="order_id" value="{{$order->_id}}" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="cancelled" value="1" />
    <button class="cancel" type="button" onclick="submitOrder(this.form, {close: true})">Annuler la commande</button>
  </form>
</div>

<h3>{{tr}}CProductOrder{{/tr}} {{$order->order_number}}</h3>

{{include file="inc_order.tpl"}}