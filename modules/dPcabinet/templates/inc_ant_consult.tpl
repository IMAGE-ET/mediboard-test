{{mb_script module="dPplanningOp" script="cim10_selector" ajax=1}}
{{mb_script module="cabinet" script="dossier_medical" ajax=1}}

<script>
  var cim10url = new Url;

  reloadCim10 = function(sCode){
    var oForm = getForm("addDiagFrm");

    oCimField.add(sCode);

    {{if $_is_anesth}}
    if(DossierMedical.sejour_id){
      oCimAnesthField.add(sCode);
    }
    {{/if}}
    $V(oForm.code_diag, '');
    $V(oForm.keywords_code, '');
  };

  easyMode = function() {
    var url = new Url("dPcabinet", "vw_ant_easymode");
    url.addParam("patient_id", "{{$patient->_id}}");
    {{if isset($consult|smarty:nodefaults)}}
      url.addParam("consult_id", "{{$consult->_id}}");
    {{/if}}
    url.pop(900, 600, "Mode grille");
  };

  Main.add(function () {
    if (!DossierMedical.patient_id) {
      DossierMedical.sejour_id  = '{{$sejour_id}}';
      DossierMedical._is_anesth = '{{$_is_anesth}}';
      DossierMedical.patient_id = '{{$patient->_id}}';
    }
    {{if $conf.ref_pays == 2 && $m == "dPurgences"}}
      DossierMedical.reload_dbl = true;
    {{/if}}
    DossierMedical.reloadDossiersMedicaux();
  });
</script>

<table class="main">
  {{mb_default var=show_header value=0}}
  {{if $show_header}}
    <tr>
      <th class="title" colspan="2">
        <a style="float: left" href="?m=patients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}">
          {{mb_include module=patients template=inc_vw_photo_identite size=42}}
        </a>

        <h2 style="color: #fff; font-weight: bold;">
          {{$patient}}
          {{if isset($sejour|smarty:nodefaults)}}
          <span style="font-size: 0.7em;"> - {{$sejour->_shortview|replace:"Du":"Séjour du"}}</span>
          {{/if}}
        </h2>
      </th>
    </tr>
  {{/if}}
  
  <tr>
    <td class="halfPane">
      <table class="form">
        <tr>
          <td class="button">
            <button class="edit" type="button" onclick="easyMode();">Mode grille</button>
          </td>
        </tr>
        {{mb_include module=cabinet template=inc_ant_consult_trait}}
      </table>
    </td>
    <td class="halfPane">
      <table class="form">
        <tr>
          <th class="category">Dossier patient</th>
        </tr>
        <tr>
          <td class="text" id="listAnt"></td>
        </tr>
        {{if $_is_anesth || $sejour_id}}
        <tr>
          <th class="category">
            Eléments significatifs pour le séjour
          </th>
        </tr>
        <tr>
          <td class="text" id="listAntCAnesth"></td>
        </tr>
        {{/if}}
      </table>
    </td>
  </tr>
</table>
