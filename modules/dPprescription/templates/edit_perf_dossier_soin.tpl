{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=prescription_line_mix_id value=$prescription_line_mix->_id}}
<script type="text/javascript">

// Submit du debut ou de la fin de la perf
submitTiming = function(){
  var oFormClick = window.opener.document.click;
  var oForm = document.forms['editPerf{{$prescription_line_mix->_id}}'];
  submitFormAjax(oForm,'systemMsg', { onComplete: function() { 
  	refreshPerfTiming();
		refreshDossierSoin();
  } } );
}

submitTransmissions = function(){
  var oForm = document.forms['editTrans'];
  submitFormAjax(oForm,'systemMsg', { onComplete: function() { 
    if (window.opener.updateNbTrans) {
      window.opener.updateNbTrans('{{$sejour_id}}');
    }
  	refreshPerfTransmissions();
    refreshDossierSoin();
		window.opener.loadSuivi('{{$sejour_id}}');
  } } );
}

refreshDossierSoin = function(){
  var oFormClick = window.opener.document.click;
	window.opener.PlanSoins.loadTraitement('{{$sejour_id}}','{{$date}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}','{{$prescription_line_mix->_id}}','{{$prescription_line_mix->_class}}','');
}

refreshPerfTiming = function(){
  var url = new Url;
  url.setModuleAction("dPprescription", "edit_perf_dossier_soin");
  url.addParam("prescription_line_mix_id", "{{$prescription_line_mix_id}}");
  url.addParam("mode_refresh", "timing");
  url.requestUpdate("perf_timing");
}

refreshPerfTransmissions = function(transmission_id){
  var url = new Url("dPhospi", "ajax_transmission");
  url.addParam("sejour_id"   , "{{$transmission->sejour_id}}");
  url.addParam("user_id"     , '{{$transmission->user_id}}');
  url.addParam("object_id"   , '{{$prescription_line_mix_id}}');
  url.addParam("object_class", "CPrescriptionLineMix");
  url.requestUpdate("perf_trans");
}

Main.add( function(){
  refreshPerfTiming();
} );

</script>

<!-- Gestion du debut et de la fin de la prescription_line_mix -->
<div id="perf_timing"></div>

<!-- Gestion des transmissions -->
<div id="perf_trans">
{{assign var=hide_cible value=1}}
{{mb_include module=dPhospi template=inc_transmission refreshTrans=0}}
</div>