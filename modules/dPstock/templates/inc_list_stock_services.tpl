{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main tbl">
  <tr>
    <th colspan="10" class="title">Stocks de services</th>
  </tr>
  <tr>
    <th>{{mb_title class=CProductStockService field=service_id}}</th>
    <th>
      {{mb_title class=CProductStockService field=quantity}} / 
      {{mb_title class=CProductStockService field=order_threshold_min}} / 
      {{mb_title class=CProductStockService field=order_threshold_optimum}}
    </th>
  </tr>
  {{foreach from=$list_services item=_service}}
    {{assign var=_id value=$_service->_id}}
    {{assign var=_stock value=$_service->_ref_stock}}
    
    <tr>
      <td style="width: 0.1%;">
        {{$_service}}
      </td>
      
      <td>
        <form name="CProductStockService-create-{{$_id}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshListStocksService.curry({{$_stock->product_id}})})">
          <input type="hidden" name="m" value="dPstock" />
          <input type="hidden" name="dosql" value="do_stock_service_aed" />
          
          {{if !$_stock->_id}}
            {{mb_field object=$_stock field=quantity increment=1 size=1 form="CProductStockService-create-$_id"}}
            {{mb_field object=$_stock field=order_threshold_min increment=1 size=1 form="CProductStockService-create-$_id"}}
            {{mb_field object=$_stock field=order_threshold_optimum increment=1 size=1 form="CProductStockService-create-$_id"}}
            <button type="button" class="add notext" onclick="this.form.onsubmit()">{{tr}}Save{{/tr}}</button>
          {{else}}
            {{mb_value object=$_stock field=quantity}} / 
            {{mb_value object=$_stock field=order_threshold_min}} / 
            {{mb_value object=$_stock field=order_threshold_optimum}}
            
            {{mb_include module=dPstock template=inc_bargraph stock=$_stock}}
            {{mb_label object=$_stock field=common}}
            {{mb_field object=$_stock field=common typeEnum=checkbox onchange="this.form.onsubmit()"}}
          {{/if}}
          
          {{mb_key object=$_stock}}
          {{mb_field object=$_stock field=service_id hidden=true}}
          {{mb_field object=$_stock field=product_id hidden=true}}
          {{* @FIXME Oblig� d'ajouter le seuil critique car sinon : erreur JS lors du checkForm *}}
          {{mb_field object=$_stock field=order_threshold_critical hidden=true}}
        </form>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="5">{{tr}}CProductStockService.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>