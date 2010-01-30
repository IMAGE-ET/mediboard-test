{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPstock script=product_selector}}

<script type="text/javascript">
Main.add(function () {
  updateUnitQuantity(getForm("edit_reference").quantity, "equivalent_quantity");
  updateUnitQuantity(getForm("edit_reference").mdq, "equivalent_quantity_mdq");
  filterReferences(getForm("filter-references"));
  Control.Tabs.create("reference-tabs", true);
});

function updateUnitQuantity(element, view) {
  $(view).update('('+(element.value * element.form._unit_quantity.value)+' '+element.form._unit_title.value+')');
}

ProductSelector.init = function(){
  this.sForm      = "edit_reference";
  this.sId        = "product_id";
  this.sView      = "product_name";
  this.sQuantity  = "_unit_quantity";
  this.sUnit      = "_unit_title";
  this.sPackaging = "packaging";
  this.pop({{$reference->product_id}});
}

function changePage(start) {
  $V(getForm("filter-references").start, start);
}

function filterReferences(form) {
  var url = new Url("dPstock", "httpreq_vw_references_list");
  url.addFormData(form);
  url.requestUpdate("list-references");
  return false;
}
</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <form name="filter-references" action="?" method="post" onsubmit="return filterReferences(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="start" value="0" onchange="this.form.onsubmit()" />
        
        <select name="category_id" onchange="$V(this.form.start,0);this.form.onsubmit()">
          <option value="">&ndash; {{tr}}CProductCategory.all{{/tr}}</option>
        {{foreach from=$list_categories item=curr_category}} 
          <option value="{{$curr_category->category_id}}" {{if $category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
        {{/foreach}}
        </select>
        
        <select name="societe_id" onchange="$V(this.form.start,0);this.form.onsubmit()">
          <option value="">&ndash; Tous les distributeurs</option>
        {{foreach from=$list_societes item=curr_societe}} 
          <option value="{{$curr_societe->societe_id}}" {{if $societe_id==$curr_societe->_id}}selected="selected"{{/if}}>{{$curr_societe->name}}</option>
        {{/foreach}}
        </select>
        
        <input type="text" name="keywords" value="{{$keywords}}" size="12" onchange="$V(this.form.start,0)" />
        
        <button type="submit" class="search notext">{{tr}}Filter{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="$(this.form).clear(false); this.form.onsubmit();"></button>
      </form>

      <div id="list-references"></div>
    </td>


    <td class="halfPane">
      {{if $can->edit}}
      <a class="button new" href="?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id=0">{{tr}}CProductReference-title-create{{/tr}}</a>
      <form name="edit_reference" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_reference_aed" />
	    <input type="hidden" name="reference_id" value="{{$reference->_id}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="_unit_quantity" value="{{$reference->_ref_product->_unit_quantity}}" onchange="updateUnitQuantity(this.form.quantity, 'equivalent_quantity')" />
      <input type="hidden" name="_unit_title" value="{{$reference->_ref_product->_unit_title}}" />
      <table class="form">
        <tr>
          {{if $reference->_id}}
          <th class="title modify text" colspan="2">{{$reference->_view}}</th>
          {{else}}
          <th class="title" colspan="2">{{tr}}CProductReference-title-create{{/tr}}</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="societe_id"}}</th>
          <td>
            {{mb_field object=$reference field=societe_id form="edit_reference" autocomplete="true,1,50,false,true" 
                       style="width: 15em;"}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="product_id"}}</th>
          <td>
            <input type="hidden" name="product_id" value="{{$reference->product_id}}" class="{{$reference->_props.product_id}}" />
            <input type="text" name="product_name" value="{{$reference->_ref_product->name}}" size="40" readonly="readonly" ondblclick="ProductSelector.init()" />
            <button class="search notext" type="button" onclick="ProductSelector.init()">{{tr}}Search{{/tr}}</button>
            <button class="edit notext" type="button" onclick="location.href='?m=dPstock&amp;tab=vw_idx_product&amp;product_id='+this.form.product_id.value">{{tr}}Edit{{/tr}}</button>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="code"}}</th>
          <td>{{mb_field object=$reference field="code"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="supplier_code"}}</th>
          <td>{{mb_field object=$reference field="supplier_code"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="quantity"}}</th>
          <td>
            {{mb_field object=$reference field="quantity" increment=1 form=edit_reference min=1 size=4 onchange="updateUnitQuantity(this, 'equivalent_quantity')"}}
            <input type="text" name="packaging" readonly="readonly" value="{{$reference->_ref_product->packaging}}" style="border: none; background: transparent; width: 5em; color: inherit;"/>
            <span id="equivalent_quantity"></span>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="mdq"}}</th>
          <td>{{mb_field object=$reference field="mdq" increment=1 form=edit_reference min=1 size=4 onchange="updateUnitQuantity(this, 'equivalent_quantity_mdq')"}}
            <input type="text" name="packaging" readonly="readonly" value="{{$reference->_ref_product->packaging}}" style="border: none; background: transparent; width: 5em; color: inherit;"/>
            <span id="equivalent_quantity_mdq"></span>
          </td>
        </tr>
        
        <tr>
        {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
          <th>{{mb_label object=$reference field="_cond_price"}}</th>
          <td>
            {{* <div style="float: right; display: none;">
              {{mb_label object=$reference field="price"}}
              {{mb_field object=$reference field="price" increment=1 form=edit_reference decimals=4 min=0 size=5 
                         onchange="this.form._cond_price.value = (this.value/(this.form.quantity.value || 1)).toFixed(2)"}}
            </div>
            
            {{mb_field object=$reference field="_cond_price" increment=1 form=edit_reference decimals=4 min=0 size=5 
                       onchange="this.form.price.value = this.value*this.form.quantity.value"}}
             *}}
             
            {{mb_field object=$reference field="price" hidden=true}}
            
            {{assign var=sub_quantity value=$reference->_ref_product->quantity}}
            {{mb_field object=$reference field="_unit_price" increment=1 form=edit_reference decimals=4 min=0 size=4 
                       onchange="this.form.price.value = this.value*this.form.quantity.value*$sub_quantity"}}
          </td>
        {{else}}
          <th>{{mb_label object=$reference field="price"}}</th>
          <td>
            <div style="float: right;">
              {{mb_label object=$reference field="_cond_price"}}
              {{mb_field object=$reference field="_cond_price" increment=1 form=edit_reference decimals=4 min=0 size=4 
                         onchange="this.form.price.value = this.value*this.form.quantity.value"}}
            </div>
            
            {{mb_field object=$reference field="price" increment=1 form=edit_reference decimals=4 min=0 size=4 
                       onchange="this.form._cond_price.value = (this.value/(this.form.quantity.value || 1)).toFixed(2)"}}
          </td>
        {{/if}}
        </tr>
        
        <tr>
          <th>{{mb_label object=$reference field="tva"}}</th>
          <td>{{mb_field object=$reference field="tva" increment=1 form=edit_reference decimals=1 min=0 size=2}}</td>
        </tr>
        <tr>
          <td class="button" colspan="4">
            {{if $reference->_id}}
            <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$reference->_view|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
            {{else}}
            <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
      {{/if}}
      
      {{if $reference->_id}}
      <table class="main">
        <tr>
          <td>
            <ul class="control_tabs" id="reference-tabs">
              <li>
                 {{assign var=orders_count value=$lists_objects.orders|@count}}
              	 <a href="#reference-orders" {{if !$orders_count}}class="empty"{{/if}}>
              	   {{tr}}CProductOrder{{/tr}}
									 <small>({{$orders_count}})</small>
								 </a>
							</li>
              <li>
                 {{assign var=receptions_count value=$lists_objects.receptions|@count}}
                 <a href="#reference-receptions" {{if !$receptions_count}}class="empty"{{/if}}>
                   {{tr}}CProductReception{{/tr}}
                   <small>({{$receptions_count}})</small>
                 </a>
              <li>
            </ul>
            
            <hr class="control_tabs" />
          </td>
        </tr>
        
        <tr id="reference-orders" style="display: block;">
          <td>
            <table class="main tbl">
              <tr>
                <th>{{mb_title class=CProductOrder field=order_number}}</th>
                <th>{{mb_title class=CProductOrder field=date_ordered}}</th>
                <th>{{mb_title class=CProductOrder field=_status}}</th>
              </tr>
							
              {{foreach from=$lists_objects.orders item=_order}}
              <tr>
                <td>
                  <strong onmouseover="ObjectTooltip.createEx(this, '{{$_order->_guid}}')">
                    {{mb_value object=$_order field=order_number}}
                  </strong>
                </td>
                <td>{{mb_value object=$_order field=date_ordered}}</td>
                <td>{{mb_value object=$_order field=_status}}</td>
              </tr>
              {{foreachelse}}
              <tr>
                <td colspan="10"><em>{{tr}}CProductOrder.none{{/tr}}</em></td>
              </tr>
              {{/foreach}}
            </table>
          </td>
        </tr>
        
        <tr id="reference-receptions" style="display: block;">
          <td>
            <table class="main tbl">
              <tr>
                <th></th>
                <th>Date de réception</th>
              </tr>
              {{foreach from=$lists_objects.receptions item=_reception}}
              <tr>
                <td>
                  <strong onmouseover="ObjectTooltip.createEx(this, '{{$_reception->_guid}}')">
                    {{$_reception->reference}}
                  </strong>
                </td>
                <td>{{mb_value object=$_reception field=date}}</td>
              </tr>
              {{foreachelse}}
              <tr>
                <td colspan="10"><em>{{tr}}CProductReception.none{{/tr}}</em></td>
              </tr>
              {{/foreach}}
            </table>
          </td>
        </tr>
        
        <tr id="reference-bills" style="display: none;">
          <td>
            <table class="main tbl">
              <tr>
                <td colspan="10">Aucune facture</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      {{/if}}
      
    </td>
  </tr>
</table>
