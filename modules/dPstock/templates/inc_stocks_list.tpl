{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=class_name value=$stock->_class_name}}

{{mb_include module=system template=inc_pagination change_page="changePage" 
    total=$list_stocks_count current=$start step=$dPconfig.dPstock.$class_name.pagination_size}}

<table class="tbl">
  <tr>
    <th>{{mb_title object=$stock field=product_id}}</th>
    {{if $class_name == 'CProductStockService'}}
      <th>{{mb_title object=$stock field=service_id}}</th>
      <th>{{mb_title object=$stock field=common}}</th>
    {{/if}}
    <th>{{mb_title object=$stock field=quantity}}</th>
    <th colspan="3">{{mb_title object=$stock field=_package_quantity}}</th>
    <th>{{tr}}CProductStockGroup-bargraph{{/tr}}</th>
  </tr>
  
<!-- Stocks service list -->
{{foreach from=$list_stocks item=_stock}}
  {{assign var=product value=$_stock->_ref_product}}
  <tr {{if $stock_id == $_stock->_id}}class="selected"{{/if}}>
    <td>
    	{{if $class_name == 'CProductStockService'}}
			{{assign var=tab value=vw_idx_stock_service&stock_service_id}}
			{{else}}
      {{assign var=tab value=vw_idx_stock_group&stock_id}}
			{{/if}}
      <a href="?m={{$m}}&amp;tab={{$tab}}={{$_stock->_id}}">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$product->_guid}}')">
          {{$product->_view|truncate:60}}
        </span>
      </a>
    </td>
    {{if $class_name == 'CProductStockService'}}
      <td>{{$_stock->_ref_service}}</td>
      <td>{{tr}}{{$_stock->common|ternary:'Yes':''}}{{/tr}}</td>
    {{/if}}
    <td style="text-align: right;">
		  <strong>{{$_stock->quantity}}</strong>
		</td>
		
    <td style="text-align: right;">= {{$_stock->_package_quantity}}</td>
		<td>{{$product->packaging}}</td>
    <td style="text-align: right;">
		  {{if $_stock->_package_mod-0}}
		  + {{$_stock->_package_mod-0}}
			{{/if}}
		</td>
    <td>{{include file="inc_bargraph.tpl" stock=$_stock}}</td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}{{$class_name}}.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>

