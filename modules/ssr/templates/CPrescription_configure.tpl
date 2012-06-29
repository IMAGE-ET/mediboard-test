{{* $Id: configure.tpl 7993 2010-02-03 16:55:27Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7993 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=class value=CPrescription}}
<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  <table class="form">
    {{mb_include module=system template=inc_config_bool var=show_dossier_soins}}
    <tr>
      <td class="button" colspan="100">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
