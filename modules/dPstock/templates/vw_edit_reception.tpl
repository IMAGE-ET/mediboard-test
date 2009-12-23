{{* $Id: vw_aed_order.tpl 7662 2009-12-18 13:35:39Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7662 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function () {
  var form = getForm("orders-search");
  var url = new Url("dPstock", "httpreq_orders_autocomplete");
  url.autoComplete(form.keywords, null, {
    select: "view",
    dropdown: true,
    valueElement: form.id_reference
  });
});

function addOrder(id_reference){
  var parts = id_reference.match(/^(.*)-(.*)$/);
  var elementId = "order-"+parts[1];
  
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

<h3>{{tr}}CProductReception{{/tr}} {{$reception->reference}}</h3>

<div class="small-info">
  La réception d'articles est en cours de construction
</div>

<table class="main">
  <tr>
    <th class="title">{{tr}}CProductOrder{{/tr}}s</th>
    <th class="title">{{tr}}CProductReception{{/tr}}</th>
  </tr>
  <tr>
    <td class="halfPane" style="padding: 0;">
      <form name="orders-search" action="?" method="get" onsubmit="return false">
        <input type="hidden" name="id_reference" value="" onchange="addOrder(this.value)" />
        <label>
          Recherche de commandes :
          <input type="text" name="keywords" />
        </label>
      </form>
      
      <ul class="control_tabs" id="orders-tabs"></ul>
      <hr class="control_tabs" />

      <div id="orders-containers">
        <div class="small-info">
          Veuillez chercher une commande à ajouter à la réception
        </div>
      </div>
    </td>
    <td id="reception-{{$reception->_id}}" style="padding: 0;">
      {{include file="inc_reception.tpl"}}
    </td>
  </tr>
</table>