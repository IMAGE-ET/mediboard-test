{{*
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<h2>{{tr}}Import_asip{{/tr}}</h2>

{{mb_include module=system template=configure_dsn dsn=ASIP}}

<table class="main tbl">
  <tr>
    <th class="title" colspan="2">
      {{tr}}Import_tables{{/tr}}
    </th>
  <tr>
    <td class="narrow"><button onclick="importAsipTable()" class="change">{{tr}}Import{{/tr}}</button></td>
    <td id="import-log"></td>
  </tr>
</table>