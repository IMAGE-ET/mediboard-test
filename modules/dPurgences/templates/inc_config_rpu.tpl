{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-RPU" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  <table class="form">    
	
    {{mb_include module=system template=inc_config_enum var=old_rpu values="0|1"}}
    {{mb_include module=system template=inc_config_bool var=allow_change_patient}}
    {{mb_include module=system template=inc_config_enum var=sortie_prevue values="sameday|h24"}}
    {{mb_include module=system template=inc_config_bool var=only_prat_responsable}}
    {{mb_include module=system template=inc_config_bool var=show_missing_rpu}}
    {{mb_include module=system template=inc_config_bool var=gerer_reconvoc}}
	  {{mb_include module=system template=inc_config_bool var=gerer_hospi}}

    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
