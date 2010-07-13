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
  // Menu tabs initialization
  var tabs = Control.Tabs.create('tab_orders', true);
  
  // Orders lists have to be shown
  refreshAll();
});

refreshAll = function(form) {
  refreshLists(form);
  refreshReceptionsList(0);
  return false;
}

refreshReceptionsList = function(page){
  var url = new Url("dPstock", "httpreq_vw_receptions_list");
  url.addParam("start", page);
  url.addParam("keywords", $V(getForm("orders-list-filter").keywords));
  url.requestUpdate("list-receptions");
}

confirmPurge = function(element, view, type) {
  var form = element.form;
  if (confirm("ATTENTION : Vous êtes sur le point de supprimer une commande, ainsi que tous les objets qui s'y rattachent")) {
    form._purge.value = 1;
    confirmDeletion(form,  {
      typeName:'la commande',
      objName:view,
      ajax: true
    }, {
      onComplete: refreshListOrders.curry(type, form)
    });
  }
}
</script>

<div class="main">
  <!-- Action buttons -->
  <div style="float: right;">
    <button type="button" class="change" onclick="popupOrder(null, null, null, true);">{{tr}}CProductOrder-_autofill{{/tr}}</button>
    <button type="button" class="new"    onclick="popupOrder(null, null, null);">{{tr}}CProductOrder-title-create{{/tr}}</button>
  </div>

  <!-- Filter -->
  <form name="orders-list-filter" action="?" method="get" onsubmit="return refreshAll(this)">
    <select name="category_id" onchange="this.form.onsubmit()">
      <option value="" >&ndash; {{tr}}CProductCategory.all{{/tr}}</option>
    {{foreach from=$list_categories item=_category}}
      <option value="{{$_category->category_id}}" {{if $category_id==$_category->_id}}selected="selected"{{/if}}>{{$_category->name}}</option>
    {{/foreach}}
    </select>
    <input type="text" name="keywords" />
    
    <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
    <button type="button" class="cancel notext" onclick="$(this.form).clear(false); this.form.onsubmit();">{{tr}}Empty{{/tr}}</button>
  </form>

  <!-- Tabs titles -->
  <ul id="tab_orders" class="control_tabs">
    <li><a href="#list-orders-waiting" class="empty">A valider <small>(0)</small></a></li>
    <li><a href="#list-orders-locked" class="empty">A passer <small>(0)</small></a></li>
    <li><a href="#list-orders-pending" class="empty">A recevoir <small>(0)</small></a></li>
    <li><a href="#list-orders-received" class="empty">Reçues <small>(0)</small></a></li>
    <li><a href="#list-orders-cancelled" class="empty">Annulées <small>(0)</small></a></li>
    <li style="margin-left: 4em;"><a href="#list-receptions" class="empty">Réceptions <small>(0)</small></a></li>
  </ul>
  <hr class="control_tabs" />
  
  <!-- Tabs containers -->
  <div id="list-orders-waiting" style="display: none;"></div>
  <div id="list-orders-locked" style="display: none;"></div>
  <div id="list-orders-pending" style="display: none;"></div>
  <div id="list-orders-received" style="display: none;"></div>
  <div id="list-orders-cancelled" style="display: none;"></div>
  <div id="list-receptions" style="display: none;"></div>
</div>
