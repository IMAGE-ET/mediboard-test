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

{{assign var=exchange_source value=$actor->_ref_exchanges_sources.0}}
{{assign var=mod_name value=$actor->_ref_module->mod_name}}

<script type="text/javascript">
  Main.add(function () {
    Control.Tabs.create('tabs-{{$actor->_guid}}', true);
  });
</script>
  
<table class="form">
  <tr>
    <td colspan="2"> 
      <table class="form">  
        <tr>
          <td>
            <ul id="tabs-{{$actor->_guid}}" class="control_tabs">
              <li><a href="#{{$actor->_guid}}">{{tr}}{{$exchange_source->_class_name}}{{/tr}}</a></li>
              <li onmousedown="InteropActor.refreshFormatsAvailable('{{$actor->_guid}}')">
                <a href="#formats_available_{{$actor->_guid}}">{{tr}}{{$actor->_class_name}}_formats-available{{/tr}}</a></li>
              <li onmousedown="InteropActor.refreshConfigsFormats('{{$actor->_guid}}')">
                <a href="#configs_formats_{{$actor->_guid}}">{{tr}}{{$actor->_class_name}}_configs-formats{{/tr}}</a></li>   
              <li><a href="#actions_{{$actor->_guid}}">{{tr}}{{$actor->_class_name}}_actions{{/tr}}</a></li>
            </ul>
            
            <hr class="control_tabs" />
            
            <div id="{{$actor->_guid}}" style="display:none;">
              {{mb_include module=system template=inc_config_exchange_source source=$exchange_source}}
            </div>
            
            <div id="formats_available_{{$actor->_guid}}" style="display:none"></div>
            
            <div id="configs_formats_{{$actor->_guid}}" style="display:none"></div>
            
            <div id="actions_{{$actor->_guid}}" style="display:none">
              {{mb_include module=$mod_name template="`$actor->_class_name`_actions_inc"}}
            </div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>