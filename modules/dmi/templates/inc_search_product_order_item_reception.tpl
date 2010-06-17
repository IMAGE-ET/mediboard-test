{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

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
    
<form name="searchProductOrderItemReception" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function(){search_product_order_item_reception(getForm('dmi_delivery_by_product'))}})">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_order_item_reception_aed" />
  <input type="hidden" name="quantity" value="1" />
  <input type="hidden" name="date" value="now" />
  
  <h3>{{$product}}</h3>
  
  {{if $list|@count == 0}}
    <div class="small-info text">Aucun article enregistré, veuillez le créer ci-dessous</div>
  {{else}}
    <script type="text/javascript">
    Main.add(function(){
      var field = getForm("searchProductOrderItemReception")._search_lot;
      
      field.focus();
      
      field.observe("keyup", function(e) {
        var table = $("lot-list");
        table.select("tr").invoke("show");
        
        var term = $V(Event.element(e));
        if (!term) return;
        
        table.select(".CProductOrderItemReception-view").each(function(e) {
          if (!e.innerHTML.like(term)) {
            e.up("tr").hide();
          }
        });
      });
      
      field.observe("keypress", function(e) {
        if (Event.key(e) != 13) return;
        
        Event.stop(e);
        
        var visible = $("lot-list").select(".CProductOrderItemReception-view").filter(function(e){
          return e.up("tr").visible();
        });
        console.debug(visible);
        if (visible.length)
          visible[0].up("tr").down("button").focus();
        else
          Event.element(e).select();
      });
    });
    </script>
  {{/if}}
  
  {{if $list_references|@count == 0}}
    <div class="small-info text">
      Aucune référence n'est disponible, veuillez choisir le <strong>fournisseur</strong>
      de ce DMI pour créér une référence et un lot
    </div>
  {{/if}}
  
  {{if $list|@count == 0 || $list_references|@count == 0}}
    <script type="text/javascript">
    Main.add(function(){
      getForm("searchProductOrderItemReception").code.focus();
    });
    </script>
  {{/if}}
  
  {{if $list|@count > 0}}
  <label>
    Code lot
    <input type="text" size="20" name="_search_lot" />
  </label>
  {{/if}}
  
  <table class="tbl">
  	<tr>
      <th style="width: 0.1%;"></th>
      <th>{{if $list_references|@count}}Date/Référence{{else}}Fournisseur{{/if}}</th>
  		<th>{{mb_title class=CProductOrderItemReception field=code}}</th>
      <th>{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
  	</tr>
  	{{foreach from=$list item=_order_item_reception}}
    <tbody class="hoverable" id="lot-list">
      <tr>
        <td>
          <button type="button" class="tick notext" onclick="return search_product_code('{{$_order_item_reception->_ref_order_item->_ref_reference->_ref_product->code}} {{$_order_item_reception->code}}')">
            {{tr}}Select{{/tr}}
          </button>
        </td>
        <td>{{mb_value object=$_order_item_reception field=date}}</td>
    	  <td class="CProductOrderItemReception-view">{{mb_value object=$_order_item_reception field=code}}</td>
        <td>{{mb_value object=$_order_item_reception field=lapsing_date}}</td>
      </tr>
    </tbody>
  	{{/foreach}}
    <tr>
      <td>
        <button type="submit" class="tick notext">
          {{tr}}Select{{/tr}}
        </button>
      </td>
      <td>
        <select name="_reference_id" class="notNull" style="width: 10em;">
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
      <td>{{mb_field class=CProductOrderItemReception field=code size=15 prop="str notNull"}}</td>
      <td>{{mb_field class=CProductOrderItemReception field=lapsing_date prop="str notNull" size=10}}</td>
      {{* <td>{{mb_field class=CProductOrderItemReception field=date register=true form=searchProductOrderItemReception}}</td> *}}
    </tr>
  	<tr>
  	  <td id="product_reception_by_product" colspan="10"></td>
  	</tr>
  </table>
</form>