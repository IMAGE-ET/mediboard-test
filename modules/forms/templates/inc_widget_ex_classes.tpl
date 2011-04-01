{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $ex_classes|@count}}

<script type="text/javascript">
selectExClass = function(element, object_guid, event, _element_id) {
  var view = element.options ? element.options[element.options.selectedIndex].innerHTML : element.innerHTML;
  showExClassForm($V(element) || element.value, object_guid, view, null, event, _element_id);
  element.selectedIndex = 0;
}

var _popup = Control.Overlay.container && Control.Overlay.container.visible();

showExClassForm = function(ex_class_id, object_guid, title, ex_object_id, event, _element_id) {
  var url = new Url("forms", "view_ex_object_form");
  url.addParam("ex_class_id", ex_class_id);
  url.addParam("object_guid", object_guid);
  url.addParam("ex_object_id", ex_object_id);
  url.addParam("event", event);
  url.addParam("_element_id", _element_id);

  if (_popup) {
    url.popup(800, 600, title);
  }
  else {
    url.modale({title: title});
    url.modaleObject.observe("afterClose", function(){
      ExObject.register(_element_id, {
        ex_class_id: ex_class_id, 
        object_guid: object_guid, 
        event: event, 
        _element_id: _element_id
      });
    });
  }
}
</script>

  <div style="display: inline-block;">
		{{if $count_available == 1}}
      <!-- Un seul formulaire : bouton -->
		  {{assign var=_ex_class value=$ex_classes|@reset}}
		  <button class="new" value="{{$_ex_class->_id}}" onclick="selectExClass(this, '{{$object->_guid}}', '{{$event}}', '{{$_element_id}}')">
		  	{{$_ex_class->name}}
		  </button>
		{{elseif $count_available > 1}}
		  <!-- plusieurs formulaire : select -->
		  <select onchange="selectExClass(this, '{{$object->_guid}}', '{{$event}}', '{{$_element_id}}')">
		    <option selected="selected" disabled="disabled">
		      {{$count_available}} formulaire(s) disponible(s)
		    </option>
		    
		    {{foreach from=$ex_classes item=_ex_class}}
		      <option value="{{$_ex_class->_id}}" {{if $_ex_class->_disabled}}disabled="disabled"{{/if}}>{{$_ex_class->name}}</option>
		    {{/foreach}}
		  </select>
		{{/if}}
  </div>
	
{{else}}
  <em style="color: #999;">Aucun formulaire disponible</em>
{{/if}}

{{if $ex_objects|@count > 1}}
<button class="down notext" onclick="ObjectTooltip.createDOM(this, $(this).next(), {duration: 0});">Fiches enregistrées</button>

<ul id="CExObject.{{$object->_guid}}.{{$event}}" style="display: none;">
{{assign var=_events value=$object->_spec->events}}

{{foreach from=$ex_objects item=_ex_objects key=_ex_class_id}}
  {{assign var=_ex_class value=$ex_classes.$_ex_class_id}}
	
	{{if $_events.$event.multiple}}
	  <li>
	    <strong>{{$_ex_class->name}}</strong>
	    <ul>
	    {{foreach from=$_ex_objects item=_ex_object}}
	      <li>
	        <a href="#{{$_ex_object->_guid}}" 
	           onclick="showExClassForm({{$_ex_class_id}}, '{{$object->_guid}}', '{{$_ex_object}}', '{{$_ex_object->_id}}', '{{$event}}', '{{$_element_id}}')">
	          {{mb_value object=$_ex_object->_ref_last_log field=date}} &ndash; 
	          {{mb_value object=$_ex_object->_ref_last_log field=user_id}}
	        </a>
	      </li>
	    {{foreachelse}}
	      <li class="empty">{{tr}}CExObject.none{{/tr}}</li>
	    {{/foreach}}
	    </ul>
	  </li>
	{{else}}
	  {{foreach from=$_ex_objects item=_ex_object}}
    <li>
    <a href="#{{$_ex_object->_guid}}" title="{{mb_value object=$_ex_object->_ref_last_log field=date}} &ndash; {{mb_value object=$_ex_object->_ref_last_log field=user_id}}"
       onclick="showExClassForm({{$_ex_class_id}}, '{{$object->_guid}}', '{{$_ex_object}}', '{{$_ex_object->_id}}', '{{$event}}', '{{$_element_id}}')">
      {{$_ex_class->name}}
    </a>
    </li>
		{{/foreach}}
	{{/if}}
{{/foreach}}
</ul>
{{else if $ex_objects|@count == 1}}
	{{foreach from=$ex_objects item=_ex_objects key=_ex_class_id}}
    {{assign var=_ex_class value=$ex_classes.$_ex_class_id}}
	  {{foreach from=$_ex_objects item=_ex_object}}
		  <button class="edit" onclick="showExClassForm({{$_ex_class_id}}, '{{$object->_guid}}', '{{$_ex_object}}', '{{$_ex_object->_id}}', '{{$event}}', '{{$_element_id}}')">
		    {{$_ex_class->name}}
		  </button>
	  {{/foreach}}
	{{/foreach}}
{{/if}}