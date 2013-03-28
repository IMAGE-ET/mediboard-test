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

    {{mb_include module=system template=inc_config_color class=colors var=ambu}}
    {{mb_include module=system template=inc_config_color class=colors var=comp}}
    {{mb_include module=system template=inc_config_color class=colors var=exte}}
    {{mb_include module=system template=inc_config_color class=colors var=seances}}
    {{mb_include module=system template=inc_config_color class=colors var=ssr}}
    {{mb_include module=system template=inc_config_color class=colors var=psy}}
    {{mb_include module=system template=inc_config_color class=colors var=urg}}
    {{mb_include module=system template=inc_config_color class=colors var=consult}}
    {{mb_include module=system template=inc_config_color class=colors var=recuse}}
    {{mb_include module=system template=inc_config_color class=colors var=default}}
    <tr>
      <td colspan="2" class="button">
        <button type="submit" class="save">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>


  </table>
</form>