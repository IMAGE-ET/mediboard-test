{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfigPermissions" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  <table class="form">
    {{assign var="class" value="CUser"}}
    <tr>
      <th class="category" colspan="2">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=strong_password}}
    {{mb_include module=system template=inc_config_str var=max_login_attempts}}
    {{mb_include module=system template=inc_config_bool var=allow_change_password}}
    {{mb_include module=system template=inc_config_bool var=force_changing_password}}
    {{mb_include module=system template=inc_config_enum var=password_life_duration values="15 day|1 month|2 month|3 month|6 month|1 year"}}
    <tr>
      <td class="button" colspan="100">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>