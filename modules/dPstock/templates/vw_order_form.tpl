{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
	
<style type="text/css">
{{include file=../../dPcompteRendu/css/print.css header=4 footer=3 nodebug=true}}

html {
  font-family: Arial, Helvetica, sans-serif;
}

.print td {
  font-size: 11px;
  font-family: Arial, Verdana, Geneva, Helvetica, sans-serif;
}

.grid {
  border-collapse: collapse;
  border-spacing: 0;
  width: 99%;
  margin: auto;
}

.grid td,
.grid th {
  border: 1px solid #ccc !important;
  padding: 2px;
  color: #000;
}

.grid th {
  width: 0.1%;
  font-size: 1.0em !important;
}
</style>

{{if $order->object_id}}
  {{assign var=label value="Bon de commande / Facturation"}}
{{else}}
  {{assign var=label value="Bon de commande"}}
{{/if}}

<table class="main">
  <tr>
    <td>
<hr />
<table class="form">
  <col style="width: 10%" />
  <col style="width: 40%" />
  <col style="width: 10%" />
  <col style="width: 40%" />
  
  <tr>
    <th>Date</th>
    <td>{{$smarty.now|date_format:$dPconfig.datetime}}</td>
    <th>Numéro de commande</th>
    <td>{{$order->order_number}}</td>
  </tr>
  
  <tr>
    <th rowspan="2">{{mb_label object=$order field=comments}}</th>
    <td rowspan="2">{{mb_value object=$order field=comments}}</td>
    <th>{{mb_label object=$order field=_customer_code}}</th>
    <td>{{mb_value object=$order field=_customer_code}}</td>
  </tr>
  
  <tr>
    <th>{{if $order->object_id}}{{mb_label object=$order field=object_id}}{{/if}}</th>
    <td>
      {{if !$septic}}
        {{$order->_ref_object}}
      {{/if}}
    </td>
  </tr>
  
  <tr>
    <td colspan="4">
      <hr />
    </td>
  </tr>
  
  <tr>
    <th>Expéditeur</th>
    <td>
      {{assign var=address value=$order->_ref_address}}
      
      <strong>
      {{if $address instanceof CFunctions}}
        {{$order->_ref_group}}<br />
      {{/if}}
      {{$address}}
      </strong>
      
      <br />
      {{$address->adresse|nl2br}}<br />
      {{mb_value object=$address field=cp}} {{mb_value object=$address field=ville}}
      
      <br />
      {{if $address->tel}}
        <br />{{mb_title object=$address field=tel}}: {{mb_value object=$address field=tel}}
      {{/if}}
      
      {{if $address->fax}}
        <br />{{mb_title object=$address field=fax}}: {{mb_value object=$address field=fax}}
      {{/if}}
      
      {{if $address instanceof CFunctions && $address->soustitre}}
        <hr />{{$address->soustitre|nl2br}}
      {{/if}}
    </td>
    
    <th>Fournisseur</th>
    <td>
      {{assign var=societe value=$order->_ref_societe}}
      <strong>{{mb_value object=$societe field=name}}</strong><br />
      {{$societe->address|nl2br}}<br />
      {{mb_value object=$societe field=postal_code}} {{mb_value object=$societe field=city}}
      
      <br />
      {{if $societe->phone}}
        <br />{{mb_title object=$societe field=phone}}: {{mb_value object=$societe field=phone}}
      {{/if}}
      
      {{if $societe->fax}}
        <br />{{mb_title object=$societe field=fax}}: {{mb_value object=$societe field=fax}}
      {{/if}}
    </td>
  </tr>
</table>
    </td>
  </tr>
  <thead>
    <tr>
      <td>
        <h2>
          <a href="#" onclick="window.print();">
            {{$label}} - {{$order->_ref_group}}
          </a>
        </h2>
      </td>
    </tr>
  </thead>
  
  <tfoot>
    <tr>
      <td>
        <span style="float: right; text-align: right;">
          {{$smarty.now|date_format:$dPconfig.datetime}}
          
          {{if $pharmacien->_id}}
            <br />
            Pharmacien : <strong>{{$pharmacien}}</strong>
            {{if $pharmacien->commentaires}}
              - {{$pharmacien->commentaires}}
            {{/if}}
          {{/if}}
        </span>
        
        {{$label}} n°<strong>{{$order->order_number}}</strong>
        <br />
        Responsable de la commande : <strong>{{$app->_ref_user}}</strong>
      </td>
    </tr>
  </tfoot>
  
  <tr>
    <td>

<table class="grid print">
  <thead>
  	<tr>
      <th class="category">Code</th>
      {{if $order->object_id}}
        <th class="category">Lot</th>
        <th class="category">Date pér.</th>
      {{/if}}
      <th class="category" style="width: auto;">{{mb_title class=CProduct field=name}}</th>
      {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
        <th class="category">Unités</th>
        <th class="category"></th>
      {{else}}
        <th class="category">{{mb_title class=CProductOrderItem field=quantity}}</th>
      {{/if}}
      {{if $order->object_id}}
        <th class="category">{{mb_title class=CProductOrderItem field=renewal}}</th>
      {{/if}}
      <th class="category">{{mb_title class=CProductOrderItem field=unit_price}}</th>
      <th class="category">{{mb_title class=CProductOrderItem field=_price}}</th>
      <th class="category">{{mb_title class=CProductOrderItem field=tva}}</th>
    </tr>
  </thead>
	
	{{foreach from=$order->_ref_order_items item=curr_item}}
	<tr>
    <td style="text-align: right; white-space: nowrap;">
      {{if $curr_item->_ref_reference->supplier_code}}
        {{mb_value object=$curr_item->_ref_reference field=supplier_code}}
      {{else}}
        {{mb_value object=$curr_item->_ref_reference->_ref_product field=code}}
      {{/if}}
    </td>
    
    {{if $order->object_id}}
      {{if $curr_item->_ref_lot}}
        <td>{{mb_value object=$curr_item->_ref_lot field=code}}</td>
        <td>{{mb_value object=$curr_item->_ref_lot field=lapsing_date}}</td>
      {{else}}
        <td></td>
        <td></td>
      {{/if}}
    {{/if}}
    
    <td>
      <strong>{{mb_value object=$curr_item->_ref_reference->_ref_product field=name}}</strong>
      
      {{if $curr_item->septic}}
        (Déstérilisé)
      {{/if}}
    </td>
    {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
      <td style="text-align: right; white-space: nowrap;">
        {{$curr_item->_unit_quantity}}
      </td>
      <td style="white-space: nowrap;">{{$curr_item->_ref_reference->_ref_product->item_title}}</td>
    {{else}}
      <td style="text-align: center; white-space: nowrap;">{{mb_value object=$curr_item field=quantity}}</td>
    {{/if}}
    
    {{if $order->object_id}}
      <td>{{mb_value object=$curr_item field=renewal}}</td>
    {{/if}}
    
    <td style="white-space: nowrap; text-align: right;">
      {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
        {{mb_value object=$curr_item->_ref_reference field=_unit_price}}
      {{else}}
        {{mb_value object=$curr_item field=unit_price}}
      {{/if}}
    </td>
    <td style="white-space: nowrap; text-align: right;">{{mb_value object=$curr_item field=_price}}</td>
    <td style="white-space: nowrap; text-align: right;">{{mb_value object=$curr_item field=tva decimals=1}}</td>
  </tr>
  {{/foreach}}
  
  <tr>
    <td colspan="10" style="padding: 0.5em; font-size: 1.1em;">
      <span style="float: right; text-align: right;">
        <strong>{{tr}}Total{{/tr}} : {{mb_value object=$order field=_total}}</strong><br />
        <strong>{{tr}}Total TTC{{/tr}} : {{mb_value object=$order field=_total_tva}}</strong><br />
        {{mb_label object=$order->_ref_societe field=carriage_paid}} : {{mb_value object=$order->_ref_societe field=carriage_paid}}
      </span>
    </td>
  </tr>
  
</table>
</td>
</table>