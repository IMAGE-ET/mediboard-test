<!--  $Id$ -->

<script type="text/javascript">

var aTraduction = {};
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

function loadTabsAides(form){
  var url = new Url("dPcompteRendu", "httpreq_vw_list_aides");
  url.addFormData(form);
  url.requestUpdate("tabs_aides");
  return false;
}

function resetStart(form){
  ["user", "func", "etab"].each(function(type){
    form["start["+type+"]"].value = 0;
  });
  form.onsubmit();
}

function getListDependValues(select, object_class, field) {
  if (select.hasClassName("loaded")) return;
  
  var oldValue = $V(select);
  var url = new Url("dPcompteRendu", "httpreq_select_enum_values");
  url.addParam("object_class", object_class);
  url.addParam("field", field);
  url.requestUpdate(select, {onComplete: function(){
    select.addClassName("loaded");
    $V(select, oldValue);
  }});
}

var changePage = {};

Main.add(function () {
  var form = getForm("filterFrm");
  
  loadClasses('{{$aide->class}}');
  loadFields('{{$aide->field}}');
  loadDependances('{{$aide->depend_value_1}}', '{{$aide->depend_value_2}}');
  
  loadTabsAides(form);
  
  ["user", "func", "etab"].each(function(type){
    changePage[type] = function(page) {
      $V(form["start["+type+"]"], page);
    }
  });
});

</script>

<table class="main">

<tr>
  <td class="greedyPane">
    
    <a href="?m={{$m}}&amp;tab={{$tab}}&amp;aide_id=0" class="button new">{{tr}}CAideSaisie-title-create{{/tr}}</a>
    
    <form name="filterFrm" action="?" method="get" onsubmit="return loadTabsAides(this)">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="start[user]" value="{{$start.user}}" onchange="this.form.onsubmit()" />
      <input type="hidden" name="start[func]" value="{{$start.func}}" onchange="this.form.onsubmit()" />
      <input type="hidden" name="start[etab]" value="{{$start.etab}}" onchange="this.form.onsubmit()" />
      
      <table class="form">
        <tr>
          <th class="category" colspan="10">Filtrer les aides</th>
        </tr>
  
        <tr>
          <th><label for="filter_user_id" title="Filtrer les aides pour cet utilisateur">Utilisateur</label></th>
          <td>
            <select name="filter_user_id" onchange="this.form.onsubmit()" style="width: 12em;">
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
            <select name="filter_class" onchange="this.form.onsubmit()" style="width: 12em;">
              <option value="">&mdash; Tous les types d'objets</option>
              {{foreach from=$classes|smarty:nodefaults key=class_name item=fields}}
              <option value="{{$class_name}}" {{if $class_name == $filter_class}} selected="selected" {{/if}}>
                {{tr}}{{$class_name}}{{/tr}}
              </option>
              {{/foreach}}
            </select>
          </td>
          <th><label for="keywords">Mots clés</label></th>
          <td>
            <input type="text" name="keywords" value="{{$keywords}}" />
          </td>
          <td>
            <button type="submit" class="search notext">{{tr}}Filter{{/tr}}</button>
          </td>
        </tr>
      </table>
    </form>

    <div id="tabs_aides"></div>
  </td>
  <td>

    <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)" class="{{$aide->_spec}}">

    <input type="hidden" name="dosql" value="do_aide_aed" />
    <input type="hidden" name="del" value="0" />
    {{mb_key object=$aide}}

    <table class="form">

    <tr>
      <th class="title {{if $aide->aide_id}}modify{{/if}}" colspan="2">
      {{if $aide->aide_id}}
      {{mb_include module=system template=inc_object_history object=$aide}}
        Modification d'une aide
      {{else}}
        Création d'une aide
      {{/if}}
      </th>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="user_id"}}</th>
      <td>
        <select name="user_id" class="{{$aide->_props.user_id}}" style="width: 12em;">
          <option value="">&mdash; Associer &mdash;</option>
          {{foreach from=$listPrat item=curr_prat}}
            <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $aide->user_id}} selected="selected" {{/if}}>
              {{$curr_prat->_view}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$aide field="function_id"}}</th>
      <td>
        <select name="function_id" class="{{$aide->_props.function_id}}" style="width: 12em;">
          <option value="">&mdash; Associer &mdash;</option>
          {{foreach from=$listFunc item=curr_func}}
            <option class="mediuser" style="border-color: #{{$curr_func->color}};" value="{{$curr_func->function_id}}" {{if $curr_func->function_id == $aide->function_id}} selected="selected" {{/if}}>
              {{$curr_func->_view}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="group_id"}}</th>
      <td>
        <select name="group_id" class="{{$aide->_props.group_id}}" style="width: 12em;">
          <option value="">&mdash; Associer &mdash;</option>
          {{foreach from=$listEtab item=curr_etab}}
            <option value="{{$curr_etab->_id}}" {{if $curr_etab->_id == $aide->group_id}} selected="selected" {{/if}}>
              {{$curr_etab}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="class"}}</th>
      <td>
        <select name="class" class="{{$aide->_props.class}}" onchange="loadFields()" style="width: 12em;">
          <option value="">&mdash; Choisir un type objet</option>
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="field"}}</th>
      <td>
        <select name="field" class="{{$aide->_props.field}}" onchange="loadDependances()" style="width: 12em;">
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
      <th>{{mb_label object=$aide field="text"}}</th>
      <td>{{mb_field object=$aide field="text"}}</td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {{if $aide->aide_id}}
        <button class="modify" type="submit">
          {{tr}}Save{{/tr}}
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
