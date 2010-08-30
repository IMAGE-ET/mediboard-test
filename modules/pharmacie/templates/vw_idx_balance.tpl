{{* $Id: vw_idx_delivrance.tpl 9733 2010-08-04 14:03:11Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 9733 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("balance-tabs");
});

</script>

<form name="filter-product" method="get" action="">
  <input type="hidden" name="m" value="pharmacie" />
  <input type="hidden" name="tab" value="vw_idx_balance" />
  
  {{mb_field object=$stock field=product_id form="filter-product" autocomplete="true,1,50,false,true" style="width:300px; font-size: 1.4em;"}}
  
  <button type="submit" class="search notext">{{tr}}Search{{/tr}}</button>
</form>

<ul class="control_tabs" id="balance-tabs">
  <li><a href="#year">Bilan annuel</a></li>
  <li><a href="#month">Bilan mensuel</a></li>
  <li><a href="#rotation">Rotation des stocks</a></li>
</ul>
<hr class="control_tabs" />

{{math equation="1/(x-1)" x=$services|@count assign=width}}

{{foreach from=$flows item=_flows key=key}}

<table class="main tbl" id="{{$key}}" style="display: none;">
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

<table class="main tbl" id="rotation" style="display: none;">
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

