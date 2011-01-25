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

{{if count($receiver->_ref_exchanges_sources) > 0}}
  <script type="text/javascript">
    Main.add(function () {
      Control.Tabs.create('tabs-evenements-{{$receiver->_guid}}', true);
    });
  </script>
  
  <tr>
    <th class="title" colspan="2">
      {{tr}}config-exchange-source{{/tr}} '{{mb_value object=$receiver field="message"}}'
    </th>
  </tr>
  <tr>
    <td colspan="2"> 
      <table class="form">  
        <tr>
          <td>
            {{foreach from=$receiver->_spec->messages key=_message item=_evenements}}
              {{if $_message == $receiver->message}}
                <ul id="tabs-evenements-{{$receiver->_guid}}" class="control_tabs">
                  {{foreach from=$_evenements item=_evenement}}
                    <li><a href="#{{$_evenement}}">{{tr}}{{$_evenement}}{{/tr}}</a></li>
                  {{/foreach}}
                </ul>
                
                <hr class="control_tabs" />
                
                {{foreach from=$_evenements item=_evenement}}
                  <div id="{{$_evenement}}" style="display:none;">
                   {{mb_include module=system template=inc_config_exchange_source source=$receiver->_ref_exchanges_sources.$_evenement}}
                  </div>
                {{/foreach}}
              {{/if}}
            {{/foreach}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
{{/if}}