{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<button class="print" onclick="window.print();">{{tr}}Print{{/tr}}</button>

<h1 style="float: left; font-size: 2.5em; color: #000;">{{mb_value object=$order->_ref_group field=text}}</h1>
<h1 style="text-align: right; clear: both;">Bon de commande</h1>

<table class="main" style="margin-bottom: 2em;">
  <tr>
    <th style="text-align: left;">Adresse d'expédition</th>
    <th style="text-align: left;">Fournisseur</th>
  </tr>
  <tr>
    <td>{{mb_value object=$order->_ref_group field=raison_sociale}}</td>
    <td>{{mb_value object=$order->_ref_societe field=name}}</td>
  </tr>
  <tr>
    <td>{{mb_value object=$order->_ref_group field=adresse}}</td>
    <td>{{mb_value object=$order->_ref_societe field=address}}</td>
  </tr>
  <tr>
    <td>
      {{mb_value object=$order->_ref_group field=cp}}
      {{mb_value object=$order->_ref_group field=ville}}
    </td>
    <td>
      {{mb_value object=$order->_ref_societe field=postal_code}}
      {{mb_value object=$order->_ref_societe field=city}}
    </td>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th>{{tr}}CProduct-name{{/tr}}</th>
    <th>{{tr}}CProduct-code{{/tr}}</th>
    <th>{{tr}}CProductOrderItem-quantity{{/tr}}</th>
    <th>{{tr}}CProductOrderItem-unit_price{{/tr}}</th>
    <th>{{tr}}CProductOrderItem-_price{{/tr}}</th>
  </tr>
  {{foreach from=$order->_ref_order_items item=curr_item}}
  <tr>
    <td>{{mb_value object=$curr_item->_ref_reference->_ref_product field=name}}</td>
    <td>{{mb_value object=$curr_item->_ref_reference->_ref_product field=code}}</td>
    <td>{{mb_value object=$curr_item field=quantity}}</td>
    <td>{{mb_value object=$curr_item field=unit_price}}</td>
    <td>{{mb_value object=$curr_item field=_price}}</td>
  </tr>
  {{/foreach}}
  <tr>
    <td colspan="6" id="order-{{$order->_id}}-total" style="border-top: 1px solid #666;">
      <span style="float: right;">{{tr}}Total{{/tr}} : {{mb_value object=$order field=_total}}</span>
    </td>
  </tr>
</table>