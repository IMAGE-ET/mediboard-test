<script type="text/javascript">

function setObject(oObject){
  var oForm = document.filterFrm;
  oForm.object_class.value = oObject.class;
  oForm.object_id.value = oObject.id;
}

function popObject() {
  var oForm = document.filterFrm;
  var url = new Url;
  url.setModuleAction("system", "object_selector");
  url.addElement(oForm.object_class, "selClass");  
  url.popup(600, 300, "-");
}

</script>

{{if !$dialog}}
<form name="filterFrm" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="dialog" value="{{$dialog}}" />

<table class="form">
  <tr>
    <th class="category" colspan="5">
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
      <select name="object_class" title="str|maxLength|25">
        <option value="">&mdash; Toutes les classes</option>
        {{foreach from=$listClasses item=curr_class}}
        <option value="{{$curr_class}}" {{if $curr_class == $object_class}}selected="selected"{{/if}}>
          {{$curr_class}}
        </option>
        {{/foreach}}
      </select>
    </td>
    <th>
      <label for="tag" title="Etiquette (sémantique) de l'identifiant">Etiquette</label>
    </th>
    <td>
      <input name="tag" title="str" value="{{$tag}}" />
    </td>
  </tr>

  <tr>
    <th>
      <label for="object_id" title="Identifiant de l'object">Objet</label>
    </th>
    <td>
      <input name="object_id" title="ref" value="{{$object_id}}" />
      <button class="search" type="button" onclick="popObject()">Chercher un objet</button>
    </td>
  </tr>

  <tr>
    <td class="button" colspan="5">
      <button class="search" type="submit">Afficher</button>
    </td>
  </tr>
</table>

</form>
{{/if}}

<table class="tbl">
  {{if $dialog}}
  <tr>
    <th colspan="4" class="title">
      {{if $list_idSante400|@count > 0}}
      Historique de {{$item}}
      {{else}}
      Pas d'historique
      {{/if}}
    </th>
  </tr>
  {{/if}}
  
  <tr>
    {{if !$dialog}}
    <th>classe</th>
    <th>Objet</th>
    {{/if}}
    <th>Dernière mise à jour</th>
    <th>Etiquette</th>
  </tr>
  {{foreach from=$list_idSante400 item=curr_idSante400}}
  <tr>
    {{if !$dialog}}
    <td>{{$curr_idSante400->object_class}}</td>
    <td>{{$curr_idSante400->_ref_object->_view}} ({{$curr_idSante400->object_id}})</td>
    {{/if}}
    <td>{{$curr_idSante400->last_update|date_format:"%d/%m/%Y à %Hh%M (%A)"}}</td>
    <td>{{$curr_idSante400->tag}}</td>
  </tr>
  {{/foreach}}
</table>