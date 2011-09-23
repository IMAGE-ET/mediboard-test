<script type="text/javascript">
checkFilterForm = function(form) {
  if (checkForm(form) && !$V(form.delivery_trace_id) && !$V(form._datetime_min) && !$V(form._datetime_max)) {
	  alert("Veuillez choisir un intervalle de dates ou un numéro d'ordonnancier");
	  return false;
	}
	
	return true;
}

deliveryTraceInfo = function(delivery_trace_id) {
  var url = new Url("system", "httpreq_vw_object");
	url.addParam("object_class", "CProductDeliveryTrace");
  url.addParam("object_id", delivery_trace_id);
	url.modal();
}
</script>

<form name="filter-recherche-delivrances" method="get" action="?" class="not-printable" onsubmit="return checkFilterForm(this)">
  <input type="hidden" name="m" value="pharmacie" />
  <input type="hidden" name="tab" value="vw_idx_recherche_delivrances" />
	
	<table class="main form">
		<tr>
      <th>{{mb_label object=$delivery field=_date_min}}</th>
      <td>{{mb_field object=$delivery field=_datetime_min register=true form="filter-recherche-delivrances" prop="dateTime"}}</td>
      <th>{{mb_label object=$delivery field=_date_max}}</th>
      <td>{{mb_field object=$delivery field=_datetime_max register=true form="filter-recherche-delivrances" prop="dateTime"}}</td>
      <th>{{mb_label object=$delivery field=service_id}}</th>
      <td>
        <select name="service_id">
        	<option value=""> &ndash; Tous </option>
        {{foreach from=$services item=_service}}
          <option value="{{$_service->_id}}" {{if $delivery->service_id == $_service->_id}}selected="selected"{{/if}}>{{$_service}}</option>
        {{/foreach}}
        </select>
      </td>
      <th>{{mb_label object=$delivery_trace field=delivery_trace_id}}</th>
      <td>{{mb_field object=$delivery_trace field=delivery_trace_id}}</td>
			<td><button type="submit" class="search">{{tr}}Search{{/tr}}</button></td>
		</tr>
	</table>
	
	<div class="small-info">
		Seuls les 1000 premières délivrances sont affichées
	</div>
</form>

<h3>
	{{if $delivery->service_id}}
	  {{mb_value object=$delivery field=service_id}} &mdash; 
	{{/if}}
	{{mb_value object=$delivery field=_datetime_min}} - {{mb_value object=$delivery field=_datetime_max}}
	({{$delivery_traces|@count}} délivrances)
</h3>

<table class="main tbl">
	<tr>
		{{* <th class="narrow"></th> *}}
    <th class="narrow">{{mb_title class=CProductDeliveryTrace field=date_delivery}}</th>
		{{if !$delivery->service_id}}
      <th>{{mb_title class=CProductDelivery field=service_id}}</th>
		{{/if}}
    <th>{{mb_title class=CProductDelivery field=patient_id}}</th>
    <th>{{mb_title class=CProductStockGroup field=location_id}}</th>
    <th>{{mb_title class=CProductDelivery field=stock_id}}</th>
    <th>{{mb_title class=CProductDeliveryTrace field=quantity}}</th>
    <th>{{mb_title class=CProductDeliveryTrace field=target_location_id}}</th>
    <th>Raison</th>
    {{* <th>{{mb_title class=CProductDelivery field=comments}}</th> *}}
	</tr>
	{{foreach from=$delivery_traces item=_trace}}
	  <tr>
	  	{{* <td><button type="button" onclick="deliveryTraceInfo({{$_trace->_id}})" class="search"></button> *}}
      <td>{{mb_value object=$_trace field=date_delivery}}</td>
	    {{if !$delivery->service_id}}
	      <td>{{mb_value object=$_trace->_ref_delivery field=service_id}}</td>
			{{/if}}
      <td>{{mb_value object=$_trace->_ref_delivery field=patient_id}}</td>
      <td>{{mb_value object=$_trace->_ref_delivery->_ref_stock->_ref_location field=name}}</td>
      <td><strong>{{mb_value object=$_trace->_ref_delivery field=stock_id}}</strong></td>
      <td style="background: {{if $_trace->quantity > 0}}#dfd{{else}}#fdd{{/if}}">
			  {{mb_value object=$_trace field=quantity}}
				{{$_trace->_ref_delivery->_ref_stock->_ref_product->_unit_title|truncate:30}}
			</td>
      <td>{{mb_value object=$_trace field=target_location_id}}</td>
      <td>{{if $_trace->_ref_delivery->type}}{{mb_value object=$_trace->_ref_delivery field=type}}{{/if}}</td>
      {{* <td>{{mb_value object=$_trace->_ref_delivery field=comments}}</td> *}}
		</tr>
	{{/foreach}}
</table>