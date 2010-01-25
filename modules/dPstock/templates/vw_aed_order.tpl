{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPstock script=order_manager}}

<script type="text/javascript">
Main.add(function () {
  window.onbeforeunload = window.onbeforeunload.wrap(function(old){
    old();
    if (window.opener) {
      refreshLists();
    }
  });

  filterReferences(getForm("filter-references"));
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

function orderItemCallback(order_item_id, order_item) {
  createOrders(order_item.order_id);
}

function reloadOrders(order_id) {
  window.order_id = order_id;
  
  var url = new Url("dPstock", "httpreq_vw_orders_tabs");
  url.requestUpdate("orders-list");
}

function createOrders(order_id) {
  var tab = $$('a[href="#order-'+order_id+'"]')[0];
  if (tab) {
    refreshOrder(order_id);
    tabs.setActiveTab("order-"+order_id);
  }
  else {
    reloadOrders(order_id);
  }
}
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <form action="?" name="filter-references" method="post" onsubmit="return filterReferences(this);">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="societe_id" value="{{$societe_id}}" />
        <input type="hidden" name="order_form" value="true" />
        <input type="hidden" name="start" value="0" onchange="this.form.onsubmit()" />
        
        <select name="category_id" onchange="$V(this.form.start, 0, false); this.form.onsubmit();">
          <option value="" >&mdash; {{tr}}CProductCategory.all{{/tr}} &mdash;</option>
          {{foreach from=$list_categories item=curr_category}} 
            <option value="{{$curr_category->category_id}}">{{$curr_category->name}}</option>
          {{/foreach}}
        </select>
        
        <select name="societe_id" onchange="$V(this.form.start, 0, false); this.form.onsubmit();">
          <option value="" >&mdash; {{tr}}CSociete.all{{/tr}} &mdash;</option>
          {{foreach from=$list_societes item=_societe}} 
            <option value="{{$_societe->_id}}">{{$_societe}}</option>
          {{/foreach}}
        </select>
        
        <input type="text" name="keywords" value="" size="10" onchange="$V(this.form.start, 0, false)" />
        
        <button type="button" class="search notext" name="search" onclick="this.form.onsubmit()">{{tr}}Search{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="$(this.form).clear(false); this.form.onsubmit()"></button>
      </form>
      <div id="list-references"></div>
    </td>

    <td class="halfPane" id="orders-list">
      {{mb_include module=dPstock template=inc_orders_tabs}}
    </td>
  </tr>
</table>