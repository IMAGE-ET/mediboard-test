<table class="tbl">
	<tr>
		<th class="title" colspan="5">{{tr}}CFacture.all{{/tr}}</th>
	</tr>
	<tr>
		<th>{{tr}}CFacture-date{{/tr}}</th>
		<th>{{tr}}CFacture-all-element{{/tr}}</th>
		<th>{{tr}}CFacture-montant-element{{/tr}}</th>
	</tr>
		{{foreach from=$list_facture item=curr_facture}}
	<tr>
		<td>
			<a href="#1" onclick="showFacture('{{$curr_facture->_id}}')" 
         title="{{tr}}CFacture-title-modify{{/tr}}">{{mb_value object=$curr_facture field="date"}}
      </a>
		</td>
		<td>{{$curr_facture->_ref_items|@count}}</td>
		<td>{{mb_value object=$curr_facture field="_total"}}</td>
	</tr>
		{{/foreach}}
</table>
