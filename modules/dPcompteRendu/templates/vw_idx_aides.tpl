<!--  $Id$ -->

<script type="text/javascript">

var aTraduction = new Array();
{{foreach from=$listTraductions key=key item=currClass}}
aTraduction["{{$key}}"] = "{{$currClass}}";
{{/foreach}}

var classes = {{$classes|@json}};

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
      select.options[select.length] = new Option(aTraduction[option], option);
    }
  }
	
  $V(select, value);
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
    var option = elm;
    if (typeof(options[option]) != "function") { // to filter prototype functions
      select.options[select.length] = new Option(option, option);
    }
  }
	
  $V(select, value);
  loadDependances();
}

function loadDependances(depend_value_1, depend_value_2){

  var form = document.editFrm;
  var select_depend_1 = form.elements['depend_value_1'];
  var select_depend_2 = form.elements['depend_value_2'];
  var className  = form.elements['class'].value;
  var fieldName  = form.elements['field'].value;
  var options = classes[className];

  // delete all former options except first
  while (select_depend_1.length > 1) {
    select_depend_1.options[1] = null;
  }
  while (select_depend_2.length > 1) {
    select_depend_2.options[1] = null;
  }
  
  if(!options){
   return;
  }
  
  if(!classes[className][fieldName]){
  return;
  }
  
  options_depend_1 = classes[className][fieldName]['depend_value_1'];
  options_depend_2 = classes[className][fieldName]['depend_value_2'];
  
  // Depend value 1
  for (var elm in options_depend_1) {
    var option = options_depend_1[elm];
    if (typeof(option) != "function") { // to filter prototype functions
      select_depend_1.options[select_depend_1.length] = new Option(aTraduction[option], elm, elm == depend_value_1);
    }
  }
  
  // Depend value 2
  for (var elm in options_depend_2) {
    var option = options_depend_2[elm];
    if (typeof(option) != "function") { // to filter prototype functions
      select_depend_2.options[select_depend_2.length] = new Option(aTraduction[option], elm, elm == depend_value_2);
    }
  }
}

function popupImport(owner_guid){
  var url = new Url("dPcompteRendu", "aides_import_csv");
  url.addParam("owner_guid", owner_guid);
  url.pop(500, 400, "Import d'aides à la saisie");
  return false;
}

Main.add(function () {
  loadClasses('{{$aide->class}}');
  loadFields('{{$aide->field}}');
  loadDependances('{{$aide->depend_value_1}}', '{{$aide->depend_value_2}}');
});

</script>

<table class="main">

<tr>
  <td class="greedyPane">

    <form name="filterFrm" action="?" method="get">
    
    <input type="hidden" name="m" value="{{$m}}" />
    <a href="?m={{$m}}&amp;tab={{$tab}}&amp;aide_id=0" class="button new"><strong>Créer une aide à la saisie</strong></a>
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
    

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-owner', true);
});
</script>

<ul id="tabs-owner" class="control_tabs">
  <li><a href="#aides-user">Aides de '{{$userSel}}' <small>({{$aidesPrat|@count}})</small></a></li>
  <li><a href="#aides-func">Aides de '{{$userSel->_ref_function}}' <small>({{$aidesFunc|@count}})</small></a></li>
</ul>

<hr class="control_tabs" />

<div id="aides-user" style="display: none;">
  {{include file=inc_list_aides.tpl owner=$userSel aides=$aidesPrat}}
</div>

<div id="aides-func" style="display: none;">
  {{include file=inc_list_aides.tpl owner=$userSel->_ref_function aides=$aidesFunc}}
</div>

    
  </td>
  
  <td class="pane">

    <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)" class="{{$aide->_spec}}">

    <input type="hidden" name="dosql" value="do_aide_aed" />
    {{mb_field object=$aide field="aide_id" hidden=1 prop=""}}
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
      <th>{{mb_label object=$aide field="function_id"}}</th>
      <td>
        <select name="function_id" class="{{$aide->_props.function_id}}">
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
      <th>{{mb_label object=$aide field="user_id"}}</th>
      <td>
        <select name="user_id" class="{{$aide->_props.user_id}}">
          <option value="">&mdash; Associer à un utilisateur &mdash;</option>
          {{foreach from=$listPrat item=curr_prat}}
            <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $aide->user_id}} selected="selected" {{/if}}>
              {{$curr_prat->_view}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="class"}}</th>
      <td>
        <select name="class" class="{{$aide->_props.class}}" onchange="loadFields()">
          <option value="">&mdash; Choisir un objet</option>
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="field"}}</th>
      <td>
        <select name="field" class="{{$aide->_props.field}}" onchange="loadDependances()">
          <option value="">&mdash; Choisir un champ</option>
        </select>
      </td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$aide field="depend_value_1"}}</th>
      <td>
        <select name="depend_value_1" class="{{$aide->_props.depend_value_1}}">
          <option value="">&mdash; Tous</option>
        </select>
      </td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$aide field="depend_value_2"}}</th>
      <td>
        <select name="depend_value_2" class="{{$aide->_props.depend_value_2}}">
          <option value="">&mdash; Tous</option>
        </select>
      </td>
    </tr>
    

    <tr>
      <th>{{mb_label object=$aide field="name"}}</th>
      <td>{{mb_field object=$aide field="name"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$aide field=text"}}</th>
      <td>
        {{mb_field object=$aide field="text"}}
      </td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {{if $aide->aide_id}}
        <button class="modify" type="submit">
          {{tr}}Modify{{/tr}}
        </button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'aide',objName:'{{$aide->name|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">
          {{tr}}Create{{/tr}}
        </button>
        {{/if}}
      </td>
    </tr>
    </table>
    </form>
  </td>
</tr>

</table>
