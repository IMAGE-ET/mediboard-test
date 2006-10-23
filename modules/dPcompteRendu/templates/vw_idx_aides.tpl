<!--  $Id$ -->

<script type="text/javascript">

var classes = {{$classes|@json}};

var aTraducClass = new Array();
{{foreach from=$listObjectAffichage key=key item=currClass}}
aTraducClass["{{$key}}"] = "{{$currClass}}";
{{/foreach}}

function loadClasses(value) {
  var form = document.editFrm;
  var select = form.elements['class'];
  var options = classes;
  
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

  loadFields();
}

function loadFields(value) {
  var form = document.editFrm;
  var select = form.elements['field'];
  var className  = form.elements['class'].value;
  var options = classes[className];

  // delete all former options except first
  while (select.length > 1) {
    select.options[1] = null;
  }

  // insert new ones
  for (var elm in options) {
    var option = options[elm];
    if (typeof(option) != "function") { // to filter prototype functions
      select.options[select.length] = new Option(option, option, option == value);
    }
  }
}

function pageMain() {
  loadClasses('{{$aide->class}}');
  loadFields('{{$aide->field}}');
}

</script>

<table class="main">

<tr>
  <td class="greedyPane">

    <form name="filterFrm" action="?" method="get">
    
    <input type="hidden" name="m" value="{{$m}}" />
    <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;aide_id=0" class="buttonnew"><strong>Créer une aide à la saisie</strong></a>
    <table class="form">
      <tr>
        <th class="category" colspan="10">Filtrer les aides</th>
      </tr>

      <tr>
        <th><label for="filter_user_id" title="Filtrer les aides pour cet utilisateur">Utilisateur</label></th>
        <td>
          <select name="filter_user_id" onchange="this.form.submit()">
            <option value="">&mdash; Choisir un utilisateur</option>
            {{foreach from=$listPrat item=curr_user}}
            <option class="mediuser" style="border-color: #{{$curr_user->_ref_function->color}};" value="{{$curr_user->user_id}}" {{if $curr_user->user_id == $filter_user_id}} selected="selected" {{/if}}>
              {{$curr_user->_view}}
            </option>
            {{/foreach}}
          </select>
        </td>
        <th><label for="filter_class" title="Filtrer les aides pour ce type d'objet">Type d'objet</label></th>
        <td>
          <select name="filter_class" onchange="this.form.submit()">
            <option value="0">&mdash; Tous les types d'objets</option>
            {{foreach from=$classes|smarty:nodefaults key=class_name item=fields}}
            <option value="{{$class_name}}" {{if $class_name == $filter_class}} selected="selected" {{/if}}>
              {{tr}}{{$class_name}}{{/tr}}
            </option>
            {{/foreach}}
          </select>
        </td>
      </tr>
    </table>

    </form>
    
    <table class="tbl">
    
    <tr>
      <th colspan="6" class="title"><strong>Liste des aides à la saisie</strong></th>
    </tr>
    
    <tr>
      <th>Utilisateur</th>
      <th>Type d'objet</th>
      <th>Champ de l'objet</th>
      <th>Nom de l'aide</th>
      <th>Texte de remplacement</th>
    </tr>

    <tr>
      <th colspan="6"><strong>Aides du praticien</strong></th>
    </tr>
    {{foreach from=$aidesPrat item=curr_aide}}
    <tr>
      {{assign var="aide_id" value=$curr_aide->aide_id}}
      {{assign var="href" value="?m=$m&tab=$tab&aide_id=$aide_id"}}
      <td><a href="{{$href}}">{{$curr_aide->_ref_user->_view}}</a></td>
      <td><a href="{{$href}}">{{tr}}{{$curr_aide->class}}{{/tr}}</a></td>
      <td><a href="{{$href}}">{{$curr_aide->field}}</a></td>
      <td><a href="{{$href}}">{{$curr_aide->name}}</a></td>
      <td class="text">{{$curr_aide->text|nl2br}}</td>
    </tr>
    {{/foreach}}
    
    <tr>
      <th colspan="6"><strong>Aides du cabinet</strong></th>
    </tr>
    {{foreach from=$aidesFunc item=curr_aide}}
    <tr>
      {{assign var="aide_id" value=$curr_aide->aide_id}}
      {{assign var="href" value="?m=$m&tab=$tab&aide_id=$aide_id"}}
      <td><a href="{{$href}}">{{$curr_aide->_ref_function->text}}</a></td>
      <td><a href="{{$href}}">{{tr}}{{$curr_aide->class}}{{/tr}}</a></td>
      <td><a href="{{$href}}">{{$curr_aide->field}}</a></td>
      <td><a href="{{$href}}">{{$curr_aide->name}}</a></td>
      <td class="text">{{$curr_aide->text|nl2br}}</td>
    </tr>
    {{/foreach}}
      
    </table>

  </td>
  
  <td class="pane">

    <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_aide_aed" />
    <input type="hidden" name="aide_id" value="{{$aide->aide_id}}" />
    <input type="hidden" name="del" value="0" />

    <table class="form">

    <tr>
      <th class="category" colspan="2">
      {{if $aide->aide_id}}
        Modification d'une aide
      {{else}}
        Création d'une aide
      {{/if}}
      </th>
    </tr>

    <tr>
      <th><label for="user_id" title="Utilisateur concerné">Utilisateur</label></th>
      <td>
        <select name="user_id" title="{{$aide->_props.user_id}}">
          <option value="">&mdash; Associer à un praticien &mdash;</option>
          {{foreach from=$listPrat item=curr_prat}}
            <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $aide->user_id}} selected="selected" {{/if}}>
              {{$curr_prat->_view}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
  
    <tr>
      <th><label for="function_id" title="Fonction concerné">Fonction</label></th>
      <td>
        <select name="function_id" title="{{$aide->_props.function_id}}">
          <option value="">&mdash; Associer à une fonction &mdash;</option>
          {{foreach from=$listFunc item=curr_func}}
            <option class="mediuser" style="border-color: #{{$curr_func->color}};" value="{{$curr_func->function_id}}" {{if $curr_func->function_id == $aide->function_id}} selected="selected" {{/if}}>
              {{$curr_func->_view}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <th><label for="class" title="Type d'objet concerné, obligatoire.">Objet</label></th>
      <td>
        <select name="class" title="{{$aide->_props.class}}" onchange="loadFields()">
          <option value="">&mdash; Choisir un objet</option>
        </select>
      </td>
    </tr>

    <tr>
      <th><label for="field" title="Champ de l'objet concerné, obligatoire.">Champ</label></th>
      <td>
        <select name="field" title="{{$aide->_props.field}}">
          <option value="">&mdash; Choisir un champ</option>
        </select>
      </td>
    </tr>

    <tr>
      <th><label for="name" title="intitulé de l'aide, obligatoire.">Intitulé</label></th>
      <td><input type="text" name="name" title="{{$aide->_props.name}}" value="{{$aide->name}}" /></td>
    </tr>
    
    <tr>
      <th><label for="text" title="Texte de remplacement.">Texte</label></th>
      <td>
        <textarea style="width: 200px" rows="4" name="text" title="{{$aide->_props.text}}">{{$aide->text}}</textarea>
      </td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {{if $aide->aide_id}}
        <button class="submit" type="submit">
          Valider
        </button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'aide',objName:'{{$aide->name|smarty:nodefaults|JSAttribute}}'})">
          Supprimer
        </button>
        {{else}}
        <button class="submit" type="submit">
          Créer
        </button>
        {{/if}}
      </td>
    </tr>

    </table>
    
    </form>

  </td>
</tr>

</table>
