<table class="form" id="{{$class}}" style="display: none;">
  <tr>
  {{foreach from=$modeles item=owned_modeles key=owner}}
    <th class="category">{{tr}}CCompteRendu._owner.{{$owner}}{{/tr}}</th>
	{{/foreach}}
	</tr>
  <tr>
  {{foreach from=$modeles item=owned_modeles key=owner}}
    <td style="text-align: center; width: 33%;">
      <select  style="width: 90%" id="modele_{{$class}}_prat" name="modele_{{$class}}_prat" onchange="if (this.value) setClose(this.value,'{{$modelesId.$class}}');" size="20">
      {{foreach from=$owned_modeles item=modele}}
        <option value="{{$modele->_id}}">{{$modele->nom}}</option>
      {{foreachelse}}
      	<option value="">{{tr}}CCompteRendu.none{{/tr}}</option>
      {{/foreach}}
      </select>
    </td>
  {{/foreach}}
  </tr>
</table>
