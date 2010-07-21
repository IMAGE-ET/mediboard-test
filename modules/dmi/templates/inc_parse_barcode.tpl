{{assign var=debug value=true}}

<style type="text/css">
div.lots .lot:hover {
  background-color: #ddd;
  cursor: pointer;
}

div.product:hover {
  outline: 2px solid #ccc;
}
</style>

<script type="text/javascript">
selectProduct = function(element, event) {
  if (event) Event.stop(event);
  element.checked = true;
  
  var applyDMI = getForm("apply-dmi");
  if (applyDMI && applyDMI.product_id.value == element.value) return;
  
  $("apply-dmi").update();
  
  var container = element.up('div.product');
  container.addUniqueClassName('selected');
  $$('div.lots').invoke('hide');
  container.down('.lots').show();
  
  var lots = container.select('input.lot');
  if (lots.length == 1) {
    lots[0].checked = true;
    selectLot(lots[0]);
  }
}

selectLot = function(element, event){
  if (event) Event.stop(event);
  element.checked = true;
  
  var applyDMI = getForm("apply-dmi");
  if (applyDMI && applyDMI.order_item_reception_id.value == element.value) return;
  
  var url = new Url("dmi", "httpreq_apply_dmi");
  url.addParam("lot_id", element.value);
  url.addParam("dmi_id", element.value);
  url.addParam("prescription_id", window.DMI_prescription_id);
  url.addParam("operation_id", window.DMI_operation_id);
  url.requestUpdate("apply-dmi");
}

Main.add(function(){
  var products = $$('input[name=product_id]');
  var lots = $$('div.lots input[type=radio]');
  
  if (products.length == 1) {
    selectProduct(products[0]);
  }
  
  if (products.length == 1 && lots.length == 1) {
    selectLot(lots[0]);
  }
  else {
    $("apply-dmi").update();
  }
});
</script>

<div style="float: right; width: 7px; height: 7px; background-color: #fc3;" onclick="$('debug-barcode').toggle()"></div>

{{if $debug}}
  <div id="debug-barcode" style="display: none; border: 1px dotted orange; padding: 0.5em;">
    <h1>{{$parsed.type}}</h1>
  
    <table class="tbl">
    {{foreach from=$parsed.comp key=key item=value}}
      <tr>
        <th style="width: 0.1%;">{{$key}}</th>
        <td>{{$value}}</td>
      </tr>
    {{/foreach}}
    </table>
  </div>
{{/if}}

{{foreach from=$products item=_product}}
  <div class="product" style="padding-bottom: 3px;">
    <div onclick="selectProduct($(this).down('input[name=product_id]'), event)" style="padding: 3px;">
      <label style="font-size: 1.2em;">
        <input type="radio" name="product_id" value="{{$_product->_id}}" />
        [{{$_product->code}}] &ndash; 
        <span style="font-weight: bold;">{{$_product}}</span>
        {{if $_product->societe_id}}
          - <small>{{$_product->_ref_societe}}</small>
        {{/if}}
      </label>
    </div>
    
    <div class="lots" style="display: none; padding-left: 2em;">
      {{foreach from=$_product->_lots item=_lot}}
        <div style="padding: 5px;" class="lot" onclick="selectLot($(this).down('input'), event)">
          <label>
            <input type="radio" class="lot" name="_lot[{{$_product->_id}}]" value="{{$_lot->_id}}" {{* if $_lot->_selected}}checked="checked"{{/if *}} />
            <strong>[{{$_lot->code}}]</strong> &ndash; {{mb_value object=$_lot field=lapsing_date}}
          </label>
        </div>
      {{foreachelse}}
        <div>Aucun lot n'est disponible pour ce DMI</div>
      {{/foreach}}
      
      <hr />
      
      <button class="new" type="button" onclick="$(this).next('div').toggle()">
        Nouveau lot
      </button>
      
      {{assign var=product_id value=$_product->_id}}
      
      <div style="display: none;">
        <form name="create-lot-{{$_product->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: submitBarcode})">
          <input type="hidden" name="m" value="dPstock" />
          <input type="hidden" name="dosql" value="do_order_item_reception_aed" />
          <input type="hidden" name="quantity" value="1" />
          <input type="hidden" name="date" value="now" />
          
          <table class="tbl">
            <tr>
              <th><label for="_reference_id">Référence / Fournisseur</label></th>
              <th><label for="code">{{tr}}CProductOrderItemReception-code{{/tr}}</label></th>
              <th><label for="lapsing_date">{{tr}}CProductOrderItemReception-lapsing_date{{/tr}}</label></th>
              <th style="width: 0.1%;"></th>
            </tr>
            <tr>
              <td style="text-align: center;">
                <select name="_reference_id" class="notNull" style="width: 12em;">
                  {{if $_product->_ref_references|@count}}
                    <optgroup label="Références">
                      {{foreach from=$_product->_ref_references item=_reference}}
                        <option value="{{$_reference->_id}}" selected="selected">{{$_reference->_ref_societe}} ({{$_reference->quantity}})</option>
                      {{/foreach}}
                    </optgroup>
                  {{/if}}
                  <optgroup label="Fournisseurs">
                    <option value="" disabled="disabled" {{if !$_product->_ref_references|@count}}selected="selected"{{/if}}> &ndash; Choisir un fournisseur</option>
                    {{foreach from=$list_societes item=_societe}}
                      <option value="{{$_societe->_id}}-{{$_product->_id}}" {{if !$_product->_ref_references|@count && $_societe->_id == $_product->societe_id}}selected="selected"{{/if}}>{{$_societe}}</option>
                    {{/foreach}}
                  </optgroup>
                </select>
              </td>
              <td style="text-align: center;">
                {{mb_field object=$_product->_new_lot field=code size=15 prop="str notNull"}}
              </td>
              <td style="text-align: center;">
                {{mb_field object=$_product->_new_lot field=lapsing_date register=true form="create-lot-$product_id" prop="date notNull"}}
              </td>
              {{* <td>{{mb_field class=CProductOrderItemReception field=date register=true form=searchProductOrderItemReception}}</td> *}}
              <td>
                <button type="submit" class="submit notext">{{tr}}Save{{/tr}}</button>
              </td>
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
