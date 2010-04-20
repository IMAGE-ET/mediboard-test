{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=perfusion_id value=$perfusion->_id}}
<script type="text/javascript">

// Submit du debut ou de la fin de la perf
submitTiming = function(){
  var oFormClick = window.opener.document.click;
  var oForm = document.forms['editPerf{{$perfusion->_id}}'];
  submitFormAjax(oForm,'systemMsg', { onComplete: function() { 
  	refreshPerfTiming();
		refreshDossierSoin();
  } } );
}

submitTransmissions = function(){
  var oForm = document.forms['editTrans'];
  submitFormAjax(oForm,'systemMsg', { onComplete: function() { 
  	refreshPerfTransmissions(); 
    refreshDossierSoin();
		window.opener.loadSuivi('{{$sejour_id}}');
  } } );
}

refreshDossierSoin = function(){
  var oFormClick = window.opener.document.click;
	window.opener.Prescription.loadTraitement('{{$sejour_id}}','{{$date}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}','{{$perfusion->_id}}','{{$perfusion->_class_name}}','');
}

refreshPerfTiming = function(){
  var url = new Url;
  url.setModuleAction("dPprescription", "edit_perf_dossier_soin");
  url.addParam("perfusion_id", "{{$perfusion_id}}");
  url.addParam("mode_refresh", "timing");
  url.requestUpdate("perf_timing");
}

refreshPerfTransmissions = function(){
  var url = new Url;
  url.setModuleAction("dPprescription", "edit_perf_dossier_soin");
  url.addParam("perfusion_id", "{{$perfusion_id}}");
  url.addParam("mode_refresh", "trans");
  url.requestUpdate("perf_trans");
}

Main.add( function(){
  refreshPerfTiming();
  refreshPerfTransmissions();
} );

</script>

<!-- Gestion du debut et de la fin de la perfusion -->
<div id="perf_timing"></div>

<!-- Gestion des transmissions -->
<div id="perf_trans"></div>