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

changeOrderPage = function(start) {
  $V(getForm('filter').start, start); 
}

{{if $only_service_stocks == 1 && !$endowment_id}}
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

{{assign var=infinite value=$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
{{assign var=infinite_service value=$dPconfig.dPstock.CProductStockService.infinite_quantity}}
      
<table class="main">
  <tr>
    <td>
      <!-- 
      <button type="button" style="float: right;" class="tick" onclick="autoOrder({{$service->_id}}, '{{$date_min}}', '{{$date_max}}')">Commande auto</button>
      -->
      <label style="font-weight: normal;">
        <input type="checkbox" {{if $only_service_stocks == 1}}checked="checked"{{/if}} onchange="$V(getForm('filter').only_service_stocks, this.checked ? 1 : 0)" />
        Seulement les stocks du service 
      </label>
      <br />
      <label style="font-weight: normal;">
        <input type="checkbox" {{if $only_common == 1}}checked="checked"{{/if}} onchange="$V(getForm('filter').only_common, this.checked ? 1 : 0)" />
        Seulement les stocks couramment utilisés
      </label>
      <!--<br />
      <label style="font-weight: normal;">
        <input type="text" value="{{$keywords}}" onchange="$V(getForm('filter').keywords, this.value)" />
        Mots clés
      </label>-->
    </td>
    <td>
      <label>
        En fonction d'une dotation
        <select onchange="$V(getForm('filter').endowment_id, $V(this))">
          <option value="">{{tr}}No{{/tr}}</option>
          {{foreach from=$service->_back.endowments item=_endowment}}
            <option value="{{$_endowment->_id}}" {{if $endowment_id == $_endowment->_id}}selected="selected"{{/if}}>{{$_endowment->name}}</option>
          {{/foreach}}
        </select>
      </label>
    </td>
    <td>
      <form name="form-create-order" action="?" method="post" style="float: right;"
            onsubmit="return onSubmitFormAjax(this, {onComplete: refreshLists})">
        <input type="hidden" name="m" value="dPstock" />
        <input type="hidden" name="dosql" value="do_delivery_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="date_dispensation" value="now" />
        <input type="hidden" name="service_id" value="{{$service->_id}}" />
        <input type="hidden" name="order" value="1" />
        
        Faire une demande: 
        <input type="text" name="quantity" value="1" size="2" />
        
        {{mb_label class=CProductDelivery field=comments}}
        <input type="text" name="comments" size="40" />
        
        <button type="submit" class="tick notext" title="Faire la demande">Faire la demande</button>
      
        <script type="text/javascript">
          getForm("form-create-order").quantity.addSpinner({min:0});
        </script>
      </form>
    </td>
  </tr>
  <tr>
    <td colspan="10">
      {{mb_include module=system template=inc_pagination change_page="changeOrderPage" 
          total=$count_stocks current=$start step=20}}
    </td>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th style="width: 0.1%;">Code</th>
    <th></th>
    <th>Unité de dispensation</th>
    <th style="width: 30%">Commande</th>
    <th>Déjà effectuées</th>
    {{if !$infinite_service && $only_service_stocks == 1}}
    <th colspan="2" style="width: 0.1%;">
      Stock du service
    </th>
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