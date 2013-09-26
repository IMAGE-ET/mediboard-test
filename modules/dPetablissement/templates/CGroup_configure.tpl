{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfigGroup" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  <table class="form">
    <tr>
      <th class="category" colspan="10">{{tr}}config-{{$m}}{{/tr}}</th>
    </tr>

    {{mb_include module=system template=inc_config_str  var=tag_group}}
    {{mb_include module=system template=inc_config_bool var=dossiers_medicaux_shared}}
      
    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>