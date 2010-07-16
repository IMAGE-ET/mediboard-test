{{assign var=debug value=false}}

<script type="text/javascript">
selectProduct = function(element) {
  element.checked = true;
  
  var container = element.up('div');
  container.addUniqueClassName('selected');
  $$('div.lots').invoke('hide');
  container.down('.lots').show();
  
  var lots = container.select('input.lot');
  if (lots.length == 1) {
    lots[0].checked = true;
  }
}

Main.add(function(){
  var products = $$('input[name=product_id]');
  var lots = $$('div.lots input[type=radio]');
  
  if (products.length == 1) {
    selectProduct(products[0]);
  }
  
  if (products.length == 1 && lots.length == 1) {
    var url = new Url("dmi", "httpreq_apply_dmi");
    url.addParam("lot_id", lots[0].value);
    url.addParam("dmi_id", lots[0].value);
    url.requestUpdate("apply-dmi");
  }
  else {
    $("apply-dmi").update();
  }
});
</script>

{{if $debug}}
  <h1>{{$parsed.type}}</h1>

  <table class="tbl">
  {{foreach from=$parsed.comp key=key item=value}}
    <tr>
      <th style="width: 0.1%;">{{$key}}</th>
      <td>{{$value}}</td>
    </tr>
  {{/foreach}}
  </table>

  <h1>Résultats</h1>
{{/if}}

{{foreach from=$products item=_product}}
  <div class="product" style="padding: 3px;">
    <label style="font-size: 1.3em;">
      <input type="radio" name="product_id" value="{{$_product->_id}}" onclick="selectProduct(this)" />
      <span style="font-weight: bold;">{{$_product}}</span>
      {{if $_product->societe_id}}
        - <small>{{$_product->_ref_societe}}</small>
      {{/if}}
    </label>
    
    <div class="lots" style="display: none; padding-left: 2em;">
      {{foreach from=$_product->_lots item=_lot}}
        <div style="padding:5px;">
          <label>
            <input type="radio" class="lot" name="_lot[{{$_product->_id}}]" value="{{$_lot->_id}}" {{if $_lot->_selected}}checked="checked"{{/if}} />
            <strong>{{$_lot->code}}</strong> &mdash; {{mb_value object=$_lot field=lapsing_date}}
          </label>
        </div>
      {{/foreach}}
      
      <hr />
      
      <button class="new" type="button" onclick="$(this).next('div').toggle()">
        Nouveau lot
      </button>
      
      {{assign var=product_id value=$_product->_id}}
      
      <div style="display: none;">
        <form name="create-lot-{{$_product->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
          <table class="tbl">
            <tr>
              <td>
                <label for="_reference_id" style="display: none;">
                  {{if $_product->_ref_references|@count}}Date/Référence{{else}}Fournisseur{{/if}}
                </label>
                <select name="_reference_id" class="notNull">
                  {{if $_product->_ref_references|@count}}
                    {{foreach from=$_product->_ref_references item=_reference}}
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
                {{mb_field object=$_product->_new_lot field=code size=15 prop="str notNull" class="barcode"}}
              </td>
              <td>
                <label for="lapsing_date" style="display: none;">{{tr}}CProductOrderItemReception-lapsing_date{{/tr}}</label>
                {{mb_field object=$_product->_new_lot field=lapsing_date prop="str notNull" class="barcode" size=10}}
              </td>
              {{* <td>{{mb_field class=CProductOrderItemReception field=date register=true form=searchProductOrderItemReception}}</td> *}}
              <td></td>
            </tr>
          </table>
        </form>
      </div>
    </div>
  </div>
{{foreachelse}}
  <div class="small-info">
    Aucun lot n'a pu être trouvé
  </div>
{{/foreach}}

<hr />

<div id="apply-dmi"></div>

{{* 
        <form name="create-dmi-{{$_product->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
          <input type="hidden" name="m" value="dmi" />
          <input type="hidden" name="dosql" value="do_dmi_aed" />
          <input type="hidden" name="callback" value="updateProductId" />
          <input type="hidden" name="_lot_number" value="{{$dmi->_lot_number}}" />
          <input type="hidden" name="_lapsing_date" value="{{$dmi->_lapsing_date}}" />
          <input type="hidden" name="in_livret" value="1" />
          
          <table class="main form">
            <tr>
              <th class="category" colspan="2">Nouveau lot de {{$_product}}</th>
            </tr>
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
              <td>{{mb_field object=$dmi field=category_id form="create-dmi-$product_id" autocomplete="true,1,50,true,true"}}</td>
            </tr>
            <tr>
              <th></th>
              <td>
                <button type="submit" class="save">{{tr}}Save{{/tr}}</button>
              </td>
            </tr>
          </table>
 *}}
