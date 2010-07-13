{{* $Id: vw_order_form.tpl 7825 2010-01-13 11:28:06Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7825 $
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
  font-size: 1.0em !important;
}
</style>

<div class="header">
  <h1>
    <a href="#" onclick="window.print();">
      Bon de réception - {{$reception->_ref_group}}
    </a>
  </h1>
</div>

<div class="footer">
  <span style="float: right;">
    {{$smarty.now|date_format:$dPconfig.datetime}}
  </span>
  
  Bon de réception n°{{$reception->reference}}
</div>

<table class="form" style="margin-top: 5em;">
  <col style="width: 10%" />
  <col style="width: 40%" />
  <col style="width: 10%" />
  <col style="width: 40%" />
  
  <tr>
    <th></th>
    <td></td>
    <th>Fournisseur</th>
    <td>
      {{assign var=societe value=$reception->_ref_societe}}
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

<table class="grid print {{if $reception->_ref_reception_items|@count <= $nb_par_page}}bodyWithoutPageBreak{{else}}body{{/if}}">
  <col style="width: 0.1%;" />
  <col />
  <col style="width: 0.1%;" />
  <col style="width: 0.1%;" />
  
  <tr>
    <th class="category">{{mb_title class=CProductReference field=code}}</th>
    <th class="category">{{mb_title class=CProductOrderItemReception field=order_item_id}}</th>
    <th class="category">{{mb_title class=CProductOrderItemReception field=quantity}}</th>
    <th class="category">{{mb_title class=CProductOrderItemReception field=code}}</th>
    <th class="category">{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
    <th class="category">{{mb_title class=CProductOrderItem field=_price}}</th>
  </tr>
  
  {{foreach from=$reception->_ref_reception_items item=curr_item name="foreach_products"}}
  {{assign var=nb_pages value=$smarty.foreach.foreach_products.total/$nb_par_page}}
  
  {{if !$smarty.foreach.foreach_products.first && $smarty.foreach.foreach_products.index%$nb_par_page == 0}}
    </table>
    {{assign var=iterations_restantes value=$smarty.foreach.foreach_products.total-$smarty.foreach.foreach_products.iteration}}
    <table class="grid print {{if $iterations_restantes >= $nb_par_page}}body{{else}}bodyWithoutPageBreak{{/if}}">
      <tr>
        <th class="category">{{mb_title class=CProductReference field=code}}</th>
        <th class="category">{{mb_title class=CProductOrderItemReception field=order_item_id}}</th>
        <th class="category">{{mb_title class=CProductOrderItemReception field=quantity}}</th>
        <th class="category">{{mb_title class=CProductOrderItemReception field=code}}</th>
        <th class="category">{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
        <th class="category">{{mb_title class=CProductOrderItem field=_price}}</th>
      </tr>
  {{/if}}
  
  <tr>
    <td>{{mb_value object=$curr_item->_ref_order_item->_ref_reference field=code}}</td>
  	<td>{{mb_value object=$curr_item field=order_item_id}}</td>
    <td style="text-align: center; width: 1%">{{mb_value object=$curr_item field=quantity}}</td>
    <td style="text-align: center; width: 1%">{{mb_value object=$curr_item field=code}}</td>
    <td style="width: 1%">{{mb_value object=$curr_item field=lapsing_date}}</td>
    <td style="width: 1%">{{mb_value object=$curr_item->_ref_order_item field=_price}}</td>
  </tr>
  
  {{if $smarty.foreach.foreach_products.last}}
  <tr>
    <td colspan="10" style="padding: 0.5em; font-size: 1.1em;">
      <span style="float: right;">
        <strong>{{tr}}Total{{/tr}} : {{mb_value object=$reception field=_total}}</strong>
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