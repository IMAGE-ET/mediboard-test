<script type="text/javascript">
function loadDetails(element, params) {
  if (element.visible()) {
    element.hide();
    return;
  }
  
  element.show();
  
  var url = new Url("dmi", "ajax_vw_stats_detail");
  url.mergeParams(params);
  url.requestUpdate(element);
}

function downloadDetails(params) {
  var url = new Url("dmi", "ajax_vw_stats_detail");
  url.mergeParams(params);
  url.addParam("csv", 1);
	url.addParam("suppressHeaders", 1);
  url.pop(10, 10, "export", null, null, {}, Element.getTempIframe());
}
</script>


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
        <label for="group_by">Grouper par </label>
      </th>
      <td>
        <select name="group_by">
          <option value="praticien" {{if $group_by == "praticien"}} selected="selected" {{/if}}>Praticien</option>
          <option value="labo" {{if $group_by == "labo"}} selected="selected" {{/if}}>Laboratoire</option>
          <option value="code_lpp" {{if $group_by == "code_lpp"}} selected="selected" {{/if}}>Code LPP</option>
        </select>
      </td>
			
			<th>
        {{mb_label object=$dmi_line field=septic}}
        {{mb_field object=$dmi_line field=septic typeEnum=checkbox}}
			</th>
		</tr>
		
		<tr>
      <th>{{mb_title object=$interv field=_date_max}}</th>
      <td>{{mb_field object=$interv field=_date_max register=true form="filter-dmi-stats"}}</td>
      
      <th>{{mb_title object=$dmi field=_labo_id}}</th>
      <td>{{mb_field object=$dmi field=_labo_id form="filter-dmi-stats" autocomplete="true,1,50,true,true"}}</td>
      
      <th>{{mb_title object=$dmi field=code_lpp}} (partiel ou complet)</th>
      <td>{{mb_field object=$dmi field=code_lpp}}</td>
			
      <td>
        <button class="search">{{tr}}Filter{{/tr}}</button>
      </td>
		</tr>
	</table>
</form>

<div id="stats-list"></div>
