{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  Main.add(function() {
    var al_url = new Url("system", "crazy_access_logs");
    al_url.addParam("mode", "find");
    al_url.requestUpdate("crazy_al");

    var dsl_url = new Url("system", "crazy_datasource_logs");
    dsl_url.addParam("mode", "find");
    dsl_url.requestUpdate("crazy_dsl");
  });
</script>

<table class="main">
  <tr>
    <th style="width: 50%;">{{tr}}CAccessLog{{/tr}}</th>
    <th style="width: 50%;">{{tr}}CDataSourceLog{{/tr}}</th>
  </tr>

  <tr>
    <td>
      <div id="crazy_al"></div>
    </td>

    <td>
      <div id="crazy_dsl"></div>
    </td>
  </tr>
</table>