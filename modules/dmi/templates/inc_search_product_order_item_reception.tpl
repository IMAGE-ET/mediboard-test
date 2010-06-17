{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="searchProductOrderItemReception" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function(){search_product_order_item_reception(getForm('dmi_delivery_by_product'))}})">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_order_item_reception_aed" />
  <input type="hidden" name="quantity" value="1" />
  <input type="hidden" name="date" value="now" />
  
  {{if $list_references|@count == 0}}
    <div class="small-info text">
      Aucune référence n'est disponible, veuillez choisir le fournisseur de ce DMI pour créér une référence et un lot
    </div>
  {{/if}}
        
  <table class="tbl">
  	<tr>
      <th style="width: 0.1%;"></th>
  		<th>{{mb_title class=CProductOrderItemReception field=code}}</th>
      <th>{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
  		<th>{{if $list_references|@count}}Date/Référence{{else}}Fournisseur{{/if}}</th>
  	</tr>
  	{{foreach from=$list item=_order_item_reception}}	
  	<tr>
      <td>
        <button type="button" class="tick notext" onclick="return search_product_code('{{$_order_item_reception->_ref_order_item->_ref_reference->_ref_product->code}}','{{$_order_item_reception->code}}')">
          {{tr}}Select{{/tr}}
        </button>
      </td>
  	  <td>{{mb_value object=$_order_item_reception field=code}}</td>
      <td>{{mb_value object=$_order_item_reception field=lapsing_date}}</td>
  	  <td>{{mb_value object=$_order_item_reception field=date}}</td>
    </tr>
  	{{/foreach}}
    <tr>
      <td>
        <button type="button" class="tick notext">
          {{tr}}Select{{/tr}}
        </button>
      </td>
      <td>{{mb_field class=CProductOrderItemReception field=code size=10 prop="str notNull"}}</td>
      <td>{{mb_field class=CProductOrderItemReception field=lapsing_date prop="str notNull mask|99/99/9999" size=10 form=searchProductOrderItemReception}}</td>
      {{* <td>{{mb_field class=CProductOrderItemReception field=date register=true form=searchProductOrderItemReception}}</td> *}}
      <td>
        <select name="_reference_id" class="notNull">
          {{if $list_references|@count}}
            {{foreach from=$list_references item=_reference}}
              <option value="{{$_reference->_id}}">{{$_reference->_ref_societe}} ({{$_reference->quantity}})</option>
            {{/foreach}}
          {{else}}
            <option disabled="disabled" selected="selected"> &ndash; Fournisseur </option>
            {{foreach from=$list_societes item=_societe}}
              <option value="{{$_societe->_id}}-{{$product->_id}}">{{$_societe}}</option>
            {{/foreach}}
          {{/if}}
        </select>
      </td>
    </tr>
  	<tr>
  	  <td id="product_reception_by_product" colspan="10"></td>
  	</tr>
  </table>
</form>