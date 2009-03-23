
<form action="?" name="Filter" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  
	<table class="form">
		<tr>
			<th>
			  <label for="class_name" title="Veuillez sélectionner une classe">Choix de la classe</label>
			</th>

			<td>
			  <select name="class_name" onchange="submit();">
			    <option value="">&mdash; Toutes les classes</option>
					{{foreach from=$classes item=class}}  
			    <option value="{{$class}}"{{if $class_name == $class}} selected="selected"{{/if}}>
			    	{{$class}} ({{tr}}{{$class}}{{/tr}})
			    </option>
			    {{/foreach}}
			  </select>
			</td>

			<th>
			  <label for="show" title="Mode d'affichage">Afficher</label>
			</th>

			<td>
			  <select name="show" onchange="submit();">
			    <option value="all"    {{if $show == "all"   }}selected="selected"{{/if}}>Tout</option>
			    <option value="errors" {{if $show == "errors"}}selected="selected"{{/if}}>Les erreurs</option>
			  </select>
			</td>

		</tr>
	</table>

</form>

<table class="tbl">
{{foreach from=$reports key=class item=_report}}  
  <tr>
  	<th class="title" colspan="10">
    	{{$class}} ({{tr}}{{$class}}{{/tr}})
    </th>
  </tr>
  
	<tr>
	  <th>BackProp</th>
	  <th>Present</th>
	  <th>Wanted</th>
	  <th>BackName</th>
	</tr>

	{{assign var=style value="text-align: center; text-transform: uppercase; font-weight: bold;"}}
  {{foreach from=$_report key=backProp item=value}}
  <tr>
    <td>{{$backProp}}</td>
    {{if $value == "ok"}}
    <td class="ok" colspan="2" style="{{$style}}>{{$value}}">{{$value}}</td>
    {{/if}}
    
    {{if $value == "present"}}
    <td class="warning" style="{{$style}}">{{$value}}</td>
    <td />
    {{/if}}
    
    {{if $value == "wanted"}}
    <td />
    <td class="warning" style="{{$style}}">{{$value}}</td>
    {{/if}}
    
    <td>
    {{if $value != "wanted"}}
    {{$present.$class.$backProp}}
    {{/if}}
    </td>
    
  </tr>
  
	{{/foreach}}
{{/foreach}}
</table>
