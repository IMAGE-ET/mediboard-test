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
    Pour des raisons de performances, pour le moment seulement les {{$products|@count}} premiers 
    produits sont pris en compte (sur {{$total}}). Le nombre de produits pris en compte de sera plus limité
    dans la version finale.
  </div>
{{/if}}

<ul class="control_tabs small" id="balance-tabs-{{$type}}">
  {{foreach from=$flows item=_flows key=key}}
    <li onmousedown="window['{{$type}}-{{$key}}-drawFunction']()">
      <a href="#{{$type}}-{{$key}}">{{$_flows.2}}</a>
    </li>
  {{/foreach}}
  <li onmousedown="window['{{$type}}-rotation-drawFunction']()"><a href="#{{$type}}-rotation">Rotation des stocks</a></li>
  {{if $products != null}}
    <li><a href="#{{$type}}-products">Produits ({{$products|@count}}{{if $total > $products|@count}}/{{$total}}{{/if}})</a></li>
  {{/if}}
</ul>
<hr class="control_tabs" />

{{foreach from=$flows item=_flows key=key}}

{{math equation="1/x*100" x=$_flows.0.out|@reset|@count assign=width}}

<script type="text/javascript">
  window["{{$type}}-{{$key}}-drawFunction"] = function(){
    (function(){
      var options = {{$_flows.graph.options|@json}};
      options.legend.container = $("{{$type}}-{{$key}}-legend");
      options.mouse.trackFormatter = mouseTrackFormatter;
      Flotr.draw("{{$type}}-{{$key}}-graph", {{$_flows.graph.data|@json}}, options);
    }).delay(0.1);
    
    window["{{$type}}-{{$key}}-drawFunction"] = function(){};
  }
  
  Main.add(function(){
    $("{{$type}}-{{$key}}-table").gridHighlight();
    if ($("{{$type}}-{{$key}}").visible()) {
      window["{{$type}}-{{$key}}-drawFunction"]();
    }
  });
</script>

<div id="{{$type}}-{{$key}}" style="display: none; page-break-after: always;">
  <button class="hslip singleclick not-printable" onclick="exportData($('{{$type}}-{{$key}}-table'), '.price', 
  '{{$_flows.2}} &ndash; {{if $key == "month"}}{{$month}}/{{/if}}{{$year}} &ndash; {{$title|smarty:nodefaults|JSAttribute}}')">CSV</button>
  <button class="pdf singleclick not-printable" onclick="exportPDF($('{{$type}}-{{$key}}'), 
  '{{$_flows.2}} &ndash; {{if $key == "month"}}{{$month}}/{{/if}}{{$year}} &ndash; {{$title|smarty:nodefaults|JSAttribute}}')">PDF</button>
  
  <h2>{{$_flows.2}} &ndash; {{if $key == "month"}}{{$month}} / {{/if}}{{$year}}</h2>
  <h3>{{$title}}</h3>
  
  <table class="main tbl" id="{{$type}}-{{$key}}-table">
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
      <tr style="{{if $_service_id == "total"}}font-weight: bold;{{/if}} text-align: right;">
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
            <span {{if $_value.0 == 0 && $_date != "total" && $_service_id != "total"}}class="opacity-40"{{/if}}>
              <span class="quantity" style="display: none;">{{$_value.0}}</span> 
              <span class="sep" style="display: none;">/</span> 
              <span class="price">{{$_value.1}} {{$dPconfig.currency_symbol}}</span>
            </span>
          </td>
        {{/foreach}}
      </tr>
    {{/foreach}}
  </table>
  
  <table class="main graph" style="margin: 0.2em auto; width: 1%;">
    <tr>
      <td class="narrow">
        <div id="{{$type}}-{{$key}}-graph" style="width: 700px; height: 400px; margin: 0.5em auto;"></div>
      </td>
      <td id="{{$type}}-{{$key}}-legend" style="white-space: nowrap;"></td>
    </tr>
  </table>
  
</div>
{{/foreach}}

<div id="{{$type}}-rotation" style="display: none;">
  <script type="text/javascript">
    window["{{$type}}-rotation-drawFunction"] = function(){
      (function(){
        var options = {{$balance.graph.options|@json}};
        options.legend.container = $("{{$type}}-rotation-legend");
        options.mouse.trackFormatter = mouseTrackFormatter;
        Flotr.draw("{{$type}}-rotation-graph", {{$balance.graph.data|@json}}, options);
      }).delay(0.1);
      
      window["{{$type}}-rotation-drawFunction"] = function(){};
    }
    
    Main.add(function(){
      if ($("{{$type}}-rotation").visible()) {
        window["{{$type}}-rotation-drawFunction"]();
      }
    });
  </script>

  <button class="hslip singleclick not-printable" onclick="exportData($('{{$type}}-rotation-table'), null,
  'Rotation des stocks &ndash; {{$year}} &ndash; {{$title|smarty:nodefaults|JSAttribute}}')">CSV</button>
  <button class="pdf singleclick not-printable" onclick="exportPDF($('{{$type}}-rotation'), 
  'Rotation des stocks &ndash; {{$year}} &ndash; {{$title|smarty:nodefaults|JSAttribute}}')">PDF</button>
  
  <h2>Rotation des stocks &ndash; {{$year}}</h2>
  <h3>{{$title}}</h3>
  
  <table class="main tbl" id="{{$type}}-rotation-table" style="text-align: right;">
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
        
        <td>
          <span class="quantity" style="display: none;">{{$balance.in.$_date.0}}</span> 
          <span class="sep" style="display: none;">/</span> 
          <span class="price">{{$balance.in.$_date.1}} {{$dPconfig.currency_symbol}}</span>
        </td>
        
        <td>
          <span class="quantity" style="display: none;">{{$balance.out.$_date.0}}</span> 
          <span class="sep" style="display: none;">/</span> 
          <span class="price">{{$balance.out.$_date.1}} {{$dPconfig.currency_symbol}}</span>
        </td>
        
        <td>
          <span class="quantity" style="display: none;">{{$balance.diff.$_date.0}}</span> 
          <span class="sep" style="display: none;">/</span> 
          <span class="price">{{$balance.diff.$_date.1}} {{$dPconfig.currency_symbol}}</span>
        </td>
      </tr>
    {{/foreach}}
  </table>
  
  <table class="main graph" style="margin: 0.2em auto; width: 1%;">
    <tr>
      <td class="narrow">
        <div id="{{$type}}-rotation-graph" style="width: 700px; height: 400px; margin: 0.5em auto;"></div>
      </td>
      <td id="{{$type}}-rotation-legend" style="white-space: nowrap;"></td>
    </tr>
  </table>
</div>

{{if $products != null}}
<div id="{{$type}}-products" style="display: none; page-break-after: always;">
  <button class="pdf singleclick not-printable" 
  onclick="exportReport([$(this).up() {{foreach from=$flows item=_flows key=key}}, $('{{$type}}-{{$key}}'){{/foreach}}, $('{{$type}}-rotation')], '{{$year}} &ndash; {{$title|smarty:nodefaults|JSAttribute}}')">Rapport PDF</button>
  
  <h3>{{$title}}</h3>
  
  <table class="main tbl" id="{{$type}}-products-table">
    <tr>
      <th>{{tr}}CProduct{{/tr}}</th>
      <th>{{tr}}CProduct-code{{/tr}}</th>
      <th>{{tr}}CProductStockGroup-quantity{{/tr}}</th>
    </tr>
      
    {{foreach from=$products item=_product}}
      <tr>
        <td>
          <strong onmouseover="ObjectTooltip.createEx(this, '{{$_product->_guid}}')">
            {{$_product}}
          </strong>
        </td>
        <td>{{$_product->code}}</td>
        <td>{{mb_value object=$_product->_ref_stock_group field=quantity}}</td>
      </tr>
    {{/foreach}}
  </table>
</div>
{{/if}}
