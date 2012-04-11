{{* $Id: inc_order.tpl 7667 2009-12-18 16:49:15Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7667 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $reception->_id}}
  <button type="button" class="print" onclick="printReception('{{$reception->_id}}');">Bon de réception</button>
  <button type="button" class="barcode" onclick="printBarcodeGrid('{{$reception->_id}}')">Codes barres</button>
  
  {{if !$reception->locked}}
  <form name="lock-reception-{{$reception->_id}}" method="post" action="" onsubmit="return onSubmitFormAjax(this, {onComplete: function(){location.reload()}})">
    <input type="hidden" name="m" value="dPstock" />
    <input type="hidden" name="dosql" value="do_product_reception_aed" />
    {{mb_key object=$reception}}
    <input type="hidden" name="locked" value="1" />
    
    <button class="lock">Verrouiller</button>
  </form>
  {{/if}}
  
  <form name="bill-reception-{{$reception->_id}}" method="post" action="" onsubmit="return onSubmitFormAjax(this)">
    <input type="hidden" name="m" value="dPstock" />
    <input type="hidden" name="dosql" value="do_product_reception_aed" />
    {{mb_key object=$reception}}
    
    <fieldset>
      <legend>Facturation</legend>
      <table class="main form">
        <tr>
          <th>{{mb_label object=$reception field=bill_number}}</th>
          <td>{{mb_field object=$reception field=bill_number}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$reception field=bill_date}}</th>
          <td>
            {{mb_field object=$reception field=bill_date form="bill-reception-`$reception->_id`" register=true}}
            <button class="submit">{{tr}}Save{{/tr}}</button>
          </td>
        </tr>
      </table>
    </fieldset>
    <br />
  </form>
  
  <table class="tbl">
    <tr>
      <th colspan="6" class="title">{{$reception->reference}}</th>
    </tr>
    <tr>
      <th class="narrow"></th>
      <th>{{mb_title class=CProductOrderItemReception field=date}}</th>
      <th>{{mb_title class=CProductOrderItemReception field=quantity}}</th>
      <th>Unité</th>
      <th>{{mb_title class=CProductOrderItemReception field=code}}</th>
      <th>{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
    </tr>
    {{foreach from=$reception->_back.reception_items item=curr_item}}
      <tbody id="reception-item-{{$curr_item->_id}}">
        {{include file="inc_reception_item.tpl"}}
      </tbody>
    {{foreachelse}}
      <tr>
        <td colspan="6" class="empty">{{tr}}CProductOrderItemReception.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
{{else}}
  <div class="small-info">
    Effectuez la réception d'une ligne de commande à gauche pour commencer le bon de réception
  </div>
{{/if}}