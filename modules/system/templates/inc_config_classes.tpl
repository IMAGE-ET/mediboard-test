{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 9174 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  refreshConfigObjects = function(classname) {
    var url = new Url("system", "ajax_config_objects");
    url.addParam("classname", classname);
    url.requestUpdate("classes-"+classname);
  };
  
  Main.add(Control.Tabs.create.curry('tabs-config-classes', true));
</script>

<ul id="tabs-config-classes" class="control_tabs">
  {{foreach from=$classes item=_class}}
    <li onmousedown="refreshConfigObjects('{{$_class->_class_name}}');">
      <a href="#classes-{{$_class->_class_name}}">{{tr}}{{$_class->_class_name}}{{/tr}}</a></li>
  {{/foreach}}
</ul>

<hr class="control_tabs" />

{{foreach from=$classes item=_class}}
<div id="classes-{{$_class->_class_name}}" style="display: none;">
  <div class="small-info">{{tr}}config-choose-classes{{/tr}}</div>
</div>
{{/foreach}}