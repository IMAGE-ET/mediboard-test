{{* $Id: vw_aed_order.tpl 7662 2009-12-18 13:35:39Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7662 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPstock script=order_manager}}

<script type="text/javascript">
var reception_id = '{{$reception->_id}}';

Main.add(function () {
  var form = getForm("orders-search");
  var url = new Url("dPstock", "httpreq_orders_autocomplete");
  url.autoComplete(form.keywords, $("keywords_autocomplete"), {
    select: "view",
    dropdown: true,
    valueElement: form.id_reference
  });
});

function addOrder(id_reference){
  if (!id_reference) return;
  
  var parts = id_reference.match(/^(.*)-(.*)$/),
      elementId = "order-"+parts[1];
  
  if ($(elementId)) return;
  
  var container = $("orders-containers"),
      order = DOM.div({id: elementId, className: "order"}), 
      orderTab = DOM.li({}, DOM.a({href: "#"+elementId}, parts[2]));
  
  if (!container.select(".order").length) {
    container.update("");
  }
  
  container.insert(order);
  $("orders-tabs").insert(orderTab);
  var tabs = Control.Tabs.create("orders-tabs");
  tabs.setActiveTab(elementId);
  
  var url = new Url("dPstock", "httpreq_vw_order");
  url.addParam("order_id", parts[1]);
  url.requestUpdate(order);
}

function cancelReception(reception_id, on_complete) {
  var form = getForm("cancel-reception");
  $V(form.order_item_reception_id, reception_id);
  return onSubmitFormAjax(form, {onComplete: on_complete});
}

function makeReception(form) {
  $V(form.reception_id, reception_id);
  
  form.getElements().each(
    function(element) {
      if (element.name == 'barcode_printed') element.disabled = true;
    }
  );
  return onSubmitFormAjax(form);
}

function barcodePrintedReception(reception_id, value) {
  var form = getForm("barcode_printed-reception");
  $V(form.order_item_reception_id, reception_id);
  $V(form.barcode_printed, value ? '1' : '0');
  return onSubmitFormAjax(form);
}

function updateReceptionId(reception_item_id) {
  new Url("system", "ajax_object_value").mergeParams({
    guid: "CProductOrderItemReception-"+reception_item_id,
    field: "reception_id"
  }).requestJSON(function(v){
    reception_id = v;
    refreshReception(reception_id);
  });
}
</script>

<form name="cancel-reception" action="" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_order_item_reception_aed" />
  <input type="hidden" name="order_item_reception_id" value="" />
  <input type="hidden" name="del" value="1" />
</form>

<form name="barcode_printed-reception" action="" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_order_item_reception_aed" />
  <input type="hidden" name="order_item_reception_id" value="" />
  <input type="hidden" name="barcode_printed" value="" />
</form>

<h3>{{tr}}CProductReception{{/tr}} (commande {{$order->order_number}})</h3>

<table class="main">
  <tr>
    <th class="title">{{tr}}CProductOrder{{/tr}}s</th>
    <th class="title">
      {{tr}}CProductReception{{/tr}} - {{$reception->reference}}
    </th>
  </tr>
  <tr>
    <td class="halfPane" style="padding: 0;">
      <form name="orders-search" action="?" method="get" onsubmit="return false">
        <input type="hidden" name="id_reference" value="" onchange="addOrder(this.value)" />
        <label for="keywords">
          Commandes actuelles :
        </label>
        <input type="text" name="keywords" />
      </form>
      
      <ul class="control_tabs" id="orders-tabs">
        {{if $order->_id}}
          <li><a href="#order-{{$order->_id}}">{{$order->order_number}}</a></li>
        {{/if}}
      </ul>
      <hr class="control_tabs" />

      <div id="orders-containers">
        {{if !$order->_id}}
        <div class="small-info">
          Veuillez chercher une commande à ajouter à la réception
        </div>
        {{else}}
          <div class="order" id="order-{{$order->_id}}">
            {{mb_include module=dPstock template=inc_order_to_receive}}
          </div>
        {{/if}}
      </div>
    </td>
    <td id="reception" style="padding: 0;">
      {{include file="inc_reception.tpl"}}
    </td>
  </tr>
</table>