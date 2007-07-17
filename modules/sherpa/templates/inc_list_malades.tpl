<table class="tbl">
	<tr>
		<th class="title" colspan="3">Malade</th>
	</tr>
	<tr>
		<th>Nom</th>
		<th>Prénom</th>
		<th>Date de naissance</th>
	</tr>
	{{foreach from=$malades item=_item}}
		<tr {{if $_item->_id == $malade->_id}}class="selected"{{/if}}>
			<td>
				<a href="index.php?m={{$m}}&amp;tab=view_malades&amp;malnum={{$_item->malnum}}&amp;" title="Modifier l'element">
					{{mb_value object=$_item field="malnom"}}
				</a>	
			</td>
			<td>{{mb_value object=$_item field="malpre"}}</td>
			<td>{{mb_value object=$_item field="datnai"}}</td>
		</tr>
	{{/foreach}}
</table>