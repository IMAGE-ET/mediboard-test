{{mb_include_script module=system script="mb_object"}}
{{mb_include_script module=system script=object_selector}}

<script type="text/javascript">
function setField (field, value, form) {
  field = $(document.forms[form].elements[field]);

  var dateView = $(field.id+'_da');
  if (dateView) {
    dateView.update(value);
    $V(field, (value ? Date.fromLocaleDate(value).toDATE() : ''));
    return;
  }
  
  $V(field, value); 
  if (field.fire) {
    field.fire('mask:check');
  }
}

var objectsList = [];

ObjectSelector.init = function(key) {
  this.sForm     = "objects-selector";
  this.sView     = "objects_view["+key+"]";
  this.sId       = "objects_id["+key+"]";
  this.sClass    = "objects_class";
  this.onlyclass = "true";
  this.pop();
}

var emptyObjectField = '<div class="object readonly">';
emptyObjectField += '<input type="hidden" name="objects_id[%KEY]" value="%ID" />';
emptyObjectField += '<input type="text" disabled="disabled" size="40" name="objects_view[%KEY]" readonly="readonly" value="%VIEW" ondblclick="ObjectSelector.init(%KEY)" />';
emptyObjectField += '<button type="button" onclick="ObjectSelector.init(%KEY)" class="search notext">{{tr}}Search{{/tr}}</button>';
emptyObjectField += '<button type="button" onclick="this.up().remove();" class="remove notext">{{tr}}Remove{{/tr}}</button>';
emptyObjectField += '</div>';
var key = 0;

addObjectField = function(id, view) {
  var field = emptyObjectField.gsub('%KEY', key++).gsub('%ID', id || '').gsub('%VIEW', view || '');
  $("objects-list").insert(field);
}

Main.add(function() {
  {{foreach from=$objects item=object}}
    addObjectField('{{$object->_id}}', '{{$object->_view}}');
    objectsList.push({{$object->_id}});
  {{foreachelse}}
    addObjectField();
    addObjectField();
  {{/foreach}}
}); 
</script>

<form name="objects-selector" method="get" action="">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
  <input type="hidden" name="readonly_class" value="{{$readonly_class}}" />
  <table class="main form">
    <tr>
      <th>Type d'objet</th>
      <td>
        {{if $readonly_class}}
          <input type="hidden" name="objects_class" value="{{$objects_class}}" />
          {{tr}}{{$objects_class}}{{/tr}}
        {{else}}
          <select name="objects_class">
            {{foreach from=$list_classes item=class}}
              <option value="{{$class}}" {{if $objects_class == $class}}selected="selected"{{/if}}">{{tr}}{{$class}}{{/tr}}</option>
            {{/foreach}}
          </select>
        {{/if}}
      </td>
      <th>Objets</th>
      <td id="objects-list" class="readonly"></td>
    </tr>
    <tr>
      <td />
      <td><button type="submit" class="submit">{{tr}}Submit{{/tr}}</button></td>
      <th />
      <td><button type="button" class="add notext" onclick="addObjectField()">{{tr}}Add{{/tr}}</button></td>
    </tr>
  </table>
</form>

{{if $result}}

<h2>Fusion de {{tr}}{{$result->_class_name}}{{/tr}}</h2>

{{if $checkMerge}}
<div class="big-warning">
  <strong>La fusion de ces deux objects n'est pas possible</strong> à cause des problèmes suivants :<br />
  - {{$checkMerge}}<br />
  Veuillez corriger ces problèmes avant toute fusion.
</div>
{{/if}}

<form name="form-merge" action="?m={{$m}}" method="post" onsubmit="{{if $testMerge}}false{{else}}checkForm(this){{/if}}">
  <input type="hidden" name="dosql" value="do_object_merge" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="_merging" value="1" />
  {{foreach from=$objects item=object name=object}}
  <input type="hidden" name="_objects_id[{{$smarty.foreach.object.index}}]" value="{{$object->_id}}" />
  {{/foreach}}
  <input type="hidden" name="_objects_class" value="{{$result->_class_name}}" />
  
  {{math equation="100/(count+1)" count=$objects|@count assign=width}}
  <table class="form">
    <tr>
      <th class="category" style="width: 1%;">Champ</th>
      {{foreach from=$objects item=object name=object}}
      <th class="category" style="width: {{$width}}%;">{{tr}}{{$object->_class_name}}{{/tr}} {{$smarty.foreach.object.iteration}}</th>
      {{/foreach}}
      <th class="category" style="width: {{$width}}%;">Résultat</th>
    </tr>
    {{foreach from=$result->_specs item=spec name=spec}}
      {{if $spec->fieldName != $result->_spec->key && $spec->fieldName|substr:0:1 != '_'}}
        {{if $spec instanceof CRefSpec}}
          {{include field=$spec->fieldName file="../../system/templates/inc_merge_field_ref.tpl"}}
        {{else}}
          {{include field=$spec->fieldName file="../../system/templates/inc_merge_field.tpl"}}
        {{/if}}
      {{/if}}
    {{/foreach}}
  </table>

  <div class="button">
    <button type="button" class="search" onclick="MbObject.viewBackRefs('{{$result->_class_name}}', objectsList);">
      {{tr}}CMbObject-merge-moreinfo{{/tr}}
    </button>
    <button type="submit" class="submit">
      {{tr}}Merge{{/tr}}
    </button>
  </div>
</form>

{{else}}
<div class="small-info">
  Veuillez choisir des objets existants à fusionner.
</div>
{{/if}}