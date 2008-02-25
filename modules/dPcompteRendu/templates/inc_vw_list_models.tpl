{{if $list_modeles.prat|@count || $list_modeles.func|@count}}
<div id="{{$object_class}}">
  <table class="form">
    <tr>
      {{if $list_modeles.prat|@count}}<th class="category">Modèles du praticien</th>{{/if}}
      {{if $list_modeles.func|@count}}<th class="category">Modèles de la fonction</th>{{/if}}
    </tr>
    <tr>
      {{if $list_modeles.prat|@count}}
      <td style="text-align:center">
				<select id="modele_{{$object_class}}_prat" name="modele_{{$object_class}}_prat" onchange="if (this.value) setClose(this.value,'{{$object_id}}');" size="20">
				  {{foreach from=$list_modeles.prat item=model}}
				  <option value="{{$model->_id}}">{{$model->nom}}</option>
				  {{/foreach}}
				</select>
			</td>
			{{/if}}
			{{if $list_modeles.func|@count}}
			<td style="text-align:center">
				<select id="modele_{{$object_class}}_func" name="modele_{{$object_class}}_func" onchange="if (this.value) setClose(this.value,'{{$object_id}}');" size="20">
				  {{foreach from=$list_modeles.func item=model}}
				  <option value="{{$model->_id}}">{{$model->nom}}</option>
				  {{/foreach}}
				</select>
		  </td>
		  {{/if}}
    </tr>
  </table>
</div>
{{/if}}