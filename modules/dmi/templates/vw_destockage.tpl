
{{mb_form name="filterSocietes" m="dmi" tab=$tab}}
  {{mb_field object=$product field=societe_id form="filterSocietes" autocomplete="true,1,50,true,true"}}
  
  <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
{{/mb_form}}

<div class="small-info">
  Choisissez un laboratoire pour voir les lots disponibles, puis cliquez 
  sur le bouton <button class="tick notext" type="button">Commander</button> 
  pour l'ajouter à un bon de retour. Un clic sur un autre article du même 
  labo l'ajoutera à ce bon de retour tant qu'il n'est pas validé.
  <br/>
  Une fois votre bon de retour terminé, vous pourrez supprimer les lots recommandés avec le bouton 
  <button class="cancel notext" type="button">Annuler le lot</button>
</div>

<table class="main tbl">
  <tr>
    <th>{{mb_title class=CProduct field=code}}</th>
    <th>{{mb_title class=CProductReference field=product_id}}</th>
    <th>{{mb_title class=CDMI field=type}}</th>
    <th>{{mb_title class=CProductOrderItemReception field=code}}</th>
    <th>{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
    <th class="narrow">Qté <br />tot./util/.rest.</th>
    <th>Retour</th>
    <th>Bons de retour<br /> existants</th>
    <th>Suppr.</th>
  </tr>
  
  {{foreach from=$receptions item=_lot}}
    {{assign var=product value=$_lot->_ref_order_item->_ref_reference->_ref_product}}
  
    <tr>
      <td>
        {{mb_value object=$product field=code}}
      </td>
      <td>
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$_lot->_guid}}')">
          {{mb_value object=$_lot->_ref_order_item->_ref_reference field=product_id}}
        </strong>
      </td>
      <td>
        {{if $_lot->_ref_dmi->_id}}
          {{mb_value object=$_lot->_ref_dmi field=type}}
        {{/if}}
      </td>
      <td>{{mb_value object=$_lot field=code}}</td>
      <td {{if $_lot->lapsing_date && $_lot->lapsing_date|strtotime < $smarty.now}}class="error"{{/if}} style="text-align: center;">
        {{mb_value object=$_lot field=lapsing_date}}
      </td>
      <td style="text-align: center;">
        {{$_lot->_total_quantity}} / {{$_lot->_used_quantity}} / {{$_lot->_remaining_quantity}}
      </td>
      <td class="narrow" style="text-align: left;">
        {{assign var=lot_id value=$_lot->_id}}
        
        {{if $_lot->_ref_dmi->type != "purchase"}}
          <form name="product-reference-{{$_lot->_id}}" action="?m=dmi&amp;tab=vw_destockage" 
                method="post" onsubmit="return orderProduct(this)">
            <input type="hidden" name="m" value="dPstock" />
            <input type="hidden" name="dosql" value="do_order_item_aed" />
            <input type="hidden" name="_create_order" value="1" />
            <input type="hidden" name="_comments" value="Bon de retour" />
            <input type="hidden" name="reception_id" value="" />
            <input type="hidden" name="lot_id" value="{{$_lot->_id}}" />
            
            <table class="main form layout">
              <tr>
                <td class="narrow">
                  {{mb_field object=$_lot field=quantity size=1 increment=true form="product-reference-$lot_id" style="width: 1.5em;"}}
                </td>
                <td>
                  <select name="reference_id" style="max-width: 12em;">
                    {{foreach from=$product->_back.references item=_reference}}
                      <option value="{{$_reference->_id}}" 
                              {{if $_lot->_id == $_reference->_id}}selected="selected"{{/if}}>
                        {{$_reference->_ref_societe}} (x{{$_reference->quantity}})
                      </option>
                    {{/foreach}}
                  </select>
                </td>
                <td class="narrow">
                  <button class="tick notext" type="submit">Commander</button>
                </td>
              </tr>
              <tr>
                <td colspan="3">
                  <label style="float: right;">
                    {{tr}}CProductOrderItem-renewal-court{{/tr}}
                    {{mb_field object=$_lot->_new_order_item field=renewal typeEnum=checkbox}}
                  </label>
                </td>
              </tr>
            </table>
          </form>
          {{/if}}
      </td>
      <td>
        {{foreach from=$_lot->_back.order_items item=_item}}
          <strong>{{$_item->quantity}}</strong> - 
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_item->_ref_order->_guid}}')">
            {{$_item->_ref_order->order_number}}
          </span>
          <br />
        {{/foreach}}
      </td>
      <td class="narrow">
        <form name="lot-cancel-{{$_lot->_id}}" class="prepared" action="?m=dmi&amp;tab=vw_destockage" method="post"
              onsubmit="return confirm('Etes-vous sûr de vouloir annuler ce lot ?')">
          <input type="hidden" name="m" value="dPstock" />
          <input type="hidden" name="dosql" value="do_order_item_reception_aed" />
          <input type="hidden" name="cancelled" value="1" />
          <input type="hidden" name="order_item_reception_id" value="{{$_lot->_id}}" />
          <button class="cancel notext" type="submit">Annuler le lot</button>
        </form>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="10" class="empty">{{tr}}CProductOrderItemReception.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>