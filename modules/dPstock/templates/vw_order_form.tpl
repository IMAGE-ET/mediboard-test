{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<!-- Fermeture du tableau pour faire fonctionner le page-break -->
    </td>
  </tr>
</table>

{{assign var=nb_par_page value="25"}}	
	
<style type="text/css">
{{include file=../../dPcompteRendu/css/print.css header=4 footer=2 nodebug=true}}

table.print td {
  font-size: 11px;
  font-family: Arial, Verdana, Geneva, Helvetica, sans-serif;
}

table.grid {
  border-collapse: collapse;
  border-spacing: 0;
  width: 99%;
  margin: auto;
}

table.grid td,
table.grid th {
  border: 1px solid #ccc !important;
  padding: 2px;
  color: #000;
}

table.grid th {
  width: 0.1%;
  font-size: 1.0em !important;
}
</style>

<div class="header">
  <h1>
    <a href="#" onclick="window.print();">
      Bon de commande - {{$order->_ref_group}}
    </a>
  </h1>
</div>

<div class="footer">
  <span style="float: right;">
    {{$smarty.now|date_format:$dPconfig.datetime}}
  </span>
  
  Bon de commande n°{{$order->order_number}}
</div>

<table class="form" style="margin-top: 5em;">
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
    <th>{{mb_label object=$order field=comments}}</th>
    <td>{{mb_value object=$order field=comments}}</td>
    <th>{{mb_label object=$order field=_customer_code}}</th>
    <td>{{mb_value object=$order field=_customer_code}}</td>
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

<br />

<table class="grid print bodyWithoutPageBreak">
	<tr>
    <th class="category">Code</th>
    <th class="category" style="width: auto;">{{mb_title class=CProduct field=name}}</th>
    {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
      <th class="category">Unités</th>
      <th class="category"></th>
    {{else}}
      <th class="category">{{mb_title class=CProductOrderItem field=quantity}}</th>
    {{/if}}
    <th class="category">{{mb_title class=CProductOrderItem field=unit_price}}</th>
    <th class="category">{{mb_title class=CProductOrderItem field=_price}}</th>
  </tr>
	
	{{foreach from=$order->_ref_order_items item=curr_item name="foreach_products"}}
	{{assign var=nb_pages value=$smarty.foreach.foreach_products.total/$nb_par_page}}
	
	{{if !$smarty.foreach.foreach_products.first && $smarty.foreach.foreach_products.index%$nb_par_page == 0}}
</table>
		{{assign var=iterations_restantes value=$smarty.foreach.foreach_products.total-$smarty.foreach.foreach_products.iteration}}
<table class="grid print {{if $iterations_restantes >= $nb_par_page}}body{{else}}bodyWithoutPageBreak{{/if}}">
  <tr>
    <th class="category">Code</th>
    <th class="category" style="width: auto;">{{mb_title class=CProduct field=name}}</th>
    {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
      <th class="category">Unités</th>
      <th class="category"></th>
    {{else}}
      <th class="category">{{mb_title class=CProductOrderItem field=quantity}}</th>
    {{/if}}
    <th class="category">{{mb_title class=CProductOrderItem field=unit_price}}</th>
    <th class="category">{{mb_title class=CProductOrderItem field=_price}}</th>
  </tr>
  {{/if}}
	
	<tr>
    <td style="text-align: right;">{{mb_value object=$curr_item->_ref_reference field=supplier_code}}</td>
    <td>{{mb_value object=$curr_item->_ref_reference->_ref_product field=name}}</td>
    {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
      <td style="text-align: center; white-space: nowrap;">
        {{$curr_item->quantity*$curr_item->_ref_reference->quantity*$curr_item->_ref_reference->_ref_product->quantity}}
      </td>
      <td style="white-space: nowrap;">{{$curr_item->_ref_reference->_ref_product->item_title}}</td>
    {{else}}
      <td style="text-align: center; white-space: nowrap;">{{mb_value object=$curr_item field=quantity}}</td>
    {{/if}}
    <td style="white-space: nowrap; text-align: right;">{{mb_value object=$curr_item field=unit_price}}</td>
    <td style="white-space: nowrap; text-align: right;">{{mb_value object=$curr_item field=_price}}</td>
  </tr>
	
	{{if $smarty.foreach.foreach_products.last}}
	<tr>
    <td colspan="10" style="padding: 0.5em; font-size: 1.1em;">
      <span style="float: right;">
        <strong>{{tr}}Total{{/tr}} : {{mb_value object=$order field=_total}}</strong><br />
        {{mb_label object=$order->_ref_societe field=carriage_paid}} : {{mb_value object=$order->_ref_societe field=carriage_paid}}
      </span>
    </td>
  </tr>
	{{/if}}
  {{/foreach}}
</table>

<!-- re-ouverture du tableau -->
<table>
  <tr>
    <td>