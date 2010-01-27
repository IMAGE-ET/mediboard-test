{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function () {
  filterReferences(getForm("filter-products"));
  Control.Tabs.create("tabs-stocks-references", true);
});

function changePage(start) {
  $V(getForm("filter-products").start, start);
}

function filterReferences(form) {
  var url = new Url("dPstock", "httpreq_vw_products_list");
  url.addFormData(form);
  url.requestUpdate("list-products");
  return false;
}
</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="10">
      <form name="filter-products" action="?" method="post" onsubmit="return filterReferences(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="start" value="0" onchange="this.form.onsubmit()" />
        
        <select name="category_id" onchange="$V(this.form.start,0);this.form.onsubmit()">
          <option value="" >&ndash; {{tr}}CProductCategory.all{{/tr}}</option>
        {{foreach from=$list_categories item=curr_category}}
          <option value="{{$curr_category->category_id}}" {{if $filter->category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
        {{/foreach}}
        </select>
        
        {{mb_field object=$filter field=societe_id form="filter-products" autocomplete="true,2,50,false,true" 
                   style="width: 13em;"}}
        
        <input type="text" name="keywords" value="{{$keywords}}" />
        
        <button type="button" class="search notext" onclick="this.form.onsubmit()">{{tr}}Filter{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="$(this.form).clear(false); this.form.onsubmit();"></button>
      </form>

      <div id="list-products"></div>
    </td>
    
    
    <td class="halfPane">
      <a class="button new" href="?m={{$m}}&amp;tab=vw_idx_product&amp;product_id=0">{{tr}}CProduct-title-create{{/tr}}</a>
      
      <form name="edit_product" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_product_aed" />
	    <input type="hidden" name="product_id" value="{{$product->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $product->_id}}
          <th class="title modify text" colspan="2">{{$product->name}}</th>
          {{else}}
          <th class="title text" colspan="2">{{tr}}CProduct-title-create{{/tr}}</th>
          {{/if}}
        </tr>   
        <tr>
          <th style="width: 1%;">{{mb_label object=$product field="name"}}</th>
          <td>{{mb_field object=$product field="name" size=50}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="category_id"}}</th>
          <td><select name="category_id" class="{{$product->_props.category_id}}">
            <option value="">&mdash; {{tr}}CProductCategory.select{{/tr}}</option>
            {{foreach from=$list_categories item=curr_category}}
              <option value="{{$curr_category->_id}}" {{if $product->category_id == $curr_category->_id || $list_categories|@count==1}} selected="selected" {{/if}} >
              {{$curr_category->_view}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="societe_id"}}</th>
          <td><select name="societe_id" class="{{$product->_props.societe_id}}">
            <option value="">&mdash; {{tr}}CSociete.select{{/tr}}</option>
            {{foreach from=$list_potential_manufacturers item=curr_societe}}
              <option value="{{$curr_societe->_id}}" {{if $product->societe_id == $curr_societe->_id || $list_societes|@count==1}} selected="selected" {{/if}} >
              {{$curr_societe->_view}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="code"}}</th>
          <td>{{mb_field object=$product field="code"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="description"}}</th>
          <td>{{mb_field object=$product field="description"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="classe_comptable"}}</th>
          <td>{{mb_field object=$product field="classe_comptable"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="renewable"}}</th>
          <td>{{mb_field object=$product field="renewable"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="cancelled"}}</th>
          <td>{{mb_field object=$product field="cancelled"}}</td>
        </tr>
        <tr>
          <th colspan="2" class="title" style="font-size: 1em;">{{tr}}CProduct-packaging{{/tr}}</th>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="quantity"}}</th>
          <td>{{mb_field object=$product field="quantity"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="item_title"}}</th>
          <td>{{mb_field object=$product field="item_title" form="edit_product"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="unit_quantity"}}</th>
          <td>{{mb_field object=$product field="unit_quantity"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="unit_title"}}</th>
          <td>{{mb_field object=$product field="unit_title" form="edit_product"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$product field="packaging"}}</th>
          <td>{{mb_field object=$product field="packaging" form="edit_product"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            {{if $product->_id}}
            <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$product->_view|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
            
            <!-- purge: a supprimer pour le 27/01/2010 -->
            <input type="hidden" name="_purge" value="0" />
            <script type="text/javascript">
             confirmPurge = function(form) {
               if (confirm("ATTENTION : Vous êtes sur le point de supprimer un produit, ainsi que tous les objets qui s'y rattachent")) {
                 form._purge.value = 1;
                 confirmDeletion(form,  {
                   typeName:'le produit',
                   objName:'{{$product->_view|smarty:nodefaults|JSAttribute}}'
                 } );
               }
             }
            </script>
            <button type="button" class="cancel" onclick="confirmPurge(this.form)">
              {{tr}}Purge{{/tr}}
            </button>
            <!-- /purge -->
            
            {{else}}
            <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
      
      {{if $product->_id}}
      <ul class="control_tabs" id="tabs-stocks-references">
        <li><a href="#tab-stocks">{{tr}}CProductStock{{/tr}}</a></li>
        <li><a href="#tab-references">{{tr}}CProduct-back-references{{/tr}}</a></li>
      </ul>
      <hr class="control_tabs" />
      
      <div id="tab-stocks" style="display: none;">
        <table class="tbl">
          <tr>
            <th></th>
            <th>{{tr}}CProductStockGroup-quantity{{/tr}}</th>
            <th>{{tr}}CProductStockGroup-bargraph{{/tr}}</th>
          </tr>
          
          {{assign var=_stock_group value=$product->_ref_stock_group}}
          <tr>
            <td>
              <a href="?m={{$m}}&amp;tab=vw_idx_stock_group&amp;stock_id={{$_stock_group->_id}}">
                Etablissement
              </a>
            </td>
            
            {{if $product->_ref_stock_group->_id}}
              <td>{{$_stock_group->quantity}}</td>
              <td>{{include file="inc_bargraph.tpl" stock=$product->_ref_stock_group}}</td>
            {{else}}
              <td>{{tr}}CProductStockGroup.none{{/tr}}</td>
              <td>
                <button class="new" type="button" onclick="window.location='?m=dPstock&amp;tab=vw_idx_stock_group&amp;stock_id=0&amp;product_id={{$product->_id}}'">
                  {{tr}}CProductStockGroup.create{{/tr}}
                </button>
              </td>
            {{/if}}
          </tr>
          <tr>
            <th class="category" colspan="3">{{tr}}CProduct-back-stocks_service{{/tr}}</th>
          </tr>
          {{foreach from=$product->_ref_stocks_service item=curr_stock}}
            <tr>
              <td>
                <a href="?m={{$m}}&amp;tab=vw_idx_stock_service&amp;stock_service_id={{$curr_stock->_id}}">
                  {{$curr_stock->_ref_service}}
                </a>
              </td>
              <td>{{$curr_stock->quantity}}</td>
              <td>{{include file="inc_bargraph.tpl" stock=$product->_ref_stock_group}}</td>
            </tr>
          {{foreachelse}}
            <tr>
              <td colspan="3">{{tr}}CProductStockService.none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
      </div>
      
      <table class="tbl" id="tab-references" style="display: none;">
        <tr>
           <th style="width: 0.1%;">{{mb_title class=CProductReference field=code}}</th>
           <th>{{mb_title class=CProductReference field=societe_id}}</th>
           <th>{{mb_title class=CProductReference field=supplier_code}}</th>
           <th>{{mb_title class=CProductReference field=quantity}}</th>
           <th>{{mb_title class=CProductReference field=price}}</th>
           <th>{{mb_title class=CProductReference field=_unit_price}}</th>
         </tr>
         {{foreach from=$product->_ref_references item=curr_reference}}
         <tr>
           <td>
             <a href="?m=dPstock&amp;tab=vw_idx_reference&amp;reference_id={{$curr_reference->_id}}">
               <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_reference->_guid}}')">
                 {{if $curr_reference->code}}
                   {{mb_value object=$curr_reference field=code}}
                 {{else}}
                   [Aucun code]
                 {{/if}}
               </span>
             </a>
           </td>
           <td>
             <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_reference->_ref_societe->_guid}}')">
               {{mb_value object=$curr_reference field=societe_id}}
             </span>
           </td>
           <td>{{mb_value object=$curr_reference field=supplier_code}}</td>
           <td>{{mb_value object=$curr_reference field=quantity}}</td>
           <td>{{mb_value object=$curr_reference field=price}}</td>
           <td>{{mb_value object=$curr_reference field=_unit_price}}</td>
         </tr>
         {{foreachelse}}
         <tr>
           <td colspan="10">{{tr}}CProductReference.none{{/tr}}</td>
         </tr>
         {{/foreach}}
         
          <tr>
            <td colspan="10">
              <button class="new" type="button" onclick="window.location='?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id=0&amp;product_id={{$product->_id}}'">
                Nouvelle référence pour ce produit
              </button>
            </td>
          </tr>
       </table>
     
       {{/if}}
    </td>
  </tr>
</table>