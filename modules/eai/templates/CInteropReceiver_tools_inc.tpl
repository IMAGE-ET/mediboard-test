{{*
 * View Interop Receiver Exchange Sources EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{if $actor->_id}}
  {{assign var=mod_name value=$actor->_ref_module->mod_name}}
  
  <script type="text/javascript">  
    Main.add(function () {
      tabs = Control.Tabs.create('tabs-{{$actor->_guid}}', false,  {
          afterChange: function(newContainer){
            $$("a[href='#"+newContainer.id+"']")[0].up("li").onmousedown();
          }
      });
    });
  </script>
  
  <table class="form">  
    <tr>
      <td>
        <ul id="tabs-{{$actor->_guid}}" class="control_tabs">
          <li onmousedown="InteropActor.refreshFormatsAvailable('{{$actor->_guid}}')">
            <a href="#formats_available_{{$actor->_guid}}">{{tr}}{{$actor->_parent_class_name}}_formats-available{{/tr}}</a></li>
          <li onmousedown="InteropActor.refreshExchangesSources('{{$actor->_guid}}')">
            <a href="#exchanges_sources_{{$actor->_guid}}">{{tr}}{{$actor->_parent_class_name}}_exchanges-sources{{/tr}}</a></li>
          {{if $actor->_ref_object_configs}}
          <li onmousedown="InteropActor.refreshConfigObjectValues('{{$actor->_id}}', '{{$actor->_ref_object_configs->_guid}}');">
            <a href="#actor_config_{{$actor->_id}}">{{tr}}{{$actor->_parent_class_name}}_config{{/tr}}</a></li>
          {{/if}}
        </ul>
        
        <hr class="control_tabs" />
        
        <div id="formats_available_{{$actor->_guid}}" style="display:none"></div>
        
        <div id="exchanges_sources_{{$actor->_guid}}" style="display:none"></div>
        
        {{if $actor->_ref_object_configs}}
          <div id="actor_config_{{$actor->_id}}" style="display: none;"></div>
        {{/if}}
      </td>
    </tr>
  </table>
{{/if}}