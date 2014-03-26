{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-Display" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  <table class="form">
    {{mb_include module=system template=inc_config_str var=rpu_warning_time}}
    {{mb_include module=system template=inc_config_str var=rpu_alert_time}}
    {{mb_include module=system template=inc_config_str var=date_tolerance}}
    {{mb_include module=system template=inc_config_enum var=default_view values="tous|presents"}}
    {{mb_include module=system template=inc_config_bool var=age_patient_rpu_view}}
    {{mb_include module=system template=inc_config_bool var=responsable_rpu_view}}
    {{mb_include module=system template=inc_config_bool var=diag_prat_view}}
    {{mb_include module=system template=inc_config_bool var=hide_reconvoc_sans_sortie}}
    {{mb_include module=system template=inc_config_str var=attente_first_part}}
    {{mb_include module=system template=inc_config_str var=attente_second_part}}
    {{mb_include module=system template=inc_config_str var=attente_third_part}}
    {{mb_include module=system template=inc_config_bool var=show_statut}}
    {{mb_include module=system template=inc_config_bool var=display_regule_par}}
    {{mb_include module=system template=inc_config_bool var=view_rpu_uhcd}}
    {{mb_include module=system template=inc_config_enum var=main_courante_refresh_frequency      values="90|180|300|600"}}
    {{mb_include module=system template=inc_config_enum var=uhcd_refresh_frequency               values="90|180|300|600"}}
    {{mb_include module=system template=inc_config_enum var=imagerie_refresh_frequency           values="90|180|300|600"}}
    {{mb_include module=system template=inc_config_enum var=identito_vigilance_refresh_frequency values="90|180|300|600"}}
    {{mb_include module=system template=inc_config_bool var=use_vue_topologique}}
    {{mb_include module=system template=inc_config_enum var=vue_topo_refresh_frequency           values="90|180|300|600"}}

    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
