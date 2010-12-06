{{* $Id: vw_idx_delivrance.tpl 9733 2010-08-04 14:03:11Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 9733 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{main}}
  Control.Tabs.create("balance-tabs", true);
  Control.Tabs.create("balance-tabs-byproduct", true);
{{/main}}

<script type="text/javascript">
function toggleDisplay(value) {
  var container = $("balance-product-results");
  
  switch (value) {
    case "quantity": 
    case "price": 
      container.select(".quantity,.price,.sep").invoke("hide");
      container.select("."+value).invoke("show");
      break;
    case "both":
      container.select(".quantity,.price,.sep").invoke("show");
  }
}

function exportData(table, selector, filename) {
  var url = new Url("system", "export_csv_array");
  url.addParam("suppressHeaders", 1);
  var file = {
    data: [],
    filename: filename || "Export"
  };
  
  table.select("tr").each(function(row, i){
    file.data.push([]);
    var line = file.data.last();
    row.childElements().each(function(cell, j) {
      cell = cell.down(selector || "*") || cell;
      var v = cell.innerHTML.replace(/\n/g, "").stripTags().strip();
      line.push(v);
    });
  });
  
  file.data = file.data.toJSON();
  
  window._tempIframe = window._tempIframe || Element.getTempIframe();
  
  url.pop(20, 20, 'csv_export', null, null, file, window._tempIframe);
}

function exportPDF(table, filename) {
  var url = new Url("system", "export_pdf");
  url.addParam("suppressHeaders", 1);
  console.log(table.xml);
  var file = {
    data: table.outerHTML || new XMLSerializer().serializeToString(table),
    filename: filename || "Export"
  };
  
  window._tempIframe = window._tempIframe || Element.getTempIframe();
  
  url.pop(20, 20, 'pdf_export', null, null, file, window._tempIframe);
}
</script>

<ul class="control_tabs" id="balance-tabs">
  <li><a href="#byproduct">Par produit</a></li>
  <li><a href="#byselection">Par sélection de produits</a></li>
  <li><a href="#stock-locations">{{tr}}CProductStockLocation{{/tr}}</a></li>
  <li><a href="#stock-loss">Pertes</a></li>
  <li><a href="#stock-global-value">Valorisation globale</a></li>
</ul>
<hr class="control_tabs" />

<div id="byproduct" style="display: none">
  <form name="filter-product" method="get" action="?" onsubmit="return Url.update(this, 'balance-product-results')">
    <input type="hidden" name="m" value="pharmacie" />
    <input type="hidden" name="a" value="ajax_vw_balance_product" />
    
    {{mb_field object=$stock field=product_id form="filter-product" autocomplete="true,1,50,false,true" style="width:300px; font-size: 1.4em;"}}
    
    <button type="submit" class="search">{{tr}}Show{{/tr}}</button>
    
    <label>
      <input type="checkbox" name="include_void_service" />
      Inclure les mouvements sans service de destination
    </label>

    <label>
      <input type="radio" name="display" value="quantity" onclick="toggleDisplay(this.value)" /> Quantité
    </label>
    <label>
      <input type="radio" name="display" value="price" checked="checked" onclick="toggleDisplay(this.value)" /> Prix
    </label>
    <label>
      <input type="radio" name="display" value="both" onclick="toggleDisplay(this.value)" /> Les deux
    </label>
  </form>
  
  <div id="balance-product-results">
    <div class="small-info">
      Choisissez un produit cliquez sur {{tr}}Show{{/tr}}
    </div>
  </div>
</div>

<div id="byselection" style="display: none">
  <form name="filter-products" method="get" action="?" onsubmit="return Url.update(this, 'balance-selection-results')">
    <input type="hidden" name="m" value="pharmacie" />
    <input type="hidden" name="a" value="ajax_vw_balance_selection" />
    
    <table class="main">
      <tr>
        <td class="narrow">
          <fieldset>
            <legend>{{tr}}CProductSelection{{/tr}}</legend>
            <select name="product_selection_id" onchange="$('advanced-filters').setClassName('opacity-40', $V(this))">
              <option value=""> &ndash; Aucune </option>
              {{foreach from=$list_selections item=_selection}}
                <option value="{{$_selection->_id}}" 
                        {{if $product_selection_id == $_selection->_id}}selected="selected"{{/if}}>
                  {{$_selection}}
                </option>
              {{/foreach}}
            </select>
          </fieldset>
        </td>
        <td>
          <fieldset id="advanced-filters" class="{{$product_selection_id|ternary:'opacity-40':''}}">
            <legend>Filtres avancés</legend>
            
            <table class="layout">
              <tr>
                <th>{{mb_label object=$product field=category_id}}</th>
                <td>
                  <select name="category_id">
                    <option value=""> &ndash; Toutes </option>
                    {{foreach from=$list_categories item=_category}}
                      <option value="{{$_category->_id}}"
                              {{if $category_id == $_category->_id}}selected="selected"{{/if}}>
                        {{$_category}}
                      </option>
                    {{/foreach}}
                  </select>
                </td>
              </tr>
              
              {{* 
              <tr>
                <th>{{mb_label object=$product field=societe_id}}</th>
                <td>
                  <select name="manuf_id">
                    <option value=""> &ndash; Tous </option>
                    {{foreach from=$list_societes item=_societe}}
                      <option value="{{$_societe->_id}}">{{$_societe}}</option>
                    {{/foreach}}
                  </select>
                </td>
              </tr>
              *}}
              
              <tr>
                <th>Distributeur / Labo</th>
                <td>
                  <select name="supplier_id">
                    <option value=""> &ndash; Tous </option>
                    {{foreach from=$list_societes item=_societe}}
                      <option value="{{$_societe->_id}}"
                              {{if $supplier_id == $_societe->_id}}selected="selected"{{/if}}>
                        {{$_societe}}
                      </option>
                    {{/foreach}}
                  </select>
                </td>
              </tr>
              
              <tr>
                <th>{{mb_label object=$product field=classe_comptable}}</th>
                <td>{{mb_field object=$product field=classe_comptable form="filter-products" autocomplete="true,1,50,false,true"}}</td>
              </tr>
              
              <tr>
                <th>{{mb_label object=$product field=_classe_atc}}</th>
                <td>{{mb_field object=$product field=_classe_atc}}</td>
              </tr>
              
              <tr>
                <th><label for="hors_t2a">Hors T2A</label></th>
                <td><input type="checkbox" name="hors_t2a" {{if $hors_t2a}} checked="checked" {{/if}} /></td>
              </tr>
              
            </table>
          </fieldset>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="button">
          <button class="search">{{tr}}Display{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>

  <div id="balance-selection-results">
    <div class="small-info">
      Configurez le filtre dans le formulaire et cliquez sur Filtrer
    </div>
  </div>
</div>

<table id="stock-locations" style="display: none;" class="main tbl">

{{foreach from=$list_locations item=_location}}
  <tr>
    <td class="narrow">
      <button class="print notext" onclick="new Url('dPstock','print_stock_location').addParam('stock_location_id','{{$_location->_id}}').addParam('empty',1).popup()">
        {{tr}}Print{{/tr}}
      </button>
    </td>
    <td>
      {{$_location}}
    </td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="2">{{tr}}CProductStockLocation.none{{/tr}}</td>
  </tr>
{{/foreach}}

</table>

<div id="stock-loss" style="display: none;">
  <form name="filter-products-loss" method="get" action="?" onsubmit="return Url.update(this, 'stock-loss-results')">
    <input type="hidden" name="m" value="pharmacie" />
    <input type="hidden" name="a" value="ajax_vw_loss" />
    
    <table class="main form">
      <tr>
        <th>{{mb_label object=$delivery field=type typeEnum=select}}</th>
        <td>{{mb_field object=$delivery field=type typeEnum=select}}</td>
        
        <th>{{mb_label object=$delivery field=_date_min}}</th>
        <td>{{mb_field object=$delivery field=_date_min register=true form="filter-products-loss"}}</td>
        
        <th>{{mb_label object=$delivery field=_date_max}}</th>
        <td>{{mb_field object=$delivery field=_date_max register=true form="filter-products-loss"}}</td>
      </tr>
      <tr>
        <td colspan="6">
          <button class="search" type="submit">
            {{tr}}Display{{/tr}}
          </button>
        </td>
      </tr>
    </table>
    
  </form>
  <div id="stock-loss-results"></div>
</div>

<div id="stock-global-value" style="display: none;">
  <button class="change" onclick="(new Url('pharmacie', 'ajax_vw_total_price')).requestUpdate($(this).next('div'))">
    Valeur totale
  </button>
  <div style="font-size: 1.5em; text-align: center;"></div>
</div>