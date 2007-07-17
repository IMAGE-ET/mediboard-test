<table class="tbl">
	<tr>
		<th class="title" colspan="3">Etablissement</th>
	</tr>
	<tr>
		<th>Groupe</th>
		<th>Increment Year</th>
		<th>Increment Patient</th>
	</tr>
	{{foreach from=$etablissements item=_item}}
		<tr {{if $_item->_id == $etablissement->_id}}class="selected"{{/if}}>
			<td>
				<a href="index.php?m={{$m}}&amp;tab=view_etablissements&amp;sp_etab_id={{$_item->sp_etab_id}}&amp;" title="Modifier l'element">
					{{$_item->_ref_group->_view}}
				</a>	
			</td>
			<td>{{mb_value object=$_item field="increment_year"}}</td>
			<td>{{mb_value object=$_item field="increment_patient"}}</td>
		</tr>
	{{/foreach}}
</table>