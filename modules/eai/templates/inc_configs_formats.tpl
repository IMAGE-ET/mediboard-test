{{*
 * View Configs Formats EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<script type="text/javascript">
  Main.add(function(){
    Control.Tabs.create('tabs-configs-formats', true).activeLink.up().onmousedown();
  });
</script>

<table class="main">
  <tr>
    <td style="vertical-align: top;" class="narrow">
      <ul id="tabs-configs-formats" class="control_tabs_vertical">
        {{foreach from=$formats_tabular item=_format_tabular}}
          {{assign var=configs value=$_format_tabular->_configs_format}}
          
          {{if $configs}}
            <li onmousedown="InteropActor.viewConfigsFormat('{{$actor_guid}}', '{{$configs->_guid}}');">
              <a {{if !$configs->_id}}class="wrong"{{/if}} href="#format_{{$configs->_guid}}">
                {{tr}}{{$configs->_class}}{{/tr}}
              </a>
            </li>
          {{/if}}
        {{/foreach}}
        
        {{foreach from=$formats_xml item=_format_xml}}
          {{assign var=configs value=$_format_xml->_configs_format}}
          
          {{if $configs}}
            <li onmousedown="InteropActor.viewConfigsFormat('{{$actor_guid}}', '{{$configs->_guid}}');">
              <a {{if !$configs->_id}}class="wrong"{{/if}} href="#format_{{$configs->_guid}}">
                {{tr}}{{$configs->_class}}{{/tr}}
              </a>
            </li>
          {{/if}}
        {{/foreach}}
      </ul>
    </td>
    <td style="vertical-align: top;">
      {{foreach from=$formats_xml item=_format_xml}}
        {{assign var=configs value=$_format_xml->_configs_format}}
        
        {{if $configs}}
          <div id="format_{{$configs->_guid}}" style="display: none;"></div>
        {{/if}}
      {{/foreach}}
      
      {{foreach from=$formats_tabular item=_format_tabular}}
        {{assign var=configs value=$_format_tabular->_configs_format}}
        
        {{if $configs}}
          <div id="format_{{$configs->_guid}}" style="display: none;"></div>
        {{/if}}
      {{/foreach}}
    </td>
  </tr>
</table>