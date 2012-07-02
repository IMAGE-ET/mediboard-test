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
    {{mb_include module=system template=inc_config_bool var=check_cotation}}
    {{mb_include module=system template=inc_config_bool var=check_ccmu}}
    {{mb_include module=system template=inc_config_bool var=check_dp}}
    {{mb_include module=system template=inc_config_bool var=check_gemsa}}
    {{mb_include module=system template=inc_config_bool var=check_can_leave}}
	  {{mb_include module=system template=inc_config_bool var=hide_reconvoc_sans_sortie}}
	  {{mb_include module=system template=inc_config_str var=attente_first_part}}
	  {{mb_include module=system template=inc_config_str var=attente_second_part}}
	  {{mb_include module=system template=inc_config_str var=attente_third_part}}
    {{mb_include module=system template=inc_config_bool var=show_statut}}
    {{mb_include module=system template=inc_config_bool var=display_regule_par}}
    
    
	  <tr>
	    <td class="button" colspan="2">
	      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
	    </td>
	  </tr>
	</table>
</form>
