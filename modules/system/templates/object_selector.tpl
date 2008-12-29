<script type="text/javascript">

function setClose(selClass,keywords,key,val){
  var oObject = {
    objClass : selClass,
    id : key,
    view : val,
    keywords : keywords
  }
  
  var oSelector = window.opener.ObjectSelector;
  
  if (oSelector) {
    oSelector.set(oObject);
  }
  else {
    window.opener.setObject(oObject);
  }
  window.close();
}
</script>

<form action="?" name="frmSelector" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="a" value="object_selector" />
<input type="hidden" name="dialog" value="1" />
<input type="hidden" name="onlyclass" value="{{$onlyclass}}" />
{{if $onlyclass=='true'}}
<input type="hidden" name="selClass" value="{{$selClass}}" />
{{/if}}
<table class="form">
  <tr>
    <th class="category" colspan="3">Critères de sélection</th>
  </tr>
  <tr>
    <th><label for="selClass" title="Veuillez Sélectionner une Class">Choix du type d'objet</label></th>
    <td>
      <select class="notNull str" name="selClass" {{if $onlyclass=='true'}}disabled="disabled"{{/if}}>
        <option value="">&mdash; Choisissez un type</option>
        {{foreach from=$listClass|smarty:nodefaults item=curr_listClass}}
        <option value="{{$curr_listClass}}"{{if $selClass==$curr_listClass}} selected="selected"{{/if}}>{{tr}}{{$curr_listClass}}{{/tr}}</option>
        {{/foreach}}
      </select>
    </td>
    <td></td>
  </tr>
  <tr>
    <th><label for="keywords" title="Veuillez saisir un ou plusieurs mot clé">Mots Clés</label></th>
    <td><input class="notNull str" type="text" name="keywords" value="{{$keywords|stripslashes}}" /></td>
    <td><button class="search" type="submit">Rechercher</button></td>
  </tr>
</table>
</form>

{{if $selClass}}
<table class="tbl">
  <tr>
    <th align="center" colspan="2">Résultat de la recherche</th>
  </tr>
  
  {{foreach from=$list item=curr_list}}
    <tr>
      <td>{{$curr_list->_view}}</td>     
      <td class="button"><button type="button" class="tick" onclick="setClose('{{$selClass}}', '{{$keywords|stripslashes|smarty:nodefaults|JSAttribute}}', {{$curr_list->_id}}, '{{$curr_list->_view|smarty:nodefaults|JSAttribute}}')">Sélectionner</button></td>
    </tr>
  {{/foreach}}
</table>
{{/if}}