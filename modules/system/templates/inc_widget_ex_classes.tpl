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
  selectExClass = function(element, object_guid) {
    showExClassForm($V(element), object_guid, element.options[element.options.selectedIndex].innerHTML);
    element.selectedIndex = 0;
  }
  
  showExClassForm = function(ex_class_id, object_guid, title, ex_object_id) {
    var url = new Url("system", "view_ex_object_form");
    url.addParam("object_guid", object_guid);
    url.addParam("ex_class_id", ex_class_id);
    url.addParam("ex_object_id", ex_object_id);
    url.modale({title: title});
  }
</script>

<select onchange="selectExClass(this, '{{$object->_guid}}')">
  <option selected="selected" disabled="disabled">
    {{$ex_classes|@count}} formulaire(s) disponible(s)
  </option>
  
  {{foreach from=$ex_classes item=_ex_class}}
    <option value="{{$_ex_class->_id}}">{{$_ex_class}}</option>
  {{/foreach}}
</select>

{{else}}
  Aucun formulaire disponible
{{/if}}

{{foreach from=$ex_objects item=_ex_objects key=_ex_class_id}}
  <h3>{{$ex_classes.$_ex_class_id}}</h3>
  
  <ul>
  {{foreach from=$_ex_objects item=_ex_object}}
    <li>
      <a href="#{{$_ex_object->_guid}}" onclick="showExClassForm({{$_ex_class_id}}, '{{$object->_guid}}', '{{$_ex_object}}', '{{$_ex_object->_id}}')">
        {{* {{mb_value object=$_ex_object->_ref_last_log field=date}} &ndash;  *}}{{$_ex_object}}
      </a>
    </li>
  {{/foreach}}
  </ul>
  
{{/foreach}}