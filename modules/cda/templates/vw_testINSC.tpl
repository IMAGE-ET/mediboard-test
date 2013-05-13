{{*
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}
{{mb_script module=cda script=ccda}}

<table class="tbl">
  <tr>
    <th colspan="3" class="title">
      {{tr}}Choose the test{{/tr}}
    </th>
  </tr>
  <tr>
    <td>
      <button type="button" onclick="Ccda.testInsc('auto')">{{tr}}test automatic{{/tr}}</button>
    </td>
    <td>
      <button type="button" onclick="Ccda.testInsc('manuel')">{{tr}}test manuel{{/tr}}</button>
    </td>
    <td>
      <button type="button" onclick="Ccda.testInsc('saisi')">{{tr}}test saisi{{/tr}}</button>
    </td>
  </tr>
</table>
<br/>
{{if $app->user_prefs.VitaleVision}}
  {{include file="../../dPpatients/templates/inc_vitalevision.tpl" debug=false keepFiles=true}}
{{/if}}
<div id ="test_insc"></div>
