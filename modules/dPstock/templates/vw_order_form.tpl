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

{{assign var=nb_par_page value="23"}}	
	
<style type="text/css">
{{include file=../../dPcompteRendu/css/print.css header=4 footer=4 nodebug=true}}

table.print td{
 font-size: 0.9em;
 font-family: Arial;
}

</style>

<div class="header">
  <h1>
    <a href="#" onclick="window.print();">Bon de commande - {{mb_value object=$order->_ref_group field=text}}</a>
  </h1>
</div>


<div class="footer">

</div>

	<table class="print {{if $order->_ref_order_items|@count <= $nb_par_page}}bodyWithoutPageBreak{{else}}body{{/if}}" style="border-spacing:0">
		<tr>
			<td colspan="5">
			  <table class="tbl">
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
	   </td>
	</tr>	
	
	<tr>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductReference field=code}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProduct field=name}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItem field=quantity}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItem field=unit_price}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItem field=_price}}</th>
  </tr>
	
	{{foreach from=$order->_ref_order_items item=curr_item name="foreach_products"}}
	{{assign var=nb_pages value=$smarty.foreach.foreach_products.total/$nb_par_page}}
	
	{{if !$smarty.foreach.foreach_products.first && $smarty.foreach.foreach_products.index%$nb_par_page == 0}}
		</table>
		{{assign var=iterations_restantes value=$smarty.foreach.foreach_products.total-$smarty.foreach.foreach_products.iteration}}
    <table style="border-spacing:0" class="print {{if $iterations_restantes >= $nb_par_page}}body{{else}}bodyWithoutPageBreak{{/if}}">
    <tr>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductReference field=code}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProduct field=name}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItem field=quantity}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItem field=unit_price}}</th>
    <th class="category" style="border: 1px solid black;">{{mb_title class=CProductOrderItem field=_price}}</th>
  </tr>
  {{/if}}
	
	<tr>
    <td style="border: 1px solid black;">{{mb_value object=$curr_item->_ref_reference field=code}}</td>
    <td style="border: 1px solid black;">{{mb_value object=$curr_item->_ref_reference->_ref_product field=name}}</td>
    <td style="text-align: center; white-space: nowrap; border: 1px solid black;">{{mb_value object=$curr_item field=quantity}}</td>
    <td style="white-space: nowrap; text-align: right; border: 1px solid black;">{{mb_value object=$curr_item field=unit_price decimals=4}}</td>
    <td style="white-space: nowrap; text-align: right; border: 1px solid black;">{{mb_value object=$curr_item field=_price decimals=4}}</td>
  </tr>
	
	{{if $smarty.foreach.foreach_products.last}}
	<tr>
    <td colspan="10" id="order-{{$order->_id}}-total" style="border-top: 1px solid #666;">
      <strong style="float: right;">{{tr}}Total{{/tr}} : {{mb_value object=$order field=_total decimals=4}}</strong>
    </td>
  </tr>
	{{/if}}
  {{/foreach}}
</table>

<!-- re-ouverture du tableau -->
<table>
  <tr>
    <td>