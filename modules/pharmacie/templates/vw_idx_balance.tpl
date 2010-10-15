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

<ul class="control_tabs" id="balance-tabs">
  <li><a href="#byproduct">Par produit</a></li>
  <li><a href="#byselection">Par sélection de produits</a></li>
</ul>
<hr class="control_tabs" />

<div id="byproduct" style="display: none">
  <form name="filter-product" method="get" action="?" onsubmit="return Url.update(this, 'balance-product-results')">
    <input type="hidden" name="m" value="pharmacie" />
    <input type="hidden" name="a" value="ajax_vw_balance_product" />
    
    {{mb_field object=$stock field=product_id form="filter-product" autocomplete="true,1,50,false,true" style="width:300px; font-size: 1.4em;"}}
    
    <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
  </form>
  
  <div id="balance-product-results">
    <div class="small-info">
      Choisissez un produit cliquez sur {{tr}}Search{{/tr}}
    </div>
  </div>
</div>

<div id="byselection" style="display: none">
  <form name="filter-products" method="get" action="?" onsubmit="return Url.update(this, 'balance-selection-results')">
    <input type="hidden" name="m" value="pharmacie" />
    <input type="hidden" name="a" value="ajax_vw_balance_selection" />
    
    <table class="main form">
      <tr>
        <td class="narrow">
          <fieldset>
            <legend>{{tr}}CProductSelection{{/tr}}</legend>
            <select name="product_selection_id" onchange="$('advanced-filters').setOpacity($V(this)?0.4:1)">
              <option value=""> &ndash; Aucune </option>
              {{foreach from=$list_selections item=_selection}}
                <option value="{{$_selection->_id}}">{{$_selection}}</option>
              {{/foreach}}
            </select>
          </fieldset>
        </td>
        <td>
          <fieldset id="advanced-filters">
            <legend>Filtres avancés</legend>
            
            <table class="layout">
              <tr>
                <th>{{mb_label object=$product field=category_id}}</th>
                <td>
                  <select name="category_id">
                    <option value=""> &ndash; Toutes </option>
                    {{foreach from=$list_categories item=_category}}
                      <option value="{{$_category->_id}}">{{$_category}}</option>
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
                <td><input type="checkbox" name="hors_t2a" /></td>
              </tr>
              
            </table>
          </fieldset>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="button">
          <button class="search">{{tr}}Filter{{/tr}}</button>
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