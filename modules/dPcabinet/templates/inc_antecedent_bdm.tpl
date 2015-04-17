{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  // UpdateFields de l'autocomplete de medicaments
  updateFieldsMedicamentTP{{$addform}} = function(selected) {
    var oFormTP = getForm("editLineTP{{$addform}}");
    // Submit du formulaire avant de faire le selection d'un nouveau produit
    if ($V(oFormTP._code)) {
      onSubmitFormAjax(oFormTP, function() {
        updateTP{{$addform}}(selected);
        DossierMedical.reloadDossiersMedicaux();
      });
    }
    else {
      updateTP{{$addform}}(selected);
    }
  };

  updateTP{{$addform}} = function(selected) {
    var oFormTP = getForm("editLineTP{{$addform}}");
    resetEditLineTP{{$addform}}();
    Element.cleanWhitespace(selected);
    var dn = selected.childElements();
    dn = dn[0].innerHTML;

    // On peut saisir un traitement personnel seulement le code CIP est valide
    if (isNaN(parseInt(dn))) {
      return
    }
    $V(oFormTP._code, dn);
    $("_libelle{{$addform}}").insert("<button type='button' class='cancel notext' onclick='resetEditLineTP{{$addform}}(); resetFormTP{{$addform}}();'></button>" +
    "<a href=\"#nothing\" onclick=\"Prescription.viewProduit('','','"+selected.down(".code-cis").getText()+"')\">"+
    selected.down(".libelle").getText()+"</a>");

    if (selected.down(".alias")) {
      $("_libelle{{$addform}}").insert(selected.down(".alias").getText());
    }

    if (selected.down(".forme")) {
      $("_libelle{{$addform}}").insert("<br /><span class='compact'>"+selected.down(".forme").getText()+"</span>");
    }

    $V(oFormTP.produit, '');
    $('button_submit_traitement{{$addform}}').focus();
  };

  resetEditLineTP{{$addform}} = function() {
    var oFormTP = getForm("editLineTP{{$addform}}");
    $("_libelle{{$addform}}").update("");
    oFormTP._code.value = '';
  };

  resetFormTP{{$addform}} = function() {
    var oFormTP = getForm("editLineTP{{$addform}}");
    $V(oFormTP.commentaire, '');
    $V(oFormTP.token_poso, '');
    $('addPosoLine{{$addform}}').update('');

    $V(oFormTP.long_cours, 1);
    $V(oFormTP.__long_cours, true);
  }

  refreshAddPoso{{$addform}} = function(code){
    var url = new Url("dPprescription", "httpreq_vw_select_poso");
    url.addParam("_code", code);
    url.addParam("addform", "{{$addform}}");
    url.requestUpdate("addPosoLine{{$addform}}");
  };

  submitAndCallback = function(form, callback) {
    $V(form.callback, callback);
    onSubmitFormAjax(form, function() {
      resetEditLineTP{{$addform}}();
      resetFormTP{{$addform}}();
    });
  }

  checkPosos = function() {
    var div = $("list_posogestion_tp");
    if (div.select("button").length == 0) {
      alert($T('CPrisePosologie-_poso_missing'));
      return false;
    }
    return true;
  }

  Main.add(function() {
    getForm('editLineTP{{$addform}}').produit.focus();

    // Autocomplete des medicaments
    var urlAuto = new Url("medicament", "httpreq_do_medicament_autocomplete");
    urlAuto.autoComplete(getForm('editLineTP{{$addform}}').produit, "_produit_auto_complete{{$addform}}", {
      minChars: 3,
      updateElement: updateFieldsMedicamentTP{{$addform}},
      callback: function(input, queryString) {
        var form = getForm('editLineTP{{$addform}}');
        return (queryString + "&produit_max=40&only_prescriptible_sf=0&with_alias=1&mask_generique="+($V(form.mask_generique)?'1':'0'));
      }
    } );
  });
</script>
<!-- Formulaire d'ajout de traitements -->
<form name="editLineTP{{$addform}}" action="?m=cabinet" method="post">
  <input type="hidden" name="m" value="prescription" />
  <input type="hidden" name="dosql" value="do_add_line_tp_aed" />
  <input type="hidden" name="_code" value="" onchange="refreshAddPoso{{$addform}}(this.value);"/>
  <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />
  <input type="hidden" name="praticien_id" value="{{$userSel->_id}}" />
  <input type="hidden" name="callback" value="" />
  <table class="layout">
    <col style="width: 70px;" />
    <col class="narrow" />

    <tr>
      <th>Recherche</th>
      <td>
        <div class="dropdown">
          <input type="text" name="produit" value="" size="12" class="autocomplete" />
          <div style="display:none; width: 350px;" class="autocomplete" id="_produit_auto_complete{{$addform}}"></div>
        </div>
        <button type="button" class="search notext" onclick="MedSelector.init('produit');"></button>
        <script>
          MedSelector.init = function(onglet) {
            this.sForm = "editLineTP{{$addform}}";
            this.sView = "produit";
            this.sCode = "_code";
            this.sSearch = document.editLineTP{{$addform}}.produit.value;
            this.sSearchByCIS = 1;
            this.selfClose = true;
            this._DCI = 0;
            this.sOnglet = onglet;
            this.traitement_perso = true;
            this.only_prescriptible_sf = 0;
            this.addForm = '{{$addform}}';
            this.pop();
          }
        </script>
      </td>
      <td>
        <strong><div id="_libelle{{$addform}}"></div></strong>
      </td>
    </tr>
    <tr>
      <td></td>
      <td>
        <input name="mask_generique" value="{{$app->user_prefs.check_default_generique}}" title="Masquer les génériques"
               {{if $app->user_prefs.check_default_generique}}checked="checked"{{/if}}
               type="{{if "dPprescription general see_generique"|conf:"CGroups-$g"}}checkbox{{else}}hidden{{/if}}"/>
        {{if "dPprescription general see_generique"|conf:"CGroups-$g"}}
          <label for="mask_generique">Masquer les génériques</label>
        {{/if}}
      </td>
    </tr>
    <tr>
      {{if $app->user_prefs.showDatesAntecedents}}
        <th>{{mb_label object=$line field="debut"}}</th>
        <td>{{mb_field object=$line field="debut" register=true form=editLineTP$addform}}</td>
      {{else}}
        <td colspan="2"></td>
      {{/if}}
      <td rowspan="3" id="addPosoLine{{$addform}}"></td>
    </tr>

    {{if $app->user_prefs.showDatesAntecedents}}
      <tr>
        <th>{{mb_label object=$line field="fin"}}</th>
        <td>{{mb_field object=$line field="fin" register=true form=editLineTP$addform}}</td>
      </tr>
    {{/if}}

    <tr>
      <th>{{mb_label object=$line field="commentaire"}}</th>
      <td>{{mb_field object=$line field="commentaire" size=20 form=editLineTP$addform}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$line field="long_cours"}}</th>
      <td>{{mb_field object=$line field="long_cours" typeEnum=checkbox value=1}}</td>
    </tr>

    <tr>
      <td colspan="3" {{if !$gestion_tp}}class="button"{{/if}}>
        <button id="button_submit_traitement{{$addform}}" class="tick" type="button" onclick="addToTokenPoso{{$addform}}(0);onSubmitFormAjax(this.form, function() {
        {{if $callback}}
          {{$callback}}();
        {{elseif $reload}}
          DossierMedical.reloadDossierPatient('{{$reload}}', '{{$type_see}}');
        {{else}}
          DossierMedical.reloadDossiersMedicaux();
        {{/if}}
          resetEditLineTP{{$addform}}();
          resetFormTP{{$addform}}();
          } ); this.form.produit.focus();">
          {{tr}}Add{{/tr}} le traitement
        </button>
        {{if $gestion_tp}}
          <fieldset style="display: inline-block">
            <legend>Ajouter et ...  <button type="button" class="search notext" onclick="modal('legend_actions_tp')">Légende</button></legend>
            <button type="button" class="stop" onclick="addToTokenPoso{{$addform}}(0);submitAndCallback(this.form, 'stopLineTP');">
              Arrêter
            </button>
            <button type="button" class="edit" onclick="addToTokenPoso{{$addform}}(0);submitAndCallback(this.form, 'modifyLineTP');">
              Represcrire en modifiant
            </button>
            {{if $sejour_id}}
              <button type="button" class="right" onclick="addToTokenPoso{{$addform}}(0); if (checkPosos()) { submitAndCallback(this.form, 'poursuivreLineTP'); }">
                Poursuivre
              </button>
              <button type="button" class="hslip" onclick="addToTokenPoso{{$addform}}(0);submitAndCallback(this.form, 'relaiLineDialog');">
                Relai
              </button>
              <button type="button" class="pause" onclick="addToTokenPoso{{$addform}}(0);submitAndCallback(this.form, 'pauseLineDialog')">
                Pause
              </button>
            {{/if}}
          </fieldset>

        {{/if}}
      </td>
    </tr>
  </table>
</form>