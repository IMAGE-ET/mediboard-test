{{mb_script module="dPpatients" script="pat_selector"}}
{{mb_script module="dPplanningOp" script="cim10_selector"}}
{{mb_script module="dPplanningOp" script="protocole_selector"}}
{{mb_script module=system script=alert}}

{{if "dPmedicament"|module_active}}
{{mb_script module="dPmedicament" script="medicament_selector"}}
{{mb_script module="dPmedicament" script="equivalent_selector"}}
{{/if}}

{{mb_script module="ssr" script="cotation_rhs"}}
{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{mb_script module="files" script="file"}}

{{mb_include module=ssr template=inc_form_sejour_ssr}}

{{if $sejour->_id && $can->read}}
<script>
  
Main.add(function() {
  var tabs = Control.Tabs.create('tab-sejour', true);
  (tabs.activeLink.onmousedown || Prototype.emptyFunction)();
});

var constantesMedicalesDrawn = false;
refreshConstantesMedicales = function (force) {
  if (!constantesMedicalesDrawn || force) {
    var url = new Url("patients", "httpreq_vw_constantes_medicales");
    url.addParam("patient_id", {{$sejour->_ref_patient->_id}});
    url.addParam("context_guid", "{{$sejour->_guid}}");
    url.addParam("selection[]", ["poids", "taille"]);
    if (window.oGraphs) {
      url.addParam('hidden_graphs', JSON.stringify(window.oGraphs.getHiddenGraphs()));
    }
    url.requestUpdate("constantes");
    constantesMedicalesDrawn = true;
  }
};

refreshSejoursSSR = function(sejour_id){
  var url = new Url("ssr", "ajax_vw_sejours_patient");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate("sejours_ssr");
};

loadDocuments = function(sejour_id) {
  var url = new Url("dPhospi", "httpreq_documents_sejour");
  url.addParam("sejour_id" , sejour_id);
  url.requestUpdate("docs");
};

</script>

<ul id="tab-sejour" class="control_tabs">
  {{if !$conf.ssr.recusation.sejour_readonly}}
  <li>
    <a href="#hebergement">
      Hebergement
    </a>
  </li>
  {{/if}}
  <li>
    <a href="#autonomie">
      {{tr}}CFicheAutonomie{{/tr}}
    </a>
  </li>
  {{if $can_view_dossier_medical}}
  
    {{if !$sejour->annule}}
    <li>
      <a href="#constantes" onmousedown="refreshConstantesMedicales();">
        {{tr}}CPatient.surveillance{{/tr}}
      </a>
    </li>

    <li style="display: none;">
      <a href="#antecedents">
        {{tr}}CAntecedent{{/tr}}s &amp; {{tr}}CTraitement{{/tr}}s
      </a>
    </li>  

    <li>
      <a href="#bilan" onmousedown="refreshSejoursSSR('{{$sejour->_id}}');">
        {{tr}}CPrescription{{/tr}} &amp; {{tr}}CBilanSSR{{/tr}}
      </a>
    </li>

    <li>
      <a {{if $bilan->_id && !$bilan->planification}} class="empty" {{/if}}
        href="#planification" onmousedown="Planification.refresh('{{$sejour->_id}}')">
        Planification
      </a>
    </li>
    <li>
      <a {{if $bilan->_id && !$bilan->planification}} class="empty" {{/if}}
        href="#cotation-rhs" onmousedown="CotationRHS.refresh('{{$sejour->_id}}')">
        Cotation
      </a>
    </li>
    {{/if}}

  {{/if}}
  <li>
    <a href="#docs" onmousedown="loadDocuments('{{$sejour->_id}}')">
      Documents
    </a>
  </li>
</ul>

<hr class="control_tabs" /> 

<div id="hebergement" style="display: none;">
  {{mb_include template=inc_form_hebergement}}
</div> 

<div id="autonomie" style="display: none;">
  {{if $fiche_autonomie->_id || !"forms"|module_active || ("forms"|module_active && !$conf.ssr.CFicheAutonomie.use_ex_form)}}
    {{mb_include template=inc_form_fiche_autonomie}}
  {{else}}
    {{unique_id var=unique_id_fich_autonomie}}

    <div id="fiche_auto_{{$unique_id_fich_autonomie}}">
      <script>
        createBilanSSRcallback{{$unique_id_fich_autonomie}} = function(bilan_id, obj) {
          updateBilanId(bilan_id, obj);

          ExObject.loadExObjects("CBilanSSR", bilan_id, "fiche_auto_{{$unique_id_fich_autonomie}}", 0);
        }
      </script>
      {{if !$bilan->_id}}
        <form name="Create-CBilanSSR" action="?m=ssr" method="post" onsubmit="return onSubmitFormAjax(this);">
          <input type="hidden" name="m" value="ssr" />
          <input type="hidden" name="dosql" value="do_bilan_ssr_aed" />
          <input type="hidden" name="callback" value="createBilanSSRcallback{{$unique_id_fich_autonomie}}" />
          {{mb_key object=$bilan}}
          {{mb_field object=$bilan field=sejour_id hidden=1}}
          <button type="submit" class="new">Accéder à la fiche d'autonomie</button>
        </form>
      {{else}}
        <script type="text/javascript">
          Main.add(function(){
            ExObject.loadExObjects("{{$bilan->_class}}", "{{$bilan->_id}}", "fiche_auto_{{$unique_id_fich_autonomie}}", 0);
          });
        </script>
      {{/if}}
    </div>
  {{/if}}
</div>

{{if $can_view_dossier_medical}}
<script type="text/javascript">
function loadAntecedents(sejour_id){
  var url = new Url("dPcabinet","httpreq_vw_antecedents");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate('antecedents')
}

// Main.add(loadAntecedents.curry({{$sejour->_id}}));
</script>
{{/if}}

<div id="antecedents" style="display: none;">
</div>

<div id="bilan" style="display: none;">
  {{mb_include template=inc_form_bilan_ssr}}
</div>

<div id="planification" style="display: none;">
  {{mb_include template=inc_planification}}
</div>

<div id="cotation-rhs" style="display: none;">
</div>

<div id="constantes" style="display: none;">
</div>

<div id="docs" style="display: none;">
</div>
{{/if}}