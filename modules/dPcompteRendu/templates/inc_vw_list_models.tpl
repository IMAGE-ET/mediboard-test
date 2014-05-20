<script>
  Main.add(function() {
    var url = new Url("compteRendu", "ajax_modele_autocomplete");
    url.addParam("user_id", "{{$praticien->_id}}");
    url.addParam("object_class", '{{$class}}');
    url.addParam("object_id"   , '{{$target_id}}');
    url.addParam("fast_edit"   , 0);
    url.autoComplete(getForm("chooseDoc_{{$class}}").keywords_modele, '', {
      minChars: 2,
      updateElement: createDoc,
      dropdown: true,
      width: "250px"
    });
  });
  function createDoc(selected) {
    var id = selected.down(".id").innerHTML;
    setClose(id, '{{$modelesId.$class}}');
  }
</script>

<table class="form" id="{{$class}}" style="display: none;">
  <tr>
    <td>
      <!-- Autocomplete pour choisir un modèle -->
      <form name="chooseDoc_{{$class}}" method="get" action="?">
        <input type="text" value="&mdash; Modèle" name="keywords_modele" class="autocomplete str" autocomplete="off" style="width: 215px;" />
      </form>
    </td>
  </tr>
  <tr>
  {{foreach from=$modeles item=owned_modeles key=owner}}
    <th class="category">{{tr}}CCompteRendu._owner.{{$owner}}{{/tr}}</th>
	{{/foreach}}
	</tr>
  <tr>
  {{foreach from=$modeles item=owned_modeles key=owner}}
    <td style="text-align: center; width: 33%;">
      <select  style="width: 90%" id="modele_{{$class}}_prat" name="modele_{{$class}}_prat"
               onchange="if (this.value) setClose(this.value,'{{$modelesId.$class}}', this.options[this.selectedIndex].get('fast_edit'));" size="20">
      {{foreach from=$owned_modeles item=modele}}
        <option value="{{$modele->_id}}" data-fast_edit="{{if $modele->fast_edit || $modele->fast_edit_pdf}}1{{else}}0{{/if}}">{{$modele->nom}}</option>
      {{foreachelse}}
      	<option value="">{{tr}}CCompteRendu.none{{/tr}}</option>
      {{/foreach}}
      </select>
    </td>
  {{/foreach}}
  </tr>
</table>
