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

<script type="text/javascript">
  Main.add(function () {
    Control.Tabs.create('tabs-exchanges-sources-{{$actor->_guid}}');
  });
</script>

{{if !$actor->_ref_msg_supported_family}}
  <div class="small-warning">{{tr}}CMessageSupported.none{{/tr}}</div>
{{else}}
  <table class="main">
    <tr>
      <td style="vertical-align: top;" class="narrow">
        <ul id="tabs-exchanges-sources-{{$actor->_guid}}" class="control_tabs_vertical">
          {{foreach from=$actor->_ref_msg_supported_family item=_msg_supported}}
            <li>
              <a href="#exchanges_sources_{{$actor->_guid}}_{{$_msg_supported}}">
                 {{$_msg_supported}}
              </a>
            </li>
          {{/foreach}}
        </ul>
      </td>
      <td style="vertical-align: top;">
        {{foreach from=$actor->_ref_msg_supported_family item=_msg_supported}}
          <div id="exchanges_sources_{{$actor->_guid}}_{{$_msg_supported}}" style="display:none;">
             {{mb_include module=system template=inc_config_exchange_source source=$actor->_ref_exchanges_sources.$_msg_supported}}
          </div>
        {{/foreach}}
      </td>
    </tr>
  </table>
{{/if}}