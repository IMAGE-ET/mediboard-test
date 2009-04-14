{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPstock script=order_manager}}
{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
Main.add(function () {
  window.onbeforeunload = function () {
    if (window.opener) {
      refreshLists();
    }
  }

  {{if $order->_id && !$order->date_ordered}}
  filterFields = ["category_id", "keywords", "order_id", "societe_id"];
  referencesFilter = new Filter("filter-references", "{{$m}}", "httpreq_vw_references_list", "list-references", filterFields, "societe_id");
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

{{if !$order->_id}}
<form name="order-new" action="?m={{$m}}&amp;a=vw_aed_order&amp;dialog=1" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_order_aed" />
  <input type="hidden" name="order_id" value="0" />
  <input type="hidden" name="group_id" value="{{$g}}" />
  <input type="hidden" name="_autofill" value="{{$_autofill}}" />
  <input type="hidden" name="del" value="0" />
  
  <!-- Edit order -->
  <table class="form">
    <tr>
      <th class="title" colspan="2">{{tr}}CProductOrder.create{{/tr}}</th>
    </tr>   
    <tr>
      <th>{{mb_label object=$order field=order_number}}</th>
      <td>{{mb_field object=$order field=order_number}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$order field=societe_id}}</th>
      <td>
        <select name="societe_id" class="{{$order->_props.societe_id}}">
          <option value="">&mdash; {{tr}}CSociete.select{{/tr}}</option>
        {{foreach from=$list_societes item=curr_societe}}
          <option value="{{$curr_societe->_id}}" {{if $list_societes|@count==1}} selected="selected" {{/if}} >
          {{$curr_societe->_view}}
          </option>
        {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

{{else}}
<table class="main">
  <tr>
  {{if !$order->date_ordered}}
    <td class="halfPane">
      <form action="?" name="filter-references" method="post" onsubmit="return referencesFilter.submit();">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="order_id" value="{{$order->_id}}" />
        <input type="hidden" name="societe_id" value="{{$order->societe_id}}" />
        <select name="category_id" onchange="referencesFilter.submit();">
          <option value="0" >&mdash; {{tr}}CProductCategory.all{{/tr}} &mdash;</option>
        {{foreach from=$list_categories item=curr_category}} 
          <option value="{{$curr_category->category_id}}">{{$curr_category->name}}</option>
        {{/foreach}}
        </select>
        <input type="text" name="keywords" value="" />
        <button type="button" class="search" name="search" onclick="referencesFilter.submit();">{{tr}}Search{{/tr}}</button>
      </form>
      <div id="list-references"></div>
    </td>
  {{/if}}

    <td class="halfPane">
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
        <button class="trash" type="button" onclick="submitOrder(this.form, {close: true})">Annuler la commande</button>
      </form>
      
      <table class="form">
        <tr>
          <th>{{mb_title object=$order field=order_number}}</th>
          <td>{{mb_value object=$order field=order_number}}</td>
        </tr>
        <tr>
          <th>{{mb_title object=$order field=societe_id}}</th>
          <td>{{$order->_ref_societe->_view}}</td>
        </tr>
      </table>
      
      <div id="order-{{$order->_id}}">
        {{include file="inc_order.tpl"}}
      </div>
    </td>
  </tr>
</table>
{{/if}}