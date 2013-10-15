{{*
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<br/>
<form method="POST" onsubmit="return urgencesMaintenance.displaySejour(this)">
  <table class="form">
    <tr>
      <th colspan="2" class="title">Récupération des séjours avec plusieurs RPU</th>
    </tr>
    <tr>
      <th>
        {{tr}}Nb-month{{/tr}}
      </th>
      <td>
        <input type="number" class="num" name="month_maintenance" value="6"/>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
<br/>
<div id="display_sejour"></div>