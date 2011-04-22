<table class="tbl">
	<tr>
		<th class="title" colspan="5">{{tr}}CFacturecatalogueitem.all{{/tr}}</th>
	</tr>
	<tr>
	   <th>{{tr}}CFacturecatalogueitem-libelle{{/tr}}</th>
	   <th>{{tr}}CFacturecatalogueitem-prix_ht{{/tr}}</th>
	   <th>{{tr}}CFacturecatalogueitem-taxe{{/tr}}</th>
	   <th>{{tr}}CFacturecatalogueitem-_ttc{{/tr}}</th>
	</tr> 
		{{foreach from=$catalogue_list item=_cataloguefacture}}
	<tr>
		<td>
			<a href="#1" onclick="showEditCatalogueFacture('{{$_cataloguefacture->_id}}')" 
         title="{{tr}}CFacturecatalogueitem-title-modify{{/tr}}">{{mb_value object=$_cataloguefacture field="libelle"}}
      </a>
		</td>
		<td>{{mb_value object=$_cataloguefacture field="prix_ht"}}</td>
		<td>{{mb_value object=$_cataloguefacture field="taxe"}}</td>
		<td>{{mb_value object=$_cataloguefacture field="_ttc"}}</td>
	</tr>
		{{/foreach}}
</table>
