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

{{foreach from=$flows item=_flows key=key}}

{{math equation="1/x*100" x=$_flows.0.out|@reset|@count assign=width}}

<table class="main tbl" id="{{$type}}-{{$key}}" style="display: none;">
  <tr>
    <th></th>
    
    {{foreach from=$_flows.0.out|@reset item=_flow key=_date}}
      <th style="width: {{$width}}%">
        {{if $_date == "total"}}
          Total
        {{else}}
          {{$_date|date_format:$_flows.1}}
        {{/if}}
      </th>
    {{/foreach}}
    
  </tr>
  
  {{foreach from=$_flows.0.out item=_flow key=_service_id}}
    <tr style="{{if $_service_id == "total"}}font-weight: bold;{{/if}} text-align: center;">
      <th style="width: 0.1%;">
        {{if $_service_id == "total"}}
          Total
        {{else}}
          {{$services.$_service_id}}
        {{/if}}
      </th>
      
      {{foreach from=$_flow item=_value key=_date}}
        <td style="{{if $_date == "total" && $_service_id == "total"}}font-size: 1.4em;{{/if}} 
                   {{if $_date == "total"}}font-weight: bold;{{/if}}">
          <span {{if $_value == 0 && $_date != "total" && $_service_id != "total"}}class="opacity-40"{{/if}}>{{$_value}}</span>
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
