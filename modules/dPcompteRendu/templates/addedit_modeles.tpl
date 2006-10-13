<script type="text/javascript">
function nouveau() {
  var url = new Url;
  url.setModuleTab("dPcompteRendu", "addedit_modeles");
  url.addParam("compte_rendu_id", "0");
  url.redirect();
}

function supprimer() {
  var form = document.editFrm;
  form.del.value = 1;
  form.submit();
}
{{if !$compte_rendu->compte_rendu_id}}
var listObjectClass = {{$listObjectClass|@json}};

var aTraducClass = new Array();
{{foreach from=$listObjectAffichage key=key item=currClass}}
aTraducClass["{{$key}}"] = "{{$currClass}}";
{{/foreach}}

function loadObjectClass(value) {
  var form = document.editFrm;
  var select = form.elements['object_class'];
  var options = listObjectClass;
  // delete all former options except first
  while (select.length > 1) {
    select.options[1] = null;
  }
  // insert new ones
  for (var elm in options) {
    var option = elm;
    if (typeof(options[option]) != "function") { // to filter prototype functions
      select.options[select.length] = new Option(aTraducClass[option], option, option == value);
    }
  }
  loadCategory();
}

function loadCategory(value){
  var form = document.editFrm;
  var select = form.elements['file_category_id'];
  var className  = form.elements['object_class'].value;
  var options = listObjectClass[className];
  // delete all former options except first
  while (select.length > 1) {
    select.options[1] = null;
  }
  // insert new ones
  for (var elm in options) {
    var option = options[elm];
    if (typeof(option) != "function") { // to filter prototype functions
      select.options[select.length] = new Option(option, elm, elm == value);
    }
  }
}

function pageMain() {
  loadObjectClass('{{$compte_rendu->object_class}}');
  loadCategory('{{$compte_rendu->file_category_id}}');
}
{{/if}}
</script>

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_modele_aed" />
<input type="hidden" name="compte_rendu_id" value="{{$compte_rendu->compte_rendu_id}}" />

<table class="main">

<tr>
  <td>
    {{if $compte_rendu->compte_rendu_id}}
    <button class="new" type="button" onclick="nouveau()">
      Créer un modèle
    </button>
    {{/if}}  
<table class="form">
  <tr>
    <th class="category" colspan="2">
      {{if $compte_rendu->compte_rendu_id}}
      <a style="float:right;" href="javascript:view_log('CCompteRendu',{{$compte_rendu->compte_rendu_id}})">
        <img src="images/history.gif" alt="historique" />
      </a>
      {{/if}}
      Informations sur le modèle
    </th>
  </tr>
  
  <tr>
    <th><label for="nom" title="Intitulé du modèle. Obligatoire">Nom</label></th>
    <td><input type="text" name="nom" value="{{$compte_rendu->nom}}" title="{{$compte_rendu->_props.nom}}" /></td>
  </tr>
  
  <tr>
    <th><label for="function_id" title="Fonction à laquelle le modèle est associé">Fonction</label></th>
    <td>
      <select name="function_id" title="{{$compte_rendu->_props.function_id}}">
        <option value="">&mdash; Associer à une fonction &mdash;</option>
        {{foreach from=$listFunc item=curr_func}}
          <option value="{{$curr_func->function_id}}" {{if $curr_func->function_id == $compte_rendu->function_id}} selected="selected" {{/if}}>
            {{$curr_func->_view}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th><label for="chir_id" title="Praticien auquel le modèle est associé">Praticien</label></th>
    <td>
      <select name="chir_id" title="{{$compte_rendu->_props.chir_id}}">
        <option value="">&mdash; Associer à un praticien &mdash;</option>
        {{foreach from=$listPrat item=curr_prat}}
          <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $prat_id}} selected="selected" {{/if}}>
            {{$curr_prat->_view}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th><label for="object_class" title="Type d'objet concerné, obligatoire.">Objet</label></th>
    <td>
      {{if !$compte_rendu->compte_rendu_id}}
      <select name="object_class" title="{{$compte_rendu->_props.object_class}}" onchange="loadCategory()">
        <option value="">&mdash; Choisir un objet</option>
      </select>
      {{else}}
      {{$compte_rendu->object_class}}
      {{/if}}
    </td>
  </tr>
  
  <tr>
    <th><label for="file_category_id" title="Catégorie du document">Catégorie</label></th>
    <td>
      {{if !$compte_rendu->compte_rendu_id}}
      <select name="file_category_id" title="{{$compte_rendu->_props.file_category_id}}">
        <option value="0">&mdash; Aucune Catégorie</option>
      </select>
      {{else}}
        {{if $compte_rendu->_ref_category->file_category_id}}
        {{$compte_rendu->_ref_category->nom}}
        {{else}}
        Aucune Catégorie
        {{/if}}
      {{/if}}
    </td>
  </tr>
  
  <tr>
    <td class="button" colspan="2">
    {{if $compte_rendu->compte_rendu_id}}
      <button class="modify" type="submit">Modifier</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le modèle',objName:'{{$compte_rendu->nom|smarty:nodefaults|JSAttribute}}'})">
        Supprimer
      </button>
    {{else}}
      <button class="submit" type="submit">Créer</button>
    {{/if}}
    </td>
  </tr>
</table>

  </td>
  <td class="greedyPane" style="height: 500px">
  {{if $compte_rendu->compte_rendu_id}}
    <textarea id="htmlarea" name="source">
    {{$compte_rendu->source}}
    </textarea>
  {{/if}}
  </td>
</tr>

</table>

</form>