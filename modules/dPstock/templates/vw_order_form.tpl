{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<button class="print not-printable" onclick="window.print();">{{tr}}Print{{/tr}}</button>

<div style="position: fixed; width: 100%;">
  <h1>
    Bon de commande - 
    {{mb_value object=$order->_ref_group field=text}}
  </h1>
  
  <hr />
</div>

<table class="main print" style="margin-bottom: 2em; margin-top: 6em;">
  <tr>
    <th style="text-align: left; width: 50%;">Expéditeur</th>
    <th style="text-align: left; width: 50%;">Fournisseur</th>
  </tr>
  <tr>
    <td>
      {{assign var=group value=$order->_ref_group}}
      <strong>{{mb_value object=$group field=raison_sociale}}</strong><br />
      {{$group->adresse|nl2br}}<br />
      {{mb_value object=$group field=cp}} {{mb_value object=$group field=ville}}
      
      <br />
      {{if $group->tel}}
        <br />{{mb_title object=$group field=tel}}: {{mb_value object=$group field=tel}}
      {{/if}}
      
      {{if $group->fax}}
        <br />{{mb_title object=$group field=fax}}: {{mb_value object=$group field=fax}}
      {{/if}}
    </td>
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

<table class="tbl print">
  <tr>
    <th class="category">{{mb_title class=CProductReference field=code}}</th>
    <th class="category">{{mb_title class=CProduct field=name}}</th>
    <th class="category">{{mb_title class=CProductOrderItem field=quantity}}</th>
    <th class="category">{{mb_title class=CProductOrderItem field=unit_price}}</th>
    <th class="category">{{mb_title class=CProductOrderItem field=_price}}</th>
  </tr>
  {{foreach from=$order->_ref_order_items item=curr_item}}
  <tr>
    <td>{{mb_value object=$curr_item->_ref_reference field=code}}</td>
    <td>{{mb_value object=$curr_item->_ref_reference->_ref_product field=name}}</td>
    <td style="white-space: nowrap; text-align: right;">{{mb_value object=$curr_item field=quantity}}</td>
    <td style="white-space: nowrap; text-align: right;">{{mb_value object=$curr_item field=unit_price decimals=4}}</td>
    <td style="white-space: nowrap; text-align: right;">{{mb_value object=$curr_item field=_price decimals=4}}</td>
  </tr>
  {{/foreach}}
  <tr>
    <td colspan="10" id="order-{{$order->_id}}-total" style="border-top: 1px solid #666;">
      <strong style="float: right;">{{tr}}Total{{/tr}} : {{mb_value object=$order field=_total}}</strong>
    </td>
  </tr>
</table>

