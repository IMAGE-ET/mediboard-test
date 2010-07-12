{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $product->_id}}

<script type="text/javascript">
Main.add(function(){
  var form = getForm("searchProductOrderItemReception");
  
  form.code.observe("keypress", function(e){
    if (Event.key(e) != 13) return;
    Event.element(e).form.lapsing_date.focus();
    Event.stop(e);
  });
  
  form.lapsing_date.observe("keypress", function(e){
    if (Event.key(e) != 13) return;
    
    var element = Event.element(e);
    var date = Barcode.parseDate(element.value);
    
    if (date) {
      element.value = date;
      element.form._reference_id.focus();
    }
    else {
      element.value = "";
      element.select();
    }
    
    Event.stop(e);
  });
});
</script>

<hr />
<p style="font-weight: bold; font-size: 1.4em;">
  [{{$product->code}}] - {{$product}}
</p>
{{$product->description}}

<hr />
  
<form name="searchProductOrderItemReception" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function(){search_product_order_item_reception(getForm('dmi_delivery_by_product'))}})">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_order_item_reception_aed" />
  <input type="hidden" name="quantity" value="1" />
  <input type="hidden" name="date" value="now" />
  
  {{if $list|@count}}
    <script type="text/javascript">
      Main.add(function(){
        var field = getForm("searchProductOrderItemReception")._search_lot;
        
        field.focus();
        field.observe("keyup", filterByLotNumber);
        field.observe("keypress", function(e) {
          if (Event.key(e) != 13) return;
          Event.stop(e);
          selectAvailableLine(e);
        });
      });
    </script>
  {{/if}}
  
  {{if $list_references|@count == 0 || $list|@count == 0}}
    <div class="small-info text">
      Aucun lot n'est actuellement enregistré, veuillez renseigner les points suivants:
      <ul>
        {{if $list_references|@count == 0}}
          <li>aucune référence n'est disponible, veuillez choisir le <strong>laboratoire</strong></li>
        {{/if}}
        
        {{if $list|@count == 0}}
          <li>aucun lot enregistré, veuillez le créer ci-dessous</li>
        {{/if}}
      </ul>
    </div>
  {{/if}}
  
  {{if $product->_id && ($list|@count == 0 || $list_references|@count == 0)}}
    <script type="text/javascript">
    Main.add(function(){
      getForm("searchProductOrderItemReception").code.focus();
    });
    </script>
  {{/if}}
  
  {{if $list|@count > 0}}
  <label>
    Code lot
    <input type="text" size="20" name="_search_lot" class="barcode" />
  </label>
  {{/if}}
  
  <table class="tbl">
  	<tr>
      <th style="width: 0.1%;"></th>
      <th>{{if $list_references|@count}}Date/Référence{{else}}Fournisseur{{/if}}</th>
  		<th>{{mb_title class=CProductOrderItemReception field=code}}</th>
      <th>{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
      <th>{{mb_title class=CProductOrderItemReception field=quantity}}</th>
  	</tr>
    <tbody id="lot-list">
  	{{foreach from=$list item=_order_item_reception}}
      <tr class="hoverable">
        <td>
          <button type="button" class="tick notext" onfocus="$(this).up('tr').addClassName('selected')" onblur="$(this).up('tr').removeClassName('selected')"
                  onclick="return search_product_code('{{$_order_item_reception->_ref_order_item->_ref_reference->_ref_product->code}} {{$_order_item_reception->code}}')">
            {{tr}}Select{{/tr}}
          </button>
        </td>
        <td>{{mb_value object=$_order_item_reception field=date}}</td>
    	  <td class="CProductOrderItemReception-view">{{mb_value object=$_order_item_reception field=code}}</td>
        <td>{{mb_value object=$_order_item_reception field=lapsing_date}}</td>
        <td>{{mb_value object=$_order_item_reception field=quantity}}</td>
      </tr>
  	{{/foreach}}
    </tbody>
    <tr>
      <td>
        <button type="submit" class="tick notext">
          {{tr}}Select{{/tr}}
        </button>
      </td>
      <td>
        <label for="_reference_id" style="display: none;">
          {{if $list_references|@count}}Date/Référence{{else}}Fournisseur{{/if}}
        </label>
        <select name="_reference_id" class="notNull">
          {{if $list_references|@count}}
            {{foreach from=$list_references item=_reference}}
              <option value="{{$_reference->_id}}">{{$_reference->_ref_societe}} ({{$_reference->quantity}})</option>
            {{/foreach}}
          {{else}}
            <option disabled="disabled" selected="selected"> &ndash; Fournisseur </option>
            {{foreach from=$list_societes item=_societe}}
              <option value="{{$_societe->_id}}-{{$product->_id}}" {{if $_societe->_id == $product->societe_id}}selected="selected"{{/if}}>{{$_societe}}</option>
            {{/foreach}}
          {{/if}}
        </select>
      </td>
      <td>
        <label for="code" style="display: none;">{{tr}}CProductOrderItemReception-code{{/tr}}</label>
        {{mb_field object=$lot field=code size=15 prop="str notNull" class="barcode"}}
      </td>
      <td>
        <label for="lapsing_date" style="display: none;">{{tr}}CProductOrderItemReception-lapsing_date{{/tr}}</label>
        {{mb_field object=$lot field=lapsing_date prop="str notNull" class="barcode" size=10}}
      </td>
      {{* <td>{{mb_field class=CProductOrderItemReception field=date register=true form=searchProductOrderItemReception}}</td> *}}
      <td></td>
    </tr>
  </table>
  
  <div id="product_reception_by_product"></div>
</form>

{{else}}
  <div class="small-info">
    Produit non trouvé avec le code <strong>{{$keywords}}</strong>.
    <br />Veuillez renseigner son libellé ainsi que sa référence produit.
  </div>
  
  <script type="text/javascript">
    updateProductId = function(id, object){
      var form = getForm('dmi_delivery_by_product');
      $V(form._view, object.code);
      $V(form.product_id, "");
      dmiAutocompleter.activate.bind(dmiAutocompleter)();
      //search_product_order_item_reception(form);
    }
  </script>
  
  <form name="create-dmi" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
    <input type="hidden" name="m" value="dmi" />
    <input type="hidden" name="dosql" value="do_dmi_aed" />
    <input type="hidden" name="callback" value="updateProductId" />
    <input type="hidden" name="_lot_number" value="{{$dmi->_lot_number}}" />
    <input type="hidden" name="_lapsing_date" value="{{$dmi->_lapsing_date}}" />
    
    {{mb_key object=$dmi}}
    {{mb_field object=$dmi field=in_livret hidden=true}}
    
    <table class="main form">
      <tr>
        <th>{{mb_label object=$dmi field=nom}}</th>
        <td>{{mb_field object=$dmi field=nom}}</td>
      </tr>
      <tr>
        <th>Référence produit</th>
        <td>{{mb_field object=$dmi field=code}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$dmi field=type}}</th>
        <td>{{mb_field object=$dmi field=type}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$dmi field=_scc_code}}</th>
        <td>{{mb_field object=$dmi field=_scc_code}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$dmi field=category_id}}</th>
        <td>{{mb_field object=$dmi field=category_id form="create-dmi" autocomplete="true,1,50,true,true"}}</td>
      </tr>
      <tr>
        <th></th>
        <td>
          <button type="submit" class="save">{{tr}}Save{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
  
  <hr />
{{/if}}
