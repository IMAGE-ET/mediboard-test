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
  url.requestJSON(fillInputs);
}

fillInputs = function(data){
  $('list-order').select('input.num').each(function(e){
    var stock_id = e.form.stock_id.value;
    if (data[stock_id]) {
      if (e.name == 'quantity') {
        $V(e, data[stock_id], true);
      }
    }
    else e.value = 0;
  });
}

{{if $only_service_stocks == 1}}
Main.add(function(){
  autoOrder({{$service->_id}}, '{{$date_min}}', '{{$date_max}}');
});
{{/if}}
</script>

{{if $smarty.now|date_format:"%Y-%m-%d" < $date_min || $smarty.now|date_format:"%Y-%m-%d" > $date_max}}
	<div class="small-info">
	  La date courante n'est pas comprise dans l'intervalle spécifié, les dispensations effectuées ne seront pas affichées.
	</div>
{{/if}}

<div class="pagination">
  <!-- 
  <button type="button" style="float: right;" class="tick" onclick="autoOrder({{$service->_id}}, '{{$date_min}}', '{{$date_max}}')">Commande auto</button>
  -->
  <label style="float: left; font-weight: normal;">
    <input type="checkbox" {{if $only_service_stocks == 1}}checked="checked"{{/if}} onchange="$V(getForm('filter').only_service_stocks, this.checked ? 1 : 0)" />
    Seulement les stocks du service 
  </label>

{{assign var=step value=$count_stocks|min:20}}
{{foreach from=0|range:$count_stocks:$step item=page}}
  {{assign var=view value=$page/20+1}}
  {{if $view|intval == $view}}
  <a href="#1" class="page {{if $page==$start}}active{{/if}}" 
     onclick="$V(getForm('filter').start,'{{$page}}'); refreshOrders();">{{$view}}</a>
  {{/if}}
{{/foreach}}
</div>

{{assign var=infinite value=$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
{{assign var=infinite_service value=$dPconfig.dPstock.CProductStockService.infinite_quantity}}

<table class="tbl">
  <tr>
    <th></th>
    <th>Unité de dispensation</th>
    <th style="width: 30%">Commande</th>
    <th>Déjà effectuées</th>
    {{if !$infinite_service && $only_service_stocks == 1}}
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