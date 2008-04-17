{{mb_include_script module=dPstock script=order_manager}}

<script type="text/javascript">
function pageMain() {
  regFieldCalendar("edit_order", "date");
  refreshLists();
  
  // Initialisation des onglets du menu
  var tabs = Control.Tabs.create('tab_orders', true);
  //tabs.setActiveTab("orders-pending");
}
</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="5">
      <form name="search-order" action="?" method="post" onsubmit="refreshLists($F(this.keywords)); return false;">
        <input type="hidden" class="m" name="{{$m}}" />
        <input type="text" class="search" name="keywords" title="Rechercher une commande par numéro ou fournisseur" />
        <button type="button" class="search" onclick="refreshLists($F(this.form.keywords))">Rechercher</button>
      </form>
      
      <form name="order-new" action="?" method="post">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="order_id" value="0" />
        <input type="hidden" name="_autofill" value="0" />
        <button type="button" class="change" onclick="Form.Element.setValue(_autofill, 1); popupOrder(this.form, 800, 600);">Commande auto</button>
        <button type="button" class="new"    onclick="Form.Element.setValue(_autofill, 0); popupOrder(this.form, 800, 600);">Nouvelle commande</button>
      </form>
    
      <ul id="tab_orders" class="control_tabs">
        <li><a href="#list-orders-waiting"  >A valider (<span id="list-orders-waiting-count">0</span>)</a></li>
        <li><a href="#list-orders-locked"   >A envoyer (<span id="list-orders-locked-count">0</span>)</a></li>
        <li><a href="#list-orders-pending"  >A recevoir (<span id="list-orders-pending-count">0</span>)</a></li>
        <li><a href="#list-orders-received" >Reçues (<span id="list-orders-received-count">0</span>)</a></li>
        <li><a href="#list-orders-cancelled">Annulées (<span id="list-orders-cancelled-count">0</span>)</a></li>
      </ul>
      <hr class="control_tabs" />
      <div id="list-orders-waiting" style="display: none;"></div>
      <div id="list-orders-locked" style="display: none;"></div>
      <div id="list-orders-pending" style="display: none;"></div>
      <div id="list-orders-received" style="display: none;"></div>
      <div id="list-orders-cancelled" style="display: none;"></div>
    </td>
  </tr>
</table>
