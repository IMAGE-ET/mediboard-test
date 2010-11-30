{{* $Id: vw_idx_delivrance.tpl 9733 2010-08-04 14:03:11Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 9733 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{main}}
  Control.Tabs.create("balance-tabs-{{$type}}", true);
{{/main}}

{{if $total > $products|@count}}
  <div class="small-warning">
    Seulement les {{$products|@count}} premiers produits sont pris en compte (sur {{$total}}).
  </div>
{{/if}}

<ul class="control_tabs small" id="balance-tabs-{{$type}}">
  {{foreach from=$flows item=_flows key=key}}
    <li><a href="#{{$type}}-{{$key}}">{{$_flows.2}}</a></li>
  {{/foreach}}
  <li><a href="#{{$type}}-rotation">Rotation des stocks</a></li>
  {{if $products != null}}
    <li><a href="#{{$type}}-products">Produits ({{$products|@count}}{{if $total > $products|@count}}/{{$total}}{{/if}})</a></li>
  {{/if}}
</ul>
<hr class="control_tabs" />

{{math equation="1/(x-1)" x=$services|@count assign=width}}

{{foreach from=$flows item=_flows key=key}}

<table class="main tbl" id="{{$type}}-{{$key}}" style="display: none;">
  <tr>
    <th></th>
    
    {{foreach from=$services item=_service}}
      <th style="width: {{$width}}%">{{$_service}}</th>
    {{/foreach}}
    
    <th>Total</th>
  </tr>
  
  {{foreach from=$_flows.0.out item=_flow key=_date}}
    <tr {{if $_date == "total"}}style="font-weight: bold"{{/if}}>
      <th style="width: 0.1%;">
        {{if $_date == "total"}}
          Total
        {{else}}
          {{$_date|date_format:$_flows.1}}
        {{/if}}
      </th>
      
      {{foreach from=$_flow item=_value key=_service_id}}
        <td style="text-align: center; {{if $_date == "total" && $_service_id == "total"}}font-size: 1.4em;{{/if}} {{if $_service_id == "total"}}font-weight: bold; width: 0.1%;{{/if}}">
          {{$_value}}
        </td>
      {{/foreach}}
    </tr>
  {{/foreach}}
</table>
{{/foreach}}

<table class="main tbl" id="{{$type}}-rotation" style="display: none;">
  <tr>
    <th></th>
    
    <th style="width: 33%">Entrée</th>
    <th style="width: 33%">Sortie</th>
    <th style="width: 33%">Balance</th>
  </tr>
  
  {{foreach from=$balance.in key=_date item=_balance}}
    <tr>
      <th style="width: 0.1%;">
        {{$_date|date_format:"%b"}}
      </th>
      
      <td style="text-align: center;">{{$balance.in.$_date}}</td>
      <td style="text-align: center;">{{$balance.out.$_date}}</td>
      <td style="text-align: center;">{{$balance.diff.$_date}}</td>
    </tr>
  {{/foreach}}
</table>

{{if $products != null}}
<table class="main tbl" id="{{$type}}-products" style="display: none;">
  <tr>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProduct-code{{/tr}}</th>
    <th>{{tr}}CProductStockGroup-quantity{{/tr}}</th>
  </tr>
    
  {{foreach from=$products item=_product}}
    <tr>
      <td>{{$_product}}</td>
      <td>{{$_product->code}}</td>
      <td>{{mb_value object=$_product->_ref_stock_group field=quantity}}</td>
    </tr>
  {{/foreach}}
</table>
{{/if}}
