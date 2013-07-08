{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage xds
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<form name="editConfig" action="?m=xds&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />

  <table class="form">
    <tr>
      <th class="title" colspan="2">Configuration</th>
    </tr>

    <tr>
      <td class="button" colspan="2">
        <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>