<form name="class-selection" action="" method="get">
	<input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
	<label>Classe:
	<select name="class_name" onchange="this.form.submit()">
		{{foreach from=$classes item=_class}}
		  <option value="{{$_class}}" {{if $class_name == $_class}}selected="selected"{{/if}}>{{$_class}} - {{tr}}{{$_class}}{{/tr}}</option>
		{{/foreach}}
	</select>
	</label>
	<label>Nombre d'objets à afficher:<input type="text" name="count" value="{{$objects_count}}" size="4" /></label>
</form>

<table class="tbl main">
	<tr>
		<th>Collection</th>
		<th>Violations</th>
	</tr>
	{{foreach from=$zombies key=name item=zombie}}
	  <tr>
			<td style="font-weight: bold;">
			  {{assign var=initiator value=$object->_backSpecs[$name]->_initiator}}
			  {{tr}}{{$initiator}}-back-{{$name}}{{/tr}}
			</td>
			
			<td>
				<div class="{{$zombie.count|ternary:warning:info}}">
					<span onmouseover="ObjectTooltip.createDOM(this, '{{$_class}}-{{$name}}')">
						{{$zombie.count}}
					</span>
				</div>
				
				<table class="tbl" id="{{$_class}}-{{$name}}" style="display: none;">
				  <tr>
				  	<th>{{$initiator}}.{{$name}}</th>
					</tr>
	        {{foreach from=$zombie.objects item=_object}}
          <tr>
            <td>
		          <span onmouseover="ObjectTooltip.createEx(this, '{{$_object->_guid}}')">
		            {{$_object}}
		          </span>
            </td>
          </tr>
          {{foreachelse}}
					<tr><td><em>{{tr}}None{{/tr}}</em></td></tr>
	        {{/foreach}}
        </table>
			</td>

		</tr>
	{{foreachelse}}
	<tr><td colspan="3">La classe n'est référencé dans aucune collection</td></tr>
	{{/foreach}}
</table>