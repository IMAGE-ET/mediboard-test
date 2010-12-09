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
  showExClassForm = function(ex_class_id, object_guid, title) {
    var url = new Url("system", "view_ex_object_form");
    url.addParam("object_guid", object_guid);
    url.addParam("ex_class_id", ex_class_id);
    url.modale({title: title});
  }
</script>

<select onchange="showExClassForm($V(this), '{{$object->_guid}}', this.options[this.options.selectedIndex].innerHTML)">
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