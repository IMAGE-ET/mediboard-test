<script type="text/javascript">

  refreshConstantesMedicales = function(context_guid) {
    if(context_guid) {
      var url = new Url("patients", "httpreq_vw_constantes_medicales");
      url.addParam("context_guid", context_guid);
      url.addParam("can_edit", 0);
      url.addParam("can_select_context", 0);
      if (window.oGraphs) {
        url.addParam('hidden_graphs', JSON.stringify(window.oGraphs.getHiddenGraphs()));
      }
      url.requestUpdate("constantes");
    }
  };

  constantesMedicalesDrawn = false;
  refreshConstantesHack = function(sejour_id) {
    (function(){
      if (constantesMedicalesDrawn == false && $('constantes').visible() && sejour_id) {
        refreshConstantesMedicales('CSejour-'+sejour_id);
        constantesMedicalesDrawn = true;
      }
    }).delay(0.5);
  };

  loadResultLabo = function(sejour_id) {
    var url = new Url("dPImeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate('result_labo');
  };

  Main.add( function(){
    dossier_sejour_tabs = Control.Tabs.create('dossier_sejour_tab_group', true);
    PMSI.loadPatient(null, {{$object->_id}});
    refreshConstantesHack('{{$object->_id}}');
    {{if $isImedsInstalled}}
    loadResultLabo('{{$object->_id}}');
    {{/if}}
  } );

</script>

<table class="main layout">
  <tr>
    <td style="white-space: nowrap" class="narrow">
      <ul id="dossier_sejour_tab_group" class="control_tabs_vertical">
        <li><a href="#div_patient" onmousedown="PMSI.loadPatient(null, {{$object->_id}})">Identité du patient</a></li>
        <li><a href="#div_sejour" >Séjour</a></li>
        <li onmousedown="refreshConstantesHack('{{$object->_id}}')"><a href="#constantes">Constantes</a></li>
        {{if $isImedsInstalled}}
          <li><a href="#result_labo">Labo</a></li>
        {{/if}}
      </ul>
    </td>
    <td>
      <div id="div_patient" style="display:none;"></div>
      <div id="div_sejour" style="display:none;">
        {{mb_include module=planningOp template="CSejour_complete"}}
      </div>
      <div id="constantes" style="display:none;"></div>
      {{if $isImedsInstalled}}
        <div id="result_labo" style="display:none;"></div>
      {{/if}}
    </td>
  </tr>
</table>
