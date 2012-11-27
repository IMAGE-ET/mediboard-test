{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-dPhospi" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  <table class="form">
  
  {{mb_include module=system template=inc_config_str var=tag_service}}

  {{mb_include module=system template=inc_config_bool var=pathologies}}
  
  {{mb_include module=system template=inc_config_str var=nb_hours_trans}}
  
  {{mb_include module=system template=inc_config_str var=hour_limit}}
  
  {{mb_include module=system template=inc_config_bool var=show_age_patient}}
  
  {{mb_include module=system template=inc_config_str var=max_affectations_view}}

  {{mb_include module=system template=inc_config_enum var=systeme_prestations values=standard|expert}}

  {{mb_include module=system template=inc_config_bool var=use_vue_topologique}}
  
  {{mb_include module=system template=inc_config_str var=nb_colonnes_vue_topologique}}
  
  {{mb_include module=system template=inc_config_bool var=stats_for_all}}
  
  {{mb_include module=system template=inc_config_bool var=hide_alertes_temporel}}

  {{mb_include module=system template=inc_config_bool var=show_age_sexe_mvt}}

  {{mb_include module=system template=inc_config_bool var=show_hour_anesth_mvt}}

  {{mb_include module=system template=inc_config_bool var=show_retour_mvt}}

  {{mb_include module=system template=inc_config_bool var=show_collation_mvt}}

  {{mb_include module=system template=inc_config_bool var=show_sortie_mvt}}

  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>
</form>