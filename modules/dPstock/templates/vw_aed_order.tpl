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
  window.onbeforeunload = window.onbeforeunload.wrap(function(old){
    old();
    if (window.opener) {
      refreshLists();
    }
  });

  filterReferences(getForm("filter-references"));

  {{if $order->_id}}
  //refreshOrder({{$order->_id}}, {refreshLists: false});
  {{/if}}
});

function changePage(start) {
  $V(getForm("filter-references").start, start);
}

function filterReferences(form) {
  var url = new Url("dPstock", "httpreq_vw_references_list");
  url.addFormData(form);
  url.requestUpdate("list-references");
  return false;
}

</script>

<div style="float: right">
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

<table class="main">
  <tr>
    <td class="halfPane">
      <form action="?" name="filter-references" method="post" onsubmit="return filterReferences(this);">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="order_id" value="{{$order->_id}}" />
        <input type="hidden" name="societe_id" value="{{$order->societe_id}}" />
        
        <select name="category_id" onchange="$V(this.form.start, 0, false); this.form.onsubmit();">
          <option value="0" >&mdash; {{tr}}CProductCategory.all{{/tr}} &mdash;</option>
          {{foreach from=$list_categories item=curr_category}} 
            <option value="{{$curr_category->category_id}}">{{$curr_category->name}}</option>
          {{/foreach}}
        </select>
        
        <input type="text" name="keywords" value="" onchange="$V(this.form.start, 0, false)" />
        <input type="hidden" name="start" value="0" onchange="this.form.onsubmit()" />
        
        <button type="button" class="search notext" name="search" onclick="this.form.onsubmit()">{{tr}}Search{{/tr}}</button>
      </form>
      <div id="list-references"></div>
    </td>

    <td class="halfPane" id="order-{{$order->_id}}">
      {{include file="inc_order.tpl"}}
    </td>
  </tr>
</table>