{{*
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}
{{mb_script module=cda script=ccda}}
<div id="resultAction">
  <table class="tbl">
    <tr>
      <th colspan="4">
        {{tr}}Action{{/tr}}
      </th>
    </tr>
    <tr>
      <td>
        <button type="button" class="button" onclick="Ccda.action('createClass')">
          {{tr}}createClass{{/tr}}
        </button>
      </td>
      <td>
        <button type="button" class="button" onclick="Ccda.action('createTest')">
          {{tr}}createTest{{/tr}}
        </button>
      </td>
      <td>
        <button type="button" class="button" onclick="Ccda.action('clearXSD')">
          {{tr}}clearXSD{{/tr}}
        </button>
      </td>
      <td>
      <button type="button" class="button" onclick="Ccda.action('missClass')">
        {{tr}}missClass{{/tr}}
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
  {{if $action != "missClass"}}
    <tr>
      <td>
        {{if $result == true}}
          {{tr}}Action done{{/tr}}
        {{else}}
          {{tr}}Action aborted{{/tr}}
        {{/if}}
      </td>
    </tr>
  {{else}}
    {{foreach from=$result item=_class}}
      <tr>
        <td>
          {{$_class}}
        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td>
          {{tr}}nothingClass{{/tr}}
        </td>
      </tr>
    {{/foreach}}
  {{/if}}
  </table>
</div>