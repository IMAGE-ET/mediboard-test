<table class="tbl">
  <!-- Affichage des delivrances globales -->
  <tr><th colspan="10">
    Délivrances globales
  </th></tr>
  <tr>
    <th>{{tr}}CProductDelivery-service_id{{/tr}}</th>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date_dispensation{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date_delivery{{/tr}}</th>
    <th style="width: 1%">{{tr}}CProductDelivery-code{{/tr}}</th>
    <th></th>
  </tr>
  {{foreach from=$deliveries_global item=curr_delivery_global}}
    {{include file="inc_restockages_service_line.tpl" curr_delivery=$curr_delivery_global mode_nominatif=0}}
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDelivery.global.none{{/tr}}</td>
  </tr>
  {{/foreach}}
  
  <!-- Affichage des delivrances nominatives -->
  <tr><th colspan="10">
    Délivrances nominatives
  </th></tr>
  <tr>
    <th>{{tr}}CProductDelivery-patient_id{{/tr}}</th>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date_dispensation{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date_delivery{{/tr}}</th>
    <th style="width: 1%">{{tr}}CProductDelivery-code{{/tr}}</th>
    <th></th>
  </tr>
  {{foreach from=$deliveries_nominatif item=curr_delivery_nominatif}}
    {{include file="inc_restockages_service_line.tpl" curr_delivery=$curr_delivery_nominatif mode_nominatif=1}}
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDelivery.nominatif.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
