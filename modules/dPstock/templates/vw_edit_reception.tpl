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
  
  tabs = Control.Tabs.create("orders-tabs");
  
  filterReferences(getForm("filter-references"));
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
  $$("a[href='#no-order']")[0].up().insert({before: orderTab});
  tabs = Control.Tabs.create("orders-tabs");
  tabs.setActiveTab(elementId);
  
  refreshOrderToReceive(parts[1], order);
}

function refreshOrderToReceive(order_id, element) {
  var url = new Url("dPstock", "httpreq_vw_order");
  url.addParam("order_id", order_id);
  url.requestUpdate(element);
} 

function cancelReception(reception_id, on_complete) {
  var form = getForm("cancel-reception");
  $V(form.order_item_reception_id, reception_id);
  return onSubmitFormAjax(form, {onComplete: on_complete});
}

function makeReception(form, order_id) {
  $V(form.reception_id, reception_id);
  
  form.getElements().each(
    function(element) {
      if (element.name == 'barcode_printed') element.disabled = true;
    }
  );
  return onSubmitFormAjax(form, {onComplete: function(){
    /*$V(form.code, ''); 
    $V(form.lapsing_date, '');*/
    refreshOrderToReceive(order_id, "order-"+order_id);
  }});
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

function filterReferences(form) {
  var url = new Url("dPstock", "httpreq_vw_references_list");
  url.addFormData(form);
  url.requestUpdate("list-references");
  return false;
}

function changePage(start) {
  $V(getForm("filter-references").start, start);
}

function changeLetter(letter) {
  var form = getForm("filter-references");
  $V(form.start, 0, false);
  $V(form.letter, letter);
}

function receptionCallback(){
  refreshReception(window.reception_id);
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

<table class="main">
  <tr>
    <td><h3>{{tr}}CProductReception{{/tr}} (commande {{$order->order_number}})</h3></td>
    <td><h3>{{tr}}CProductReference-societe_id{{/tr}} : {{$order->societe_id|ternary:$order->_ref_societe:$reception->_ref_societe}}</h3></td>
  </tr>
  <tr>
    <th class="title">{{tr}}CProductOrder{{/tr}}</th>
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
        <input type="text" name="keywords" size="30" />
      </form>
      
      <ul class="control_tabs" id="orders-tabs">
        {{if $order->_id}}
          <li><a href="#order-{{$order->_id}}">{{$order->order_number}}</a></li>
        {{/if}}
        <li><a href="#no-order">Hors commande</a></li>
      </ul>
      <hr class="control_tabs" />

      <div id="orders-containers">
        <div id="no-order">
          <form action="?" name="filter-references" method="post" onsubmit="return filterReferences(this);">
            <input type="hidden" name="m" value="{{$m}}" />
            <input type="hidden" name="mode" value="reception" />
            <input type="hidden" name="start" value="0" onchange="this.form.onsubmit()" />
            <input type="hidden" name="letter" value="{{$letter}}" onchange="this.form.onsubmit()" />
            
            <select name="category_id" onchange="$V(this.form.start, 0, false); this.form.onsubmit();">
              <option value="" >&mdash; {{tr}}CProductCategory.all{{/tr}} &mdash;</option>
              {{foreach from=$list_categories item=curr_category}} 
                <option value="{{$curr_category->category_id}}">{{$curr_category->name}}</option>
              {{/foreach}}
            </select>
        
            {{mb_field object=$order field=societe_id form="filter-references" autocomplete="true,1,50,false,true" 
                       style="width: 12em;" onchange="\$V(this.form.start,0)"}}
            
            <input type="text" name="keywords" value="" size="10" onchange="$V(this.form.start, 0, false)" />
            
            <button type="button" class="search notext" name="search" onclick="this.form.onsubmit()">{{tr}}Search{{/tr}}</button>
            <button type="button" class="cancel notext" onclick="$(this.form).clear(false); this.form.onsubmit()"></button>
            
            {{mb_include module=system template=inc_pagination_alpha current=$letter change_page=changeLetter narrow=true}}
          </form>
          <div id="list-references"></div>
        </div>
        
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