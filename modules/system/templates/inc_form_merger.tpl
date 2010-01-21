{{* $Id: object_merger.tpl 7740 2010-01-04 17:33:14Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7740 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=system script=mb_object}}
{{mb_include_script module=system script=object_selector}}

<script>

function updateOptions(field) {
  var form = field.form;
	$A(form.elements["_choix_"+field.name]).each(function (element) {
	  element.checked = element.value.stripAll() == field.value.stripAll();
	} );
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
emptyObjectField += '<input type="text" size="40" name="objects_view[%KEY]" readonly="readonly" value="%VIEW" ondblclick="ObjectSelector.init(%KEY)" />';
emptyObjectField += '<button type="button" onclick="ObjectSelector.init(%KEY)" class="search notext">{{tr}}Search{{/tr}}</button>';
emptyObjectField += '<button type="button" onclick="this.up().remove();" class="remove notext">{{tr}}Remove{{/tr}}</button>';
emptyObjectField += '</div>';
var key = 0;

function addObjectField(id, view) {
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
          <strong>{{tr}}{{$objects_class}}{{/tr}}</strong>
        {{else}}
          <select name="objects_class">
            {{foreach from=$list_classes item=class}}
              <option value="{{$class}}" {{if $objects_class == $class}}selected="selected"{{/if}}>{{tr}}{{$class}}{{/tr}}</option>
            {{/foreach}}
          </select>
        {{/if}}
      </td>
      <th>Objets</th>
      <td id="objects-list"></td>
    </tr>

    <tr>
      <td />
      <td>
        <button type="submit" class="submit">{{tr}}Submit{{/tr}}</button>
      </td>
      <th />
      <td><button type="button" class="add notext" onclick="addObjectField()">{{tr}}Add{{/tr}}</button></td>
    </tr>

    {{if $result}}
    <tr>
      <th>Afficher les champs</th>
      <td>
        <label>
          <input type="checkbox" onclick="$$('tr.duplicate').invoke('setVisible', $V(this));" />
          avec une valeur multiple identiques
          <strong>({{$counts.duplicate}} valeurs)</strong>
        </label>
        <br />
        <label>
          <input type="checkbox" onclick="$$('tr.unique').invoke('setVisible', $V(this));" />
          avec une valeur unique
          <em>({{$counts.unique}} valeurs)</em>
        </label>
        <br />
        <label>
          <input type="checkbox" onclick="$$('tr.none'  ).invoke('setVisible', $V(this));" />
          sans valeurs
          <em>({{$counts.none}} valeurs)</em>
        </label>
      </td>
      <th />
      <td />
    </tr>
    {{/if}}

 </table>
</form>
