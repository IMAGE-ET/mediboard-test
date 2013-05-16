{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage CDA
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />

  <table class="form">
    <tr>
      <th class="title" colspan="2">Configuration</th>
    </tr>
    <tr>
      {{mb_include module=system template=inc_config_str var=OID_root maxlength=64 cssClass="pattern|[0-2](\.([1-9][0-9]*\\x7C0))*"}}
    </tr>
    <tr>
      <td colspan="2">
        <button type="submit" class="save">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>