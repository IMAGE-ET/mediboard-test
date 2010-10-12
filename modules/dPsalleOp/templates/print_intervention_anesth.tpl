{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
	
Main.add(function(){
	var url = new Url("dPhospi", "httpreq_vw_constantes_medicales");
	url.addParam("patient_id", {{$operation->_ref_sejour->patient_id}});
	url.addParam("context_guid", "{{$operation->_ref_sejour->_guid}}");
	url.addParam("selection[]", ["pouls", "ta", "frequence_respiratoire", "score_sedation", "spo2", "diurese"]);
	url.addParam("date_min", "{{$operation->_datetime_reel}}");
	url.addParam("date_max", "{{$operation->_datetime_reel_fin}}");
	url.addParam("print", 1);
	url.requestUpdate("constantes");
});
	
</script>

{{assign var=sejour value=$operation->_ref_sejour}}
{{assign var=patient value=$sejour->_ref_patient}}
{{assign var=consult_anesth value=$operation->_ref_consult_anesth}}

<table class="tbl">
	<tr>
	  <th class="title" onclick="window.print();" colspan="2">Fiche d'intervention anesth�sie - {{$operation->_ref_sejour->_ref_patient->_view}}</th>
	</tr>
  <tr>
		<td><strong>Date de l'intervention</strong> {{mb_value object=$operation field=_datetime}}</td>
    <td><strong>Interventon r�alis�e</strong> {{mb_include module=dPplanningOp template=inc_vw_operation _operation=$operation nodebug=true}}</td>
	</tr>
	<tr>
		<td><strong>{{mb_label object=$operation field=anesth_id}}</strong> {{mb_value object=$operation field=anesth_id}}</td>
    <td><strong>{{mb_label object=$operation field=chir_id}}</strong> {{mb_value object=$operation field=chir_id}}</td>
	</tr>
  <tr>
  	<td><strong>{{mb_label object=$consult_anesth field=position}}</strong> {{mb_value object=$consult_anesth field=position}}</td>
		<td><strong>{{mb_label object=$operation field=type_anesth}}</strong> {{mb_value object=$operation field=type_anesth}}</td>
  </tr>
</table>
<table class="tbl">	
	<tr>
	  <th colspan="4">Evenements per-op�ratoire</th>
	</tr>		
	{{foreach from=$perops item=_perop}}
	<tr>
		{{if $_perop instanceof CAnesthPerop}}
		  <td style="width: 1%; text-align: center;">{{mb_ditto name=date value=$_perop->datetime|date_format:$dPconfig.date}}</td>
			<td style="width: 1%; text-align: center;">{{mb_ditto name=time value=$_perop->datetime|date_format:$dPconfig.time}}</td>
			<td style="font-weight: bold;" colspan="2">{{$_perop->libelle}}</td>
		{{else}}
		  {{assign var=unite value=""}}
      {{if $_perop->_ref_object instanceof CPrescriptionLineMedicament || $_perop->_ref_object instanceof CPrescriptionLineMixItem}}
        {{assign var=unite value=$_perop->_ref_object->_ref_produit->libelle_unite_presentation}}
      {{/if}}
		  <td style="width: 1%; text-align: center;">{{mb_ditto name=date value=$_perop->dateTime|date_format:$dPconfig.date}}</td>
      <td style="width: 1%; text-align: center;">{{mb_ditto name=time value=$_perop->dateTime|date_format:$dPconfig.time}}</td>
      <td colspan="2">
				{{if $_perop->_ref_object instanceof CPrescriptionLineElement}}
				  {{$_perop->_ref_object->_view}}
				{{else}}
				  {{$_perop->_ref_object->_ucd_view}}
				{{/if}}
			  <strong>{{$_perop->quantite}} {{$unite}} </strong>
      </td>
		{{/if}}
	</tr>
	{{/foreach}}
</table>

<div id="constantes"></div>