{{*
 * $Id$
 *
 * @category hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<form name="editConfig-treatment" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">

    {{mb_include module=system template=inc_config_bool var=mandatory_num_dos_ipp_adm}}

    {{mb_include module=system template=inc_config_str var=tag}}
    {{mb_include module=system template=inc_config_str var=sending_application}}
    {{mb_include module=system template=inc_config_str var=importFunctionName}}
    {{mb_include module=system template=inc_config_bool var=doctorActif}}

    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>