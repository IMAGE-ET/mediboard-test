{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function(){
  {{if $blood_salvage->_id}}
	  var url = new Url;
	  url.setModuleAction("bloodSalvage","httpreq_vw_recuperation_start_timing");
	  url.addParam("blood_salvage_id","{{$blood_salvage->_id}}");
	  url.requestUpdate("start-timing");
  {{/if}}
  }
);

</script>
{{mb_include_script module="bloodSalvage" script="bloodSalvage"}}

{{assign var=patient value=$selOp->_ref_sejour->_ref_patient}}  
{{if $blood_salvage->_id }}
  <!-- Informations sur le patient (Groupe, rhésus, ASA, RAI...) -->
	<div id="info-patient">
		{{include file="inc_vw_patient_infos.tpl"}}
	</div>
	<div id="start-timing"></div>
	<div id="materiel">
   {{include file=inc_blood_salvage_conso.tpl}}
  </div>
 <div id="unregister" style="float:left">
 <form name="inscriptionRSPO" action="?m={{$m}}" method="post">
  <input type="hidden" name="blood_salvage_id" value="{{$blood_salvage->_id}}">
  <input type="hidden" name="m" value="bloodSalvage" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="dosql" value="do_bloodSalvage_aed" />
  <button type="button" class="cancel" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$blood_salvage->_view|smarty:nodefaults|JSAttribute}}'})">Désinscrire</button>
  </form>
 </div>
{{else}}
	<div class="big-info">
		Aucun Cell Saver n'est prévu pour cette intervention.
	</div>
	<div id="register" style="text-align:center">
	<form name="inscriptionRSPO" action="?m={{$m}}" method="post">
	<input type="hidden" name="operation_id" value="{{$selOp->_id}}">
	<input type="hidden" name="m" value="bloodSalvage" />
  <input type="hidden" name="dosql" value="do_bloodSalvage_aed" />
  <button type="button" class="new" onclick="submitNewBloodSalvage(this.form);">Inscrire le patient au protocole RSPO</button>
  </form>
  </div>
{{/if}}
