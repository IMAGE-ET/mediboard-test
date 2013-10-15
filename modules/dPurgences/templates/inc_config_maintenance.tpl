{{*
 * $Id$
 *  
 * @category DPUrgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=dPadmissions script=identito_vigilance}}
{{mb_script module=dPurgences script=urgences_maintenance}}
<br/>
<table class="form">
  <tr>
    <th class="title">
      {{tr}}Action{{/tr}}
    </th>
  </tr>
  <tr>
    <td>
      <button type="button" class="search" onclick="urgencesMaintenance.checkRPU()">{{tr}}Check_rpu{{/tr}}</button>
    </td>
  </tr>
</table>