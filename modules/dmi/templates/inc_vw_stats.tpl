<table class="main tbl">
	<tr>
    <th>
    	{{if $group_by == "praticien"}}
    	  Praticien
			{{else}}
			  Labo
			{{/if}}
		</th>
    <th>Total</th>
	</tr>
	{{foreach from=$dmi_lines_count item=_stat}}
	  <tr>
	  	<td>
	      {{if $group_by == "praticien"}}
	        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_stat.$group_by}}
	      {{elseif $group_by == "labo"}}
				  <a class="button lookup" href="?m=dmi&tab=vw_tracabilite&societe_id={{$_stat.$group_by->_id}}&septic={{$septic}}">
				  	Détails
				  </a>
				  <span onmouseover="ObjectTooltip.createEx(this, '{{$_stat.$group_by->_guid}}')">
	          {{$_stat.$group_by}}
					</span>
	      {{/if}}
			</td>
			<td>{{$_stat.sum}}</td>
		</tr>
	{{foreachelse}}
	  <tr>
	  	<td colspan="2" class="empty">
	  		Aucune valeur pour ces critères
	  	</td>
	  </tr>
	{{/foreach}}
</table>