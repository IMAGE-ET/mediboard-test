{{mb_include_script module=dPstock script=order_manager}}

<script type="text/javascript">
function pageMain() {
  window.onbeforeunload = function () {
    if (window.opener) {
      refreshLists();
    }
  }
  {{if $order->_id}}
  submitFilter({});
  {{/if}}
}

function submitFilter (oForm) {
  if (oForm) {
    url = new Url; // FIXME : ya pas un autre moyen ?
    url.setModuleAction("dPstock","httpreq_vw_products_list");
    url.addParam("category_id",  (oForm.category_id?oForm.category_id.value:null));
    url.addParam("keywords",     (oForm.keywords   ?oForm.keywords.value:null));
    url.addParam("order_id",     {{$order->_id}});
    url.requestUpdate("list-products", { waitingText: null } );
  }
}
</script>


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
      <th class="title" colspan="2">Nouvelle commande</th>
    </tr>   
    <tr>
      <th>{{mb_label object=$order field="order_number"}}</th>
      <td>{{mb_field object=$order field="order_number"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$order field="societe_id"}}</th>
      <td>
        <select name="societe_id" class="{{$order->_props.societe_id}}">
          <option value="">&mdash; Choisir une société</option>
        {{foreach from=$list_societes item=curr_societe}}
          <option value="{{$curr_societe->_id}}" {{if $list_societes|@count==1}} selected="selected" {{/if}} >
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
      <td colspan="2" class="button">
        <button class="submit" type="submit">Remplir</button>
      </td>
    </tr>
  </table>
</form>



{{else}}
<table class="main">
  <tr>
  {{if !$order->date_ordered && !$hide_products_list}}
    <td class="halfPane">
      <form action="?" name="filter-products" method="post" onsubmit="submitFilter(this); return false;">
        <input type="hidden" name="m" value="{{$m}}" />
        <label for="category_id" title="Choisissez une catégorie">Catégorie</label>
        <select name="category_id" onchange="submitFilter(this.form)">
          <option value="0" >&mdash; Choisir une catégorie &mdash;</option>
        {{foreach from=$list_categories item=curr_category}} 
          <option value="{{$curr_category->category_id}}">{{$curr_category->name}}</option>
        {{/foreach}}
        </select>
        <input type="text" name="keywords" value="" />
        <button type="button" class="search" name="search">Rechercher</button>
      </form>
      
      {{if !$dialog}}
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_order_manager&amp;order_id=0">
        Nouvelle commande
      </a>
      {{/if}}
    
      <div style="text-align: right;">
      <button type="button" class="down" onclick="">Suggérer</button>
      </div>
      <div id="list-products"></div>
    </td>
  {{/if}}

    <td class="halfPane">
      <form name="order-edit-{{$order->_id}}" action="?" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_order_aed" />
        <input type="hidden" name="order_id" value="{{$order->_id}}" />
        <input type="hidden" name="group_id" value="{{$g}}" />
        <input type="hidden" name="del" value="0" />
        {{if $order->date_ordered}}
          <input type="hidden" name="_receive" value="0" />
          <button type="button" class="tick" onclick="Form.Element.setValue(_receive, 1); submitOrder(this.form, {close: true})">Recevoir tout</button>
          
        {{else if !$order->_received}}
          <input type="hidden" name="_autofill" value="0" />
          <button type="button" class="change" onclick="Form.Element.setValue(_autofill, 1); submitOrder(this.form, {refreshLists: true})">Commande auto</button>
        {{/if}}
        
        {{if $order->_id}}
          <input type="hidden" name="cancelled" value="0" />
          <button class="trash" type="button" onclick="Form.Element.setValue(cancelled, 1); submitOrder(this.form, {close: true})">Supprimer</button>
        {{/if}}
        
        {{assign var=readonly value=$order->_id}}
        <!-- Edit order -->
        <table class="form">
          <tr>
            {{if $order->_id}}
            <th class="title modify" colspan="2">Modification de la commande {{$order->order_number}}</th>
            {{else}}
            <th class="title" colspan="2">Nouvelle commande</th>
            {{/if}}
          </tr>   
          <tr>
            <th>{{mb_label object=$order field="order_number"}}</th>
            <td {{if $readonly}}class="readonly"{{/if}}>{{mb_field object=$order field="order_number" readonly=$readonly}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$order field="societe_id"}}</th>
            <td>
            {{if $readonly}}
              {{$order->_ref_societe->_view}}
            {{else}}
              <select name="societe_id" class="{{$order->_props.societe_id}}">
              <option value="">&mdash; Choisir une société</option>
              {{foreach from=$list_societes item=curr_societe}}
                <option value="{{$curr_societe->_id}}" {{if $order->societe_id == $curr_societe->_id || $list_societes|@count==1}} selected="selected" {{/if}} >
                {{$curr_societe->_view}}
                </option>
              {{/foreach}}
              </select>
            {{/if}}
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$order field="locked"}}</th>
            <td>{{mb_field object=$order field="locked" typeEnum="checkbox" onChange="submitOrder(this.form, {refreshLists: true})"}}</td>
          </tr>
        </table>
      </form>
      
      <div id="order-{{$order->_id}}">
        {{include file="inc_order.tpl"}}
      </div>
    </td>
  </tr>
</table>
{{/if}}