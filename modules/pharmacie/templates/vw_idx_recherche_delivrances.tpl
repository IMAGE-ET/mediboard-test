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
      <th><strong>OU</strong> {{mb_label object=$delivery_trace field=delivery_trace_id}}</th>
      <td>{{mb_field object=$delivery_trace field=delivery_trace_id size=20}}</td>
			<td>
        <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
			</td>
		</tr>
	</table>
	
	{{if $delivery_traces|@count == 1000}}
		<div class="small-info">
			Seuls les 1000 premières délivrances sont affichées
		</div>
	{{/if}}
</form>

<h3>
	{{if $delivery->service_id}}
	  {{mb_value object=$delivery field=service_id}} &mdash; 
	{{/if}}
	{{mb_value object=$delivery field=_datetime_min}} - {{mb_value object=$delivery field=_datetime_max}}
	({{$delivery_traces|@count}} délivrances)
	
	<button type="button" class="change not-printable" onclick="$('table-nodetails').toggle(); $('table-details').toggle(); ">
		Afficher / cacher les détails
	</button>
	
  <button type="button" class="print not-printable" onclick="window.print()">
    {{tr}}Print{{/tr}}
  </button>
</h3>

<table class="main tbl" id="table-details">
	<tr>
		{{* <th class="narrow"></th> *}}
    <th class="narrow">{{mb_title class=CProductDeliveryTrace field=date_delivery}}</th>
		{{if !$delivery->service_id}}
      <th>{{mb_title class=CProductDelivery field=service_id}}</th>
		{{/if}}
    <th>{{mb_title class=CProductDelivery field=patient_id}}</th>
    <th>{{mb_title class=CProductStockGroup field=location_id}}</th>
    <th>Raison</th>
    {{* <th>{{mb_title class=CProductDelivery field=comments}}</th> *}}
    <th>{{mb_title class=CProductDeliveryTrace field=target_location_id}}</th>
    <th>{{mb_title class=CProductDeliveryTrace field=quantity}}</th>
	</tr>
	
  {{foreach from=$products item=_product}}
		<tr>
			<th class="title" style="text-align: left;" colspan="{{$delivery->service_id|ternary:5:6}}">{{$_product}}</th>
			<th class="title" style="text-align: left;">
				{{$_product->_total}}
				{{$_product->_unit_title|truncate:30}}
			</th>
		</tr>
		{{foreach from=$_product->_traces item=_trace}}
		  <tr>
		  	{{* <td><button type="button" onclick="deliveryTraceInfo({{$_trace->_id}})" class="search"></button> *}}
	      <td>{{mb_value object=$_trace field=date_delivery}}</td>
		    {{if !$delivery->service_id}}
		      <td>{{mb_value object=$_trace->_ref_delivery field=service_id}}</td>
				{{/if}}
	      <td>{{mb_value object=$_trace->_ref_delivery field=patient_id}}</td>
	      <td>{{mb_value object=$_trace->_ref_delivery->_ref_stock->_ref_location field=name}}</td>
        <td>{{mb_value object=$_trace field=target_location_id}}</td>
        <td>{{if $_trace->_ref_delivery->type}}{{mb_value object=$_trace->_ref_delivery field=type}}{{/if}}</td>
        {{* <td>{{mb_value object=$_trace->_ref_delivery field=comments}}</td> *}}
	      <td style="background: {{if $_trace->quantity > 0}}#dfd{{else}}#fdd{{/if}}">
				  {{mb_value object=$_trace field=quantity}}
				</td>
			</tr>
		{{/foreach}}
  {{/foreach}}
</table>

<table class="main tbl" id="table-nodetails" style="display: none;">
  <tr>
    <th></th>
    <th>{{mb_title class=CProductDeliveryTrace field=quantity}}</th>
  </tr>
  
  {{foreach from=$products item=_product}}
    <tr>
      <td><strong>{{$_product}}</strong></th>
      <td>
        {{$_product->_total}} {{$_product->_unit_title|truncate:30}}
      </td>
    </tr>
  {{/foreach}}
</table>