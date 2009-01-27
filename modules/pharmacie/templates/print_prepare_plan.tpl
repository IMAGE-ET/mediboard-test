<script type="text/javascript">
Main.add(window.print);
</script>

<h1>Plan de cueillette du 
  {{$date_min|date_format:$dPconfig.datetime}} au 
  {{$date_max|date_format:$dPconfig.datetime}} le 
  {{$smarty.now|date_format:$dPconfig.datetime}}</h1>

<h2>Délivrances globales</h2>
<table class="main tbl">
  <colgroup>
    <col span="4" style="width: 0.1%;" />
  </colgroup>
  
  <tr>
    <th>{{tr}}CProduct-code{{/tr}}</th>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProductDeliveryTrace-quantity{{/tr}}</th>
    <th>{{tr}}CProduct-_unit_title{{/tr}}</th>
    <th>{{tr}}CProductDeliveryTrace-code{{/tr}}</th>
  </tr>
{{foreach from=$deliveries item=curr_list key=id}}
  <tr>
    <th colspan="20">{{$list_services.$id->_view}}</th>
  </tr>
  {{foreach from=$curr_list item=disp}}
  <tr>
    <td>{{$disp->_ref_stock->_ref_product->code}}</td>
    <td>{{$disp->_ref_stock->_ref_product->name}}</td>
    <td>{{$disp->quantity}}</td>
    <td>{{$disp->_ref_stock->_ref_product->_unit_title}}</td>
    <td></td>
  </tr>
  {{/foreach}}
{{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDeliveryTrace.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>