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
            switch (newContainer.id) {
              case "exchanges_sources_{{$actor->_guid}}" :
                InteropActor.refreshExchangesSources('{{$actor->_guid}}');
                break;
              case "formats_available_{{$actor->_guid}}" :
                InteropActor.refreshFormatsAvailable('{{$actor->_guid}}');
                break;
              case "configs_formats_{{$actor->_guid}}" :      
                InteropActor.refreshConfigsFormats('{{$actor->_guid}}');
                break;
              case "tags_{{$actor->_guid}}" :
                InteropActor.refreshTags('{{$actor->_guid}}');
                break;
              case "linked_objects_{{$actor->_guid}}" :
                InteropActor.refreshLinkedObjects('{{$actor->_guid}}');
                break;
            }
          }
      });
    });
  </script>
    
  
  <table class="form">  
    <tr>
      <td>
        <ul id="tabs-{{$actor->_guid}}" class="control_tabs">
          {{if !$actor instanceof CSenderSOAP}} 
            <li>
              <a href="#exchanges_sources_{{$actor->_guid}}">{{tr}}{{$actor->_parent_class}}_exchanges-sources{{/tr}}</a></li>
          {{/if}}
          <li>
            <a href="#formats_available_{{$actor->_guid}}">{{tr}}{{$actor->_class}}_formats-available{{/tr}}</a></li>
          <li>
            <a href="#configs_formats_{{$actor->_guid}}">{{tr}}{{$actor->_class}}_configs-formats{{/tr}}</a></li>
          <li>
            <a href="#tags_{{$actor->_guid}}">{{tr}}{{$actor->_parent_class}}_tags{{/tr}}</a></li>  
          <li>
            <a href="#linked_objects_{{$actor->_guid}}">{{tr}}CObjectToInteropSender{{/tr}}</a>
          </li> 
          <li>
            <a href="#actions_{{$actor->_guid}}">{{tr}}{{$actor->_class}}_actions{{/tr}}</a></li>
        </ul>
        
        <hr class="control_tabs" />
        
        {{if !$actor instanceof CSenderSOAP}}
          <div id="exchanges_sources_{{$actor->_guid}}" style="display:none;"></div>
        {{/if}}
        
        <div id="formats_available_{{$actor->_guid}}" style="display:none"></div>
        
        <div id="configs_formats_{{$actor->_guid}}" style="display:none"></div>
        
        <div id="tags_{{$actor->_guid}}" style="display:none"></div>
        
        <div id="linked_objects_{{$actor->_guid}}" style="display: none"></div>
        
        <div id="actions_{{$actor->_guid}}" style="display:none">
          {{mb_include module=$mod_name template="`$actor->_class`_actions_inc"}}
        </div>
      </td>
    </tr>
  </table>
{{/if}}