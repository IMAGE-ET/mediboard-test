{{* $Id: configure.tpl 8207 2010-03-04 17:05:05Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 8207 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(function(){
    Control.Tabs.create('tabs-config-actions', true);
  });
</script>

<table class="main">
  <tr>
    <td style="vertical-align: top;" class="narrow">
      <ul id="tabs-config-actions" class="control_tabs_vertical">
        <li>
          <a href="#actions-export">
            {{tr}}sip_config-actions-export{{/tr}}
          </a>
        </li>
       </ul>
    </td>
    <td style="vertical-align: top;">
      <div id="actions-export" style="display: none;">
        {{mb_include template=inc_config_export}}
      </div>
    </td>
  </tr>
</table>