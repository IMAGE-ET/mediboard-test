<form name="filterFrm" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="dialog" value="{{$dialog}}" />

<table class="form">
  <tr>
    <th class="category" colspan="6">
      {{if $list_idSante400|@count == 100}}
      Plus de 100 identifiants, seuls les 100 plus récents sont affichés
      {{else}}
      {{$list_idSante400|@count}} identifiants trouvés
      {{/if}}
    </th>
  </tr>

  <tr>
    <th>
      <label for="object_class" title="Classe de l'object">Classe</label>
    </th>
    <td>
      <select name="object_class" class="str maxLength|25">
        <option value="">&mdash; Toutes les classes</option>
        {{foreach from=$listClasses|smarty:nodefaults item=curr_class}}
        <option value="{{$curr_class}}" {{if $curr_class == $filter->object_class}}selected="selected"{{/if}}>
          {{$curr_class}}
        </option>
        {{/foreach}}
      </select>
    </td>

    <th>
      <label for="object_id" title="Identifiant de l'object">Objet</label>
    </th>
    <td>
      <input name="object_id" class="ref" value="{{$filter->object_id}}" />
      <button class="search" type="button" onclick="popObject(this)">Chercher</button>
    </td>
  </tr>
  
  <tr>
    <th>
      <label for="id400" title="Identifiant Santé 400 de l'objet">ID400</label>
    </th>
    <td>
      <input name="id400" class="str" value="{{$filter->id400}}" />
    </td>
    <th>
      <label for="tag" title="Etiquette (sémantique) de l'identifiant">Etiquette</label>
    </th>
    <td>
      <input name="tag" class="str" value="{{$filter->tag}}" />
    </td>
  </tr>

  <tr>
    <td class="button" colspan="6">
      <button class="search" type="submit">Afficher</button>
    </td>
  </tr>
</table>

</form>
