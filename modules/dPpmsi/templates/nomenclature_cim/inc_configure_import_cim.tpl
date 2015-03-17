{{*
 * $Id$
 *  
 * @category pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_include module=system template=configure_dsn dsn=cim10}}
{{mb_script module=pmsi script=pmsi}}
<table class="form">
  <tr>
    <th class="title" colspan="2">Import</th>
  </tr>
  <tr>
    <td class="button" colspan="2">
      <button class="change" onclick="PMSI.importBaseCim()">{{tr}}Import{{/tr}} la base CIM10 à usage PMSI</button>
      <div id="result-import"></div>
    </td>
  </tr>
</table>