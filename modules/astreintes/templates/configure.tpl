{{* $Id: configure.tpl 18134 2013-02-18 16:38:14Z charlyecho $ *}}

{{*
 * @package Mediboard
 * @subpackage Astreintes
 * @version $Revision: 18134 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}



<form name="editConfigAstreintes" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    <tr>
      <th colspan="2" class="title">Couleurs</th>
    </tr>
    {{mb_include module=system template=inc_config_color var=astreinte_medical_color}}
    {{mb_include module=system template=inc_config_color var=astreinte_admin_color}}
    {{mb_include module=system template=inc_config_color var=astreinte_personnelsoignant_color}}

    {{mb_include module=system template=configure_placeholder placeholder=CAstreintesTemplatePlaceholder}}
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

