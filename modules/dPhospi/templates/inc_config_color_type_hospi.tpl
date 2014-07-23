{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<form name="editConfig-dPhospiColor" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    <tr>
      <th colspan="2" class="title">
        {{tr}}config-color_type_hospi_by_type{{/tr}}
      </th>
    </tr>

    {{mb_include module=system template=inc_config_color class=colors var=ambu form="editConfig-dPhospiColor"}}
    {{mb_include module=system template=inc_config_color class=colors var=comp form="editConfig-dPhospiColor"}}
    {{mb_include module=system template=inc_config_color class=colors var=exte form="editConfig-dPhospiColor"}}
    {{mb_include module=system template=inc_config_color class=colors var=seances form="editConfig-dPhospiColor"}}
    {{mb_include module=system template=inc_config_color class=colors var=ssr form="editConfig-dPhospiColor"}}
    {{mb_include module=system template=inc_config_color class=colors var=psy form="editConfig-dPhospiColor"}}
    {{mb_include module=system template=inc_config_color class=colors var=urg form="editConfig-dPhospiColor"}}
    {{mb_include module=system template=inc_config_color class=colors var=consult form="editConfig-dPhospiColor"}}
    {{mb_include module=system template=inc_config_color class=colors var=recuse form="editConfig-dPhospiColor"}}
    {{mb_include module=system template=inc_config_color class=colors var=default form="editConfig-dPhospiColor"}}
    <tr>
      <td colspan="2" class="button">
        <button type="submit" class="save">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>


  </table>
</form>