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
  showExClassForm($V(element), object_guid, element.options[element.options.selectedIndex].innerHTML, null, event, _element_id);
  element.selectedIndex = 0;
}

var _popup = !!Control.Overlay.container;

showExClassForm = function(ex_class_id, object_guid, title, ex_object_id, event, _element_id) {
  var url = new Url("system", "view_ex_object_form");
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

Main.add(function(){
  PairEffect.initGroup("CExObject-toggle", {
    bStoreInCookie: false
  }); 
});
</script>

<div style="display: inline-block;">
  <select onchange="selectExClass(this, '{{$object->_guid}}', '{{$event}}', '{{$_element_id}}')">
    <option selected="selected" disabled="disabled">
      {{$ex_classes|@count}} formulaire(s) disponible(s)
    </option>
    
    {{foreach from=$ex_classes item=_ex_class}}
      <option value="{{$_ex_class->_id}}">{{$_ex_class->name}}</option>
    {{/foreach}}
  </select>
  
  {{else}}
    <em style="color: #999;">Aucun formulaire disponible</em>
  {{/if}}
</div>

<span style="display: inline-block; white-space: nowrap;">
  <div id="CExObject.{{$object->_guid}}.{{$event}}-trigger" class="triggerShow" 
      style="padding-left: 2em; line-height: 1.4em; {{if $count == 0}}display: none;{{/if}}">
    {{$count}} formulaire(s) enregistré(s)
  </div>
</span>

<ul id="CExObject.{{$object->_guid}}.{{$event}}" class="CExObject-toggle" style="display: none">
{{foreach from=$ex_objects item=_ex_objects key=_ex_class_id}}
  <li>
    <strong>{{$ex_classes.$_ex_class_id}}</strong>
    <ul>
    {{foreach from=$_ex_objects item=_ex_object}}
      <li>
        <a href="#{{$_ex_object->_guid}}" 
           onclick="showExClassForm({{$_ex_class_id}}, '{{$object->_guid}}', '{{$_ex_object}}', '{{$_ex_object->_id}}', '{{$event}}', '{{$_element_id}}')">
          {{* {{mb_value object=$_ex_object->_ref_last_log field=date}} &ndash;  *}}{{$_ex_object}}
        </a>
      </li>
    {{foreachelse}}
      <li><em>{{tr}}CExObject.none{{/tr}}</em></li>
    {{/foreach}}
    </ul>
  </li>
{{foreachelse}}
  <li><em>{{tr}}CExClass.none{{/tr}}</em></li>
{{/foreach}}
</ul>
