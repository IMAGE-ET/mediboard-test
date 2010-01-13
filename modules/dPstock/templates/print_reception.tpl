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

{{assign var=nb_par_page value="20"}} 
  
<style type="text/css">
{{include file=../../dPcompteRendu/css/print.css header=4 footer=4 nodebug=true}}

table.print td{
 font-size: 11px;
 font-family: Arial, Verdana, Geneva, Helvetica, sans-serif;
}

</style>

<div class="header">
  <h1>
    <a href="#" onclick="window.print();">Bon de réception du {{mb_value object=$reception field="date"}}</a>
  </h1>
</div>


<div class="footer">

</div>

  <table class="print {{if $reception->_ref_reception_items|@count <= $nb_par_page}}bodyWithoutPageBreak{{else}}body{{/if}}" style="border-spacing:0">
    <tr>
      <td colspan="6" style="padding-bottom: 10px;">
        <table class="tbl">
          <tr>
            <th style="text-align: left; width: 50%;"></th>
            <th style="text-align: left; width: 50%;">Fournisseur</th>
          </tr>
          <tr>
            <td></td>
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
     </td>
  </tr> 
 	
  <tr>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItemReception field=order_item_id}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItemReception field=reception_id}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItemReception field=quantity}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItemReception field=code}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
  </tr>
  
  {{foreach from=$reception->_ref_reception_items item=curr_item name="foreach_products"}}
  {{assign var=nb_pages value=$smarty.foreach.foreach_products.total/$nb_par_page}}
  
  {{if !$smarty.foreach.foreach_products.first && $smarty.foreach.foreach_products.index%$nb_par_page == 0}}
    </table>
    {{assign var=iterations_restantes value=$smarty.foreach.foreach_products.total-$smarty.foreach.foreach_products.iteration}}
    <table style="border-spacing:0" class="print {{if $iterations_restantes >= $nb_par_page}}body{{else}}bodyWithoutPageBreak{{/if}}">
    <tr>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItemReception field=order_item_id}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItemReception field=reception_id}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItemReception field=quantity}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItemReception field=code}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItemReception field=lapsing_date}}</th>
  </tr>
  {{/if}}
  
  <tr>
  	<td class="category" style="border: 1px solid black;">{{mb_value object=$curr_item field=order_item_id}}</td>
    <td class="category" style="border: 1px solid black;">{{mb_value object=$curr_item field=reception_id}}</td>
    <td class="category" style="text-align: center; border: 1px solid black; width: 1%">{{mb_value object=$curr_item field=quantity}}</td>
    <td class="category" style="text-align: center; border: 1px solid black; width: 1%">{{mb_value object=$curr_item field=code}}</td>
    <td class="category" style="border: 1px solid black; width: 1%">{{mb_value object=$curr_item field=lapsing_date}}</td>
  </tr>
  
  {{if $smarty.foreach.foreach_products.last}}
 
  {{/if}}
  {{/foreach}}
</table>

<!-- re-ouverture du tableau -->
<table>
  <tr>
    <td>