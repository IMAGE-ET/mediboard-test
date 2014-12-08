{{*
 * $Id$
 *  
 * @category Stats
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th>{{tr}}CService{{/tr}}</th>
    <th>Nombre de patients</th>
    <th>Nombre d'AMBU</th>
    <th>Nombre d'Hospi. Comp.</th>
  </tr>
  {{foreach from=$results item=_result key=service}}
    <tr>
      <td>
        {{$service}}
      </td>
      <td>
        {{if isset($_result.patients|smarty:nodefaults)}}
          {{$_result.patients}}
        {{else}}
          0
        {{/if}}
      </td>
      <td>
        {{if isset($_result.ambu|smarty:nodefaults)}}
          {{$_result.ambu}}
        {{else}}
          0
        {{/if}}
      </td>
      <td>
        {{if isset($_result.hospi|smarty:nodefaults)}}
          {{$_result.hospi}}
        {{else}}
          0
        {{/if}}
      </td>
    </tr>
  {{/foreach}}
</table>