<table class="main tbl">
	<tr>
    <th class="narrow"></th>
    <th colspan="3">
    	{{if $group_by == "praticien"}}
    	  Praticien
			{{else}}
			  Labo
			{{/if}}
		</th>
    <th>Total</th>
	</tr>
	{{foreach from=$dmi_lines_count item=_stat}}
	  <tbody>
		  <tr>
		  	<td>
		  		<button class="lookup" onclick="loadDetails($(this).up('tbody').next('tbody'), {
             _date_min: '{{$date_min}}', 
             _date_max: '{{$date_max}}', 
						 {{if $group_by == "praticien"}} 
						   chir_id: '{{$_stat.$group_by->_id}}',
						 {{else}}
						   _labo_id: '{{$_stat.$group_by->_id}}',
						 {{/if}}
             septic: '{{$septic}}'
					})">
					  {{tr}}Details{{/tr}}
					</button>
					<button class="save" onclick="downloadDetails({
             _date_min: '{{$date_min}}', 
             _date_max: '{{$date_max}}', 
             {{if $group_by == "praticien"}} 
               chir_id: '{{$_stat.$group_by->_id}}',
             {{else}}
               _labo_id: '{{$_stat.$group_by->_id}}',
             {{/if}}
             septic: '{{$septic}}'
          })">
            Télécharger fichier CSV
					</button>
		    </td>
		  	<td colspan="3" style="font-weight: bold;">
		      {{if $group_by == "praticien"}}
		        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_stat.$group_by}}
		      {{elseif $group_by == "labo"}}
					  <a class="button search" href="?m=dmi&tab=vw_tracabilite&societe_id={{$_stat.$group_by->_id}}&septic={{$septic}}">
					  	Traçabilité
					  </a>
					  <span onmouseover="ObjectTooltip.createEx(this, '{{$_stat.$group_by->_guid}}')">
		          {{$_stat.$group_by}}
						</span>
		      {{/if}}
				</td>
				<td>{{$_stat.sum}}</td>
			</tr>
		</tbody>
		<tbody style="display: none;"></tbody>
	{{foreachelse}}
	  <tr>
	  	<td colspan="4" class="empty">
	  		Aucune valeur pour ces critères
	  	</td>
	  </tr>
	{{/foreach}}
</table>