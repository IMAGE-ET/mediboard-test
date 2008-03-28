<script type="text/javascript">

function pageMain() {
  regFieldCalendar("edit_order", "date");
  refreshLists();
  
  // Initialisation des onglets du menu
  var tabs = new Control.Tabs('tab_orders');
  tabs.setActiveTab("orders-pending");
}

function refreshListOrders(type, keywords) {
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_orders_list");
  url.addParam("type", type);
  url.addParam("keywords", keywords);
  url.requestUpdate("orders-"+type, { waitingText: null } );
}

function refreshLists(oForm) {
  var keywords = (oForm?oForm.keywords.value:null);
  refreshListOrders("waiting", keywords);
  refreshListOrders("pending", keywords);
  refreshListOrders("old",     keywords);
}

function submitOrder (oForm, refresh, listToRefresh) {
  if (refresh) {
    if (listToRefresh) {
      submitFormAjax(oForm, 'systemMsg', {
        onComplete: function() {refreshListOrders(listToRefresh);}
      });
    } else {
      submitFormAjax(oForm, 'systemMsg', {
        onComplete: function() {
          refreshListOrders("waiting");
          refreshListOrders("pending");
          refreshListOrders("old");
        }
      });
    }
  } else {
    submitFormAjax(oForm, 'systemMsg');
  }
}

function popupOrder(order_id, width, height) {
  width = width?width:500;
  height = height?height:500;
  
  var url = new Url();
  url.setModuleAction("{{$m}}", "vw_aed_order");
  url.addParam("order_id", order_id);
  url.popup(width, height, "Edition/visualisation commande");
}
</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="5">
      <form name="search-order" action="?" method="post" onsubmit="refreshLists(this); return false;">
        <input type="hidden" class="m" name="{{$m}}" />
        <input type="text" class="search" name="keywords" title="Rechercher une commande par numéro ou fournisseur" />
        <button type="button" class="search" onclick="refreshLists(this.form)">Rechercher</button>
      </form>
    
      <ul id="tab_orders" class="control_tabs">
        <li><a href="#orders-waiting"><span id="orders-waiting-count">0</span> pas envoyées</a></li>
        <li><a href="#orders-pending"><span id="orders-pending-count">0</span> non reçues</a></li>
        <li><a href="#orders-old"><span id="orders-old-count">0</span> reçues</a></li>
      </ul>
      <hr class="control_tabs" />
      <div id="orders-waiting"></div>
      <div id="orders-pending"></div>
      <div id="orders-old"></div>
    </td>
    
    <td class="halfPane">
    <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_order_manager&amp;order_id=0">
      Nouvelle commande
    </a>
    <form name="edit-order" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="tab" value="vw_idx_order_manager" />
      <input type="hidden" name="dosql" value="do_order_aed" />
	    <input type="hidden" name="order_id" value="{{$order->_id}}" />
      <input type="hidden" name="group_id" value="{{$g}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $order->_id}}
          <th class="title modify" colspan="2">Modification de la commande {{$order->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Nouvelle commande</th>
          {{/if}}
        </tr>   
        <tr>
          <th>{{mb_label object=$order field="order_number"}}</th>
          <td>{{mb_field object=$order field="order_number"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$order field="societe_id"}}</th>
          <td><select name="societe_id" class="{{$order->_props.societe_id}}">
            <option value="">&mdash; Choisir une société</option>
            {{foreach from=$list_societes item=curr_societe}}
              <option value="{{$curr_societe->_id}}" {{if $order->societe_id == $curr_societe->_id || $list_societes|@count==1}} selected="selected" {{/if}} >
              {{$curr_societe->_view}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$order field="locked"}}</th>
          <td>{{mb_field object=$order field="locked" typeEnum="checkbox"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $order->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la commande',objName:'{{$order->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
              <button class="edit" onclick="popupOrder({{$order->_id}}); return false;">Articles</button>
            {{/if}}
          </td>
        </tr>        
      </table>
    </form>
    </td>
  </tr>
</table>
