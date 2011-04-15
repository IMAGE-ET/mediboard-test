
<form name="filter-dmi-stats" method="get" action="?" onsubmit="return Url.update(this, 'stats-list')">
  <input type="hidden" name="m" value="dmi" />
  <input type="hidden" name="a" value="ajax_vw_stats" />
	
	<table class="main form">
	  <tr>
	    <th>{{mb_title object=$interv field=_date_min}}</th>
	    <td>{{mb_field object=$interv field=_date_min register=true form="filter-dmi-stats"}}</td>
			
      
      <th>{{mb_title object=$interv field=chir_id}}</th>
      <td>
      	<select name="chir_id">
      		<option value=""> &ndash; Tous les praticiens </option>
					{{foreach from=$list_chir item=_user}}
					  <option value="{{$_user->_id}}">{{$_user}}</option>
					{{/foreach}}
				</select>
			</td>
			
			<th>
        {{mb_label object=$dmi_line field=septic}}
        {{mb_field object=$dmi_line field=septic typeEnum=checkbox}}
			</th>
	    <td>
	    	<button class="search">{{tr}}Filter{{/tr}}</button>
	    </td>
		</tr>
		
		<tr>
      <th>{{mb_title object=$interv field=_date_max}}</th>
      <td>{{mb_field object=$interv field=_date_max register=true form="filter-dmi-stats"}}</td>
      
      <th>{{mb_title object=$dmi field=_labo_id}}</th>
      <td>{{mb_field object=$dmi field=_labo_id form="filter-dmi-stats" autocomplete="true,1,50,true,true"}}</td>
      
      <th>
        <label for="group_by">Grouper par </label>
      </th>
      <td>
        <select name="group_by">
          <option value="praticien" {{if $group_by == "praticien"}} selected="selected" {{/if}}>Praticien</option>
          <option value="labo" {{if $group_by == "labo"}} selected="selected" {{/if}}>Laboratoire</option>
        </select>
      </td>
		</tr>
	</table>
</form>

<div id="stats-list"></div>
