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
  $('list-order').select('input.num[name=quantity]').each(function(e){
    var stock_id = e.form.stock_id.value;
    $V(e, data[stock_id] || 0, true);
  });
}

changeOrderPage = function(start) {
  $V(getForm('filter-order').start, start); 
}

{{if $only_service_stocks == 1 && !$endowment_id}}
Main.add(function(){
  autoOrder({{$service->_id}}, '{{$delivrance->_date_min}}', '{{$delivrance->_date_max}}');
});
{{/if}}
</script>

{{assign var=infinite value=$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
{{assign var=infinite_service value=$dPconfig.dPstock.CProductStockService.infinite_quantity}}

<form name="filter-order" action="?" method="get" onsubmit="return (checkForm(this) && refreshOrders())">
  <input type="hidden" name="m" value="soins" />
  <input type="hidden" name="start" value="{{$start}}" onchange="refreshOrders()" />
  <table class="form" style="table-layout: fixed;">
    <tr>
      <th>{{mb_label object=$delivrance field=_date_min}}</th>
      <td>{{mb_field object=$delivrance field=_date_min form="filter-order" register=true onchange="\$V(this.form.start, 0); refreshOrders()"}}</td>
      <th>{{mb_label object=$delivrance field=_date_max}}</th>
      <td>{{mb_field object=$delivrance field=_date_max form="filter-order" register=true onchange="\$V(this.form.start, 0); refreshOrders()"}}</td>
    </tr>
    <tr>
      <th></th>
      <td>
        {{* 
        <button type="button" style="float: right;" class="tick" onclick="autoOrder({{$service->_id}}, '{{$delivrance->_date_min}}', '{{$delivrance->_date_max}}')">Commande auto</button>
         *}}
        
        <div style="display: none">
          <input type="hidden" name="only_service_stocks" value="1{{* $only_service_stocks*}}" onchange="$V(this.form.start, 0); refreshOrders()" />
          <label style="font-weight: normal;">
            <input type="checkbox" {{if $only_service_stocks == 1}}checked="checked"{{/if}} onchange="$V(this.form.only_service_stocks, this.checked ? 1 : 0)"/>
            Seulement les stocks du service 
          </label>
          <br />
        </div>
        
        <input type="hidden" name="only_common" value="{{$only_common}}" onchange="$V(this.form.start, 0); refreshOrders()" />
        <label style="font-weight: normal;">
          <input type="checkbox" {{if $only_common == 1}}checked="checked"{{/if}} onchange="$V(this.form.only_common, this.checked ? 1 : 0)" />
          Seulement les stocks couramment utilisés
        </label>
      </td>
      
      {{if $service->_back.endowments|@count}}
        <th>
          <label for="endowment_id">
            En fonction d'une dotation
          </label>
        </th>
        <td>
          <select name="endowment_id" onchange="$V(this.form.start, 0); refreshOrders()">
            <option value=""> &ndash; {{tr}}No{{/tr}}</option>
            {{foreach from=$service->_back.endowments item=_endowment}}
              <option value="{{$_endowment->_id}}" {{if $endowment_id == $_endowment->_id}}selected="selected"{{/if}}>{{$_endowment->name}}</option>
            {{/foreach}}
          </select>
        </td>
      {{else}}
        <td colspan="2" style="color: #aaa;">
          {{tr}}CProductEndowment.none{{/tr}}
        </td>
      {{/if}}
    </tr>
  </table>
</form>

{{if $smarty.now|date_format:"%Y-%m-%d" < $delivrance->_date_min || $smarty.now|date_format:"%Y-%m-%d" > $delivrance->_date_max}}
  <div class="small-info">
    La date courante n'est pas comprise dans l'intervalle spécifié, les dispensations effectuées ne seront pas affichées.
  </div>
{{/if}}

<hr />

<form name="form-create-order" action="?" method="post" style="display: block; text-align: right;"
      onsubmit="return onSubmitFormAjax(this, {onComplete: refreshLists})">
  <input type="hidden" name="m" value="dPstock" />
  <input type="hidden" name="dosql" value="do_delivery_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="date_dispensation" value="now" />
  <input type="hidden" name="service_id" value="{{$service->_id}}" />
  <input type="hidden" name="order" value="1" />
  
  <label for="quantity">Faire une demande</label> :
  <input type="text" name="quantity" value="1" size="2" />
  
  <label for="comments">Produit</label>
  <input type="text" name="comments" size="40" class="notNull" />
  
  <button type="submit" class="tick notext" title="Faire la demande">Faire la demande</button>

  <script type="text/javascript">
    getForm("form-create-order").quantity.addSpinner({min:0});
  </script>
</form>

<hr />

{{mb_include module=system template=inc_pagination change_page="changeOrderPage" 
    total=$count_stocks current=$start step=20}}

<table class="tbl">
  <tr>
    <th style="width: 0.1%;">Code</th>
    <th></th>
    <th>Unité de dispensation</th>
    <th style="width: 30%">Commande</th>
    <th>Déjà effectuées</th>
    {{if !$infinite_service}}
    <th colspan="3" style="width: 0.1%;">
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