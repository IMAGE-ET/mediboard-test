{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

<script type="text/javascript">
  Main.add(function () {
    var tabs = Control.Tabs.create('tabs-actions', true);
  });
</script>


<table>
  <tr>
    <td style="vertical-align: top;">
      <ul id="tabs-actions" class="control_tabs_vertical small">
        <li><a href="#CActeCCAM-maintenance">{{tr}}CActeCCAM{{/tr}}</a></li>
      </ul>
    </td>
    <td style="vertical-align: top; width: 100%">
      <div id="CActeCCAM-maintenance" style="display: none;">
        {{mb_include template=CActeCCAM_maintenance}}
      </div>
    </td>
  </tr>
</table>