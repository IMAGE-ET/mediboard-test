{{mb_script module="dPplanningOp" script="cim10_selector" ajax=1}}

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

  onSubmitAnt = function (form) {
    var rques = $(form.rques);
    if (!rques.present()) {
      return false;
    }

    onSubmitFormAjax(form, {onComplete : function() {
      DossierMedical.reloadDossiersMedicaux();
      if (window.reloadAtcd) {
        reloadAtcd();
      }
    }});

    // Après l'ajout d'antécédents
    if (Preferences.empty_form_atcd == "1") {
      $V(form.date    , "");
      $V(form.date_da , "");
      $V(form.type    , "");
      $V(form.appareil, "");
    }

    $V(form.keywords_composant, "");
    $V(form.cds, "");

    // Dans le cas des CDS, on vide le type
    if ($V(form._idex_tag) == "COMPENDIUM_CDS") {
      $V(form.type    , "");
    }

    $V(form._idex_code, "");
    $V(form._idex_tag, "");

    rques.clear().focus();

    return false;
  };

  onSubmitTraitement = function (form) {
    var trait = $(form.traitement);
    if (!trait.present()) {
      return false;
    }

    onSubmitFormAjax(form, {
      onComplete : DossierMedical.reloadDossiersMedicaux
    } );

    trait.clear().focus();

    return false;
  };

  easyMode = function() {
    var url = new Url("dPcabinet", "vw_ant_easymode");
    url.addParam("patient_id", "{{$patient->_id}}");
    {{if isset($consult|smarty:nodefaults)}}
      url.addParam("consult_id", "{{$consult->_id}}");
    {{/if}}
    url.pop(900, 600, "Mode grille");
  };

  /**
   * Mise a jour du champ _sejour_id pour la creation d'antecedent et de traitement
   * et la widget de dossier medical
   */
  DossierMedical = {
    sejour_id: '{{$sejour_id}}',
    sort_by_date : Preferences.sort_atc_by_date,
    updateSejourId : function(sejour_id) {
      this.sejour_id = sejour_id;

      // Mise à jour des formulaire
      if(document.editTrmtFrm){
        $V(document.editTrmtFrm._sejour_id, sejour_id, false);
      }
      if(document.editAntFrm){
        $V(document.editAntFrm._sejour_id, sejour_id, false);
      }
    },

    reloadDossierPatient: function() {
      var antUrl = new Url("dPcabinet", "httpreq_vw_list_antecedents");
      antUrl.addParam("patient_id", "{{$patient->_id}}");
      antUrl.addParam("_is_anesth", "{{$_is_anesth}}");
      antUrl.addParam("sort_by_date", DossierMedical.sort_by_date);
      {{if $_is_anesth}}
        antUrl.addParam("sejour_id", DossierMedical.sejour_id);
      {{/if}}
      antUrl.requestUpdate('listAnt');
      {{if $conf.ref_pays == 2 && $m == "dPurgences"}}
        refreshAntecedentsPatient();
      {{/if}}
    },

    toggleSortAntecedent: function () {
      if (DossierMedical.sort_by_date == 1) {
        DossierMedical.sort_by_date = 0;
      }
      else {
        DossierMedical.sort_by_date = 1;
      }
      DossierMedical.reloadDossierPatient();


    },
    reloadDossierSejour: function(){
      var antUrl = new Url("dPcabinet", "httpreq_vw_list_antecedents_anesth");
      antUrl.addParam("sejour_id", DossierMedical.sejour_id);
      antUrl.requestUpdate('listAntCAnesth');
    },
    reloadDossiersMedicaux: function(){
      DossierMedical.reloadDossierPatient();
      {{if $_is_anesth || $sejour_id}}
      DossierMedical.reloadDossierSejour();
      {{/if}}
    }
  };
 
  Main.add(function () {
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
