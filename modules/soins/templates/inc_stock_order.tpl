{{* $Id: inc_dispensations_list.tpl 6441 2009-06-19 09:47:12Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: 6441 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
autoOrder = function(service_id, date_min, date_max) {
  var url = new Url('soins', 'ajax_auto_order');
  url.addParam('service_id', service_id);
  url.addParam('date_min', date_min);
  url.addParam('date_max', date_max);
  url.requestUpdate('systemMsg', {onComplete:refreshLists});
}
</script>

{{if $smarty.now|date_format:"%Y-%m-%d" < $date_min || $smarty.now|date_format:"%Y-%m-%d" > $date_max}}
	<div class="small-info">
	  La date courante n'est pas comprise dans l'intervalle spécifié, les dispensations effectuées ne seront pas affichées.
	</div>
{{/if}}

<div class="pagination">
  <button type="button" style="float: right;" class="tick" onclick="autoOrder({{$service->_id}}, '{{$date_min}}', '{{$date_max}}')">Commande auto</button>

{{foreach from=0|range:$count_stocks:20 item=page}}
  <a href="javascript:;" class="page {{if $page==$start}}active{{/if}}" 
     onclick="$V(getForm('filter').start,{{$page}}); getForm('filter').onsubmit();">{{$page/20+1}}</a>
{{/foreach}}
</div>


{{assign var=infinite value=$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
{{assign var=infinite_service value=$dPconfig.dPstock.CProductStockService.infinite_quantity}}

<table class="tbl">
  <tr>
    <th></th>
    <th>Unité de dispensation</th>
    <th style="width: 30%">Dispensation</th>
    <th>Déjà effectuées</th>
    {{if !$infinite_service}}
    <th>Stock<br /> du service</th>
    {{/if}}
  </tr>
  {{foreach from=$stocks item=stock}}
    <tbody id="stock-{{$stock->_id}}" style="width: 100%">
    {{include file="inc_stock_order_line.tpl" nodebug=true}}
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CProductStockGroup.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>