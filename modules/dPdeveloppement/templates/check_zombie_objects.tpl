<form name="class-selection" action="" method="get">
	<input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
	<label>Classe:
	<select name="class_name" onchange="this.form.submit()">
		{{foreach from=$classes item=curr_class}}
		  <option value="{{$curr_class}}" {{if $class_name == $curr_class}}selected="selected"{{/if}}>{{$curr_class}} - {{tr}}{{$curr_class}}{{/tr}}</option>
		{{/foreach}}
	</select>
	</label>
	<label>Nombre d'objets à afficher:<input type="text" name="count" value="{{$objects_count}}" size="4" /></label>
</form>

<table class="tbl main">
	<tr>
		<th>Back name</th>
		<th>Back title</th>
		<th>Nombre</th>
	</tr>
	{{foreach from=$zombies key=name item=zombie}}
	  <tr class="category" id="zombie-{{$name}}-toggle-trigger">
	    <td style="font-weight: bold;">{{$name}}</td>
			<td style="font-weight: bold;">{{tr}}{{$class_name}}-back-{{$name}}{{/tr}}</td>
			<td>
				{{$zombie.count}} 
        <script type="text/javascript">Main.add(function(){new PairEffect('zombie-{{$name}}-toggle')});</script>
			</td>
		</tr>
		<tr id="zombie-{{$name}}-toggle">
			<td colspan="3">
				{{foreach from=$zombie.objects item=curr_object}}
				  <div class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$curr_object->_guid}}')">
				  	{{$curr_object}}
					</div>
				{{foreachelse}}
				  {{tr}}None{{/tr}}
				{{/foreach}}
			</td>
		</tr>
	{{foreachelse}}
	<tr><td colspan="3">La classe n'a pas de table associée</td></tr>
	{{/foreach}}
</table>