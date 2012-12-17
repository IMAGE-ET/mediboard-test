{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-CActe" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  <table class="form">
    {{mb_include module=system template=inc_config_bool var=contraste}}
    {{mb_include module=system template=inc_config_bool var=alerte_asso}}
    {{mb_include module=system template=inc_config_bool var=tarif}}
    {{mb_include module=system template=inc_config_bool var=restrict_display_tarif}}  
    {{mb_include module=system template=inc_config_bool var=codage_strict}}  
    {{mb_include module=system template=inc_config_bool var=check_incompatibility}}
    {{mb_include module=system template=inc_config_bool var=openline}}
    {{mb_include module=system template=inc_config_bool var=modifs_compacts}}
    {{mb_include module=system template=inc_config_bool var=commentaire}}
    {{mb_include module=system template=inc_config_bool var=signature}}
    {{mb_include module=system template=inc_config_bool var=envoi_actes_salle}}
    {{mb_include module=system template=inc_config_bool var=envoi_motif_depassement}}
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>