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
  refreshLists();
});
</script>

<div class="main">
  <!-- Action buttons -->
  <div style="float: right;">
    <button type="button" class="change" onclick="popupOrder(null, 900, 600, true);">{{tr}}CProductOrder-_autofill{{/tr}}</button>
    <button type="button" class="new"    onclick="popupOrder(null, 900, 600);">{{tr}}CProductOrder.create{{/tr}}</button>
  </div>

  <!-- Filter -->
  <form name="orders-list-filter" action="?" method="post" onsubmit="return refreshLists($V(this.keywords));">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="text" name="keywords" onchange="this.form.onsubmit()" />
    <button type="button" class="search" onclick="this.form.onsubmit()">{{tr}}Filter{{/tr}}</button>
    <button type="button" class="cancel notext" onclick="$V(this.form.elements.keywords, '')">{{tr}}Empty{{/tr}}</button>
  </form>

  <!-- Tabs titles -->
  <ul id="tab_orders" class="control_tabs">
    <li><a href="#list-orders-waiting">A valider (<span id="list-orders-waiting-count">0</span>)</a></li>
    <li><a href="#list-orders-locked">A passer (<span id="list-orders-locked-count">0</span>)</a></li>
    <li><a href="#list-orders-pending">A recevoir (<span id="list-orders-pending-count">0</span>)</a></li>
    <li><a href="#list-orders-received">Reçues (<span id="list-orders-received-count">0</span>)</a></li>
    <li><a href="#list-orders-cancelled">Annulées (<span id="list-orders-cancelled-count">0</span>)</a></li>
  </ul>
  <hr class="control_tabs" />
  
  <!-- Tabs containers -->
  <div id="list-orders-waiting" style="display: none;"></div>
  <div id="list-orders-locked" style="display: none;"></div>
  <div id="list-orders-pending" style="display: none;"></div>
  <div id="list-orders-received" style="display: none;"></div>
  <div id="list-orders-cancelled" style="display: none;"></div>
</div>
