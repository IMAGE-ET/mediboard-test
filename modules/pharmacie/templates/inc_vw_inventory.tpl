
<form name="filterInventory" method="get" action="?">
  <input type="hidden" name="m" value="pharmacie" />
  <input type="hidden" name="tab" value="ajax_vw_inventory" />
  
  <label>
    Catégorisation
    <select name="categorization">
      <option value=""> &ndash; Choisir </option>
      <option value="atc" {{if $categorization == "atc"}}selected="selected"{{/if}}>Par classe ATC</option>
      <option value="classe_comptable" {{if $categorization == "classe_comptable"}}selected="selected"{{/if}}>Par classe comptable</option>
      <option value="product_category" {{if $categorization == "product_category"}}selected="selected"{{/if}}>Par catégorie de produit</option>
      <option value="supplier" {{if $categorization == "supplier"}}selected="selected"{{/if}}>Par laboratoire</option>
      <option value="location" {{if $categorization == "location"}}selected="selected"{{/if}}>Par emplacement</option>
    </select>
  </label>
  
  <button type="submit" class="search">{{tr}}Display{{/tr}}</button>
</form>

<script type="text/javascript">
toggleLevel = function(radio) {
  $$('tbody.folder').invoke('hide');
  
  var radios = $A(radio.form[radio.name]);
  
  radios.each(function(r){
    var checked = r.checked;
    $$('tr.level-'+r.value).each(function(tr){
      tr.setVisible(checked)
        .setClassName('visible', checked);
    })
  });
}

addToElement = function(element, value) {
  var current = parseFloat(element.getAttribute("data-value") || 0);
  element.update((current + value).toFixed(2));
  element.writeAttribute("data-value", current + value);
}

processValuation = function(button, container, categorization, label, list, date) {
  $(button).addClassName("loading");
  
  var url = new Url();
  url.addParam("categorization", categorization);
  url.addParam("label", label);
  url.addParam("list[]", list, true);
  url.addParam("date", date);
  
  url.requestJSON(function(data){
    $H(data.totals).each(function(pair){
      $("folder-"+label).select("td.total-product-"+pair.key+"-ht").invoke("update", pair.value.ht);
      $("folder-"+label).select("td.total-product-"+pair.key+"-ttc").invoke("update", pair.value.ttc);
    });
    
    $("total-group-"+label+"-ht").update(data.total);
    $("total-group-"+label+"-ttc").update(data.total_ttc);

    addToElement($("total-ht"), data.total);
    addToElement($("total-ttc"), data.total_ttc);
    
    $(button).removeClassName("loading");
  }, {
    method: "post",
    getParameters: {
      m: "pharmacie", 
      a: "ajax_vw_valuation"
    }
  });
}

provessValuationAll = function(table) {
  var counts = table.select('tr.visible span.count').sort(function(a,b){ return parseInt(a.innerHTML) - parseInt(b.innerHTML) });
  
  $("total-ht").update(0).writeAttribute("data-value", 0);
  $("total-ttc").update(0).writeAttribute("data-value", 0);
  
  counts.each(function(count) {
    var button = count.up('tr').down('button.change');
    button.onclick();
  });
}

</script>

{{if $levels|@count}}
<form name="levelSelector" method="get" action="?">
  Niveaux : 
  {{foreach from=$levels key=_level item=_checked}}
    <label>
      <input type="radio" name="level" value="{{$_level}}" {{if $_checked}}checked="checked"{{/if}} 
             onclick="toggleLevel(this)" /> {{$_level}}
    </label>
  {{/foreach}}
</form>
{{/if}}

<button style="float: right;" onclick="provessValuationAll($('inventory'))" class="change">Tout calculer</button>

<table class="main tbl" id="inventory">
  <tr>
    <th class="narrow">{{tr}}CProduct-code{{/tr}}</th>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th class="narrow"></th>
    
    <th style="width: 5em;">HT</th>
    <th style="width: 5em;">TTC</th>
  </tr>
  
  {{foreach from=$list_by_group item=_group key=_code}}
    {{if $_group.level != false}}
      {{assign var=_level value=$_group.level}}
      <tr class="level-{{$_group.level}} {{if array_key_exists($_level, $levels) && !$levels.$_level}}" style="display: none;" {{else}} visible" {{/if}}>
        <th class="title" style="text-align: left;" onclick="$('folder-{{$_code}}').toggle()">
          {{assign var=_list_ids value=','|implode:$_group.list_id}}
          <button type="button" class="down">{{tr}}Display{{/tr}}</button>
          
          <button type="button" class="print" 
                  onclick="Event.stop(event);(new Url('pharmacie','vw_idx_products_by_id')).pop(800, 600, 'liste produits', null, null, {product_ids: '{{$_list_ids}}', show_stock_quantity: true })">
            avec quantité
          </button>
          <button type="button" class="print" 
                  onclick="Event.stop(event);(new Url('pharmacie','vw_idx_products_by_id')).pop(800, 600, 'liste produits', null, null, {product_ids: '{{$_list_ids}}' })">
            sans quantité
          </button>
        </th>
        <th class="title" style="text-align: left;">
          <span style="float: right;" class="count">{{$_group.list|@count}}</span>
          {{$_group.label}}
        </th>
        <th class="title" style="text-align: right;">
          <button type="button" class="change singleclick" 
                  onclick="processValuation(this,$(this).next('span'),'{{$categorization}}', '{{$_code}}', {{$_group.list_id|@json}}, '')">
            Valeur
          </button>
          <span></span>
        </th>
        
        <th id="total-group-{{$_code}}-ht" style="text-align: right;"></th>
        <th id="total-group-{{$_code}}-ttc" style="text-align: right;"></th>
      </tr>
      <tbody style="display: none;" id="folder-{{$_code}}" class="level-{{$_group.level}} folder">
        {{foreach from=$_group.list item=_product}}
          <tr>
            <td>
              <a class="button edit" target="edit-product" href="?m=dPstock&amp;tab=vw_idx_product&amp;product_id={{$_product->_id}}">
                {{tr}}Edit{{/tr}}
              </a>
            </td>
            <td>{{$_product->code}}</td>
            <td>{{$_product}}</td>
            
            <td class="total-product-{{$_product->_id}}-ht" style="text-align: right;">0</td>
            <td class="total-product-{{$_product->_id}}-ttc" style="text-align: right;">0</td>
          </tr>
        {{/foreach}}
      </tbody>
    {{/if}}
  {{/foreach}}
  <tr style="font-size: 1.5em; text-align: right;">
    <td></td>
    <td></td>
    <td style="font-weight: bold;">Totaux:</td>
    <td id="total-ht"></td>
    <td id="total-ttc"></td>
  </tr>
</table>