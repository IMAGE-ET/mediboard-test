{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPstock script=product_selector}}
{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
Main.add(function () {
  updateUnitQuantity(getForm("edit_reference"), "equivalent_quantity");
  filterReferences(getForm("filter-references"));
  Control.Tabs.create("reference-tabs", true);
});

function updateUnitQuantity(form, view) {
  $(view).update('('+(form.quantity.value * form._unit_quantity.value)+' '+form._unit_title.value+')');
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
        
        <select name="category_id" onchange="this.form.onsubmit()">
          <option value="0">&ndash; {{tr}}CProductCategory.all{{/tr}}</option>
        {{foreach from=$list_categories item=curr_category}} 
          <option value="{{$curr_category->category_id}}" {{if $category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
        {{/foreach}}
        </select>
        
        <select name="societe_id" onchange="this.form.onsubmit()">
          <option value="0">&ndash; Tous les distributeurs</option>
        {{foreach from=$list_societes item=curr_societe}} 
          <option value="{{$curr_societe->societe_id}}" {{if $societe_id==$curr_societe->_id}}selected="selected"{{/if}}>{{$curr_societe->name}}</option>
        {{/foreach}}
        </select>
        
        <input type="text" name="keywords" value="" size="12" />
        
        <button type="button" class="search notext" onclick="this.form.onsubmit()">{{tr}}Filter{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="this.form.reset()"></button>
      </form>

      <div id="list-references"></div>
    </td>


    <td class="halfPane">
      {{if $can->edit}}
      <a class="button new" href="?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id=0">{{tr}}CProductReference.create{{/tr}}</a>
      <form name="edit_reference" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_reference_aed" />
	    <input type="hidden" name="reference_id" value="{{$reference->_id}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="_unit_quantity" value="{{$reference->_ref_product->_unit_quantity}}" onchange="updateUnitQuantity(this.form, 'equivalent_quantity')" />
      <input type="hidden" name="_unit_title" value="{{$reference->_ref_product->_unit_title}}" />
      <table class="form">
        <tr>
          {{if $reference->_id}}
          <th class="title modify text" colspan="2">{{$reference->_view}}</th>
          {{else}}
          <th class="title" colspan="2">{{tr}}CProductReference.create{{/tr}}</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="societe_id"}}</th>
          <td>
            <select name="societe_id" class="{{$reference->_props.societe_id}}">
              <option value="">&mdash; {{tr}}CSociete.select{{/tr}}</option>
              {{foreach from=$list_societes item=curr_societe}}
                <option value="{{$curr_societe->societe_id}}" {{if $reference->societe_id == $curr_societe->_id || $list_societes|@count==1}} selected="selected" {{/if}} >
                {{$curr_societe->_view}}
                </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="product_id"}}</th>
          <td>
            <input type="hidden" name="product_id" value="{{$reference->product_id}}" class="{{$reference->_props.product_id}}" />
            <input type="text" name="product_name" value="{{$reference->_ref_product->name}}" size="40" readonly="readonly" ondblclick="ProductSelector.init()" />
            <button class="search" type="button" onclick="ProductSelector.init()">{{tr}}Search{{/tr}}</button>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="code"}}</th>
          <td>{{mb_field object=$reference field="code"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="quantity"}}</th>
          <td>
            {{mb_field object=$reference field="quantity" increment=1 form=edit_reference min=1 size=4 onchange="updateUnitQuantity(this.form, 'equivalent_quantity')"}}
            <input type="text" name="packaging" readonly="readonly" value="{{$reference->_ref_product->packaging}}" style="border: none; background: transparent; width: 5em; color: inherit;"/>
            <span id="equivalent_quantity"></span>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="price"}}</th>
          <td>{{mb_field object=$reference field="price" increment=1 form=edit_reference decimals=4 min=0 size=8}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="mdq"}}</th>
          <td>{{mb_field object=$reference field="mdq" increment=1 form=edit_reference min=1 size=4}}</td>
        </tr>
        <tr>
          <td class="button" colspan="4">
            {{if $reference->_id}}
            <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
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
              <li><a href="#reference-orders">Bons de commande</a></li>
              <li><a href="#reference-receptions">Bons de réceptions</a></li>
              <li><a href="#reference-bills">Facture</a></li>
            </ul>
            
            <hr class="control_tabs" />
          </td>
        </tr>
        
        <tr id="reference-orders" style="display: none;">
          <td>
            <table class="main tbl">
              <tr>
                <th></th>
                <th>Date de commande</th>
              </tr>
              {{foreach from=$lists_objects.orders item=_order}}
              <tr>
                <td>
                  <strong onmouseover="ObjectTooltip.createEx(this, '{{$_order->_guid}}')">
                    {{$_order->order_number}}
                  </strong>
                </td>
                <td>{{mb_value object=$_order field=date_ordered}}</td>
              </tr>
              {{foreachelse}}
              <tr>
                <td colspan="10">{{tr}}CProductOrder.none{{/tr}}</td>
              </tr>
              {{/foreach}}
            </table>
          </td>
        </tr>
        
        <tr id="reference-receptions" style="display: none;">
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
                <td colspan="10">{{tr}}CProductReception.none{{/tr}}</td>
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
