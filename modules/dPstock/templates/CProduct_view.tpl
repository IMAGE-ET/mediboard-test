<table class="tbl">
	<tr>
		<th colspan="10">Produit {{$object->code}}</th>
	</tr>
	<tr>
		<th>{{mb_title class=CProduct field=name}}</th>
		<td>{{mb_value object=$object field=name}}</td>
	</tr>
	<tr>
		<th>{{mb_title class=CProduct field=description}}</th>
		<td>{{mb_value object=$object field=description}}</td>
	</tr>
	<tr>
		<th>{{mb_title class=CSociete field=name}}</th>
		<td>{{mb_value object=$object->_ref_societe field=name}}</td>
	</tr>
	<tr>
		<th>{{tr}}CProductCategory{{/tr}}</th>
		<td>{{mb_value object=$object->_ref_category field=name}}</td>
	</tr>
	<tr>
		<th>{{mb_title class=CProduct field=renewable}}</th>
		<td>{{mb_value object=$object field=renewable}}</td>
	</tr>
	<tr>
		<th>{{mb_title class=CProduct field=_unique_usage}}</th>
		<td>{{mb_value object=$object field=_unique_usage}}</td>
	</tr>
</table>
                  