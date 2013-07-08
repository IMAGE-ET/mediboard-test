{{*
 * $Id$
 *  
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=xds script=cxds}}
<div id="resultAction">
  <table class="tbl">
    <tr>
      <th>
        {{tr}}Action{{/tr}}
      </th>
    </tr>
    <tr>
      <td>
        <button type="button" class="button" onclick="Cxds.action('createXml')">
          {{tr}}Generate-xml-jv{{/tr}}
        </button>
      </td>
    </tr>
  </table>
  <br>
  {{if $action == "null"}}
    {{mb_return}}
  {{/if}}
  <table class="tbl">
    <tr>
      <th>
        {{tr}}Result{{/tr}}
      </th>
    </tr>
      <tr>
        <td>
          {{if $result == true}}
            {{tr}}Action-done{{/tr}}
          {{else}}
            {{tr}}Action-aborted{{/tr}}
          {{/if}}
        </td>
      </tr>
  </table>
</div>