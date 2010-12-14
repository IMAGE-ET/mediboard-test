
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
toggleLevel = function(level, predicate) {
  $$('tbody.level-'+level).invoke('hide');
  $$('tr.level-'+level).invoke('setVisible', predicate);
}
</script>

{{if $levels|@count}}
  Niveaux : 
  {{foreach from=$levels key=_level item=_checked}}
    <label>
      <input type="checkbox" name="level" value="{{$_level}}" {{if $_checked}}checked="checked"{{/if}} 
             onclick="toggleLevel({{$_level}},this.checked)" /> {{$_level}}
    </label>
  {{/foreach}}
{{/if}}

<table class="main tbl">
  <tr>
    <th class="narrow">{{tr}}CProduct-code{{/tr}}</th>
    <th>{{tr}}CProduct{{/tr}}</th>
  </tr>
  
  {{foreach from=$list_by_group item=_group key=_code}}
    {{if $_group.level != false}}
      {{assign var=_level value=$_group.level}}
      <tr class="level-{{$_group.level}}" {{if array_key_exists($_level, $levels) && !$levels.$_level}} style="display: none;" {{/if}}>
        <th colspan="2" class="title" style="text-align: left;" onclick="$('folder-{{$_code}}').toggle()">
          <span style="float: right;">{{$_group.list|@count}}</span>
          <button type="button" class="down">{{tr}}Display{{/tr}}</button>
          
          {{assign var=_list_ids value=','|implode:$_group.list_id}}
          
          <button type="button" class="print" 
                  onclick="Event.stop(event);(new Url('pharmacie','vw_idx_products_by_id')).pop(800, 600, 'liste produits', null, null, {product_ids: '{{$_list_ids}}', show_stock_quantity: true })">
            avec quantité
          </button>
          <button type="button" class="print" 
                  onclick="Event.stop(event);(new Url('pharmacie','vw_idx_products_by_id')).pop(800, 600, 'liste produits', null, null, {product_ids: '{{$_list_ids}}' })">
            sans quantité
          </button>
          {{$_group.label}}
        </th>
      </tr>
      <tbody style="display: none;" id="folder-{{$_code}}" class="level-{{$_group.level}}">
        {{foreach from=$_group.list item=_product}}
          <tr>
            <td>{{$_product->code}}</td>
            <td>{{$_product}}</td>
          </tr>
        {{/foreach}}
      </tbody>
    {{/if}}
  {{/foreach}}
</table>