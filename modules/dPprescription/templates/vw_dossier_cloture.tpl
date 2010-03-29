{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=patient value=$sejour->_ref_patient}}
    
<table class="tbl">
	<tr>
	  <th class="title" colspan="2">
	    <div style="float: right">{{$dateTime|date_format:"%d/%m/%Y %Hh%M"}}</div>
	    <a href="#" onclick="window.print();">
	      Dossier cloturé
	    </a>
	  </th>
	</tr>
	<tr>
	  <th colspan="2">Patient</th>
	</tr>
	<tr>
	  <td>
	    <strong>Patient</strong>
	  </td>
	  <td>
	    {{mb_value object=$patient field=_view}}
	  </td>
	</tr>
	<tr>
	  <td style="width: 20%">
	    <strong>{{mb_title object=$patient->_ref_constantes_medicales field=poids}}</strong>
	  </td>
	  <td colspan="2">
	   {{if $patient->_ref_constantes_medicales->poids}}
	     {{mb_value object=$patient->_ref_constantes_medicales field=poids}} kg
	   {{else}}??{{/if}}
	  </td>
	</tr>
	<tr>
	  <td>
	    <strong>{{mb_label object=$patient field=adresse}}</strong>
	  </td>
	  <td>
	    {{mb_value object=$patient field=adresse}}
	  </td>
	</tr>
	<tr>
	  <td>
	    <strong>{{mb_label object=$patient field=naissance}}</strong>
	  </td>
	  <td>
	    {{mb_value object=$patient field=naissance}} ({{$patient->_age}} ans)
	  </td>
	</tr>
	<tr>
	  <td>
	    <strong>{{mb_label object=$patient field=tel}}</strong>
	  </td>
	  <td>
	    {{mb_value object=$patient field=tel}}
	  </td>
	</tr>
	<tr>
	  <th colspan="2">Séjour</th>
	</tr>
	<tr>
	  <td>
	    <strong>{{mb_label object=$sejour field=_entree}}</strong>
	  </td>
	  <td>
	    {{mb_value object=$sejour field=_entree}}
	  </td>
	</tr>
	<tr>
	  <td>
	    <strong>{{mb_label object=$sejour field=_sortie}}</strong>
	  </td>
	  <td>
	    {{mb_value object=$sejour field=_sortie}}
	  </td>
	</tr>
	<tr>
	  <td>
	    <strong>{{mb_label object=$sejour field=praticien_id}}</strong>
	  </td>
	  <td>
	    {{mb_value object=$sejour field=praticien_id}}
	  </td>
	</tr>
	<tr>
	  <td>
	    <strong>{{mb_label object=$sejour field=type}}</strong>
	  </td>
	  <td>
	    {{mb_value object=$sejour field=type}}
	  </td>
	</tr>
</table>


{{if $dossier|@count}}
  {{mb_include module=dPprescription template=inc_vw_dossier_cloture}}
{{/if}}

<table class="tbl">
  <tr>
    <th colspan="6">Observations et Transmissions</th>
  </tr>
  <tr>
    <th>{{mb_label class=CTransmissionMedicale field="type"}}</th>
    <th>{{mb_label class=CTransmissionMedicale field="user_id"}}</th>
    <th>{{mb_label class=CTransmissionMedicale field="date"}}</th>
    <th>{{tr}}Hour{{/tr}}</th>
    <th>{{mb_label class=CTransmissionMedicale field="object_id"}}</th>
    <th>{{mb_label class=CTransmissionMedicale field="text"}}</th>
  </tr>
  {{foreach from=$sejour->_ref_suivi_medical item=_suivi}}
 	  {{mb_include module=dPhospi template=inc_line_suivi _suivi=$_suivi show_patient=false nodebug=true without_del_form=true}}
  {{foreachelse}}
	  <tr>
	  	<td colspan="6">Aucune transmission</td>
	  </tr>
	{{/foreach}}
</table>