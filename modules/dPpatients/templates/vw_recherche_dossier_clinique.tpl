{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=patients script=patient}}
{{mb_script module=planningOp script=ccam_selector}}
{{mb_script module=patients script=pat_selector}}

<script type="text/javascript">
function changePage (start) {
  $V(getForm('rechercheDossierClinique').start, start);
}

function updateTokenATC(v) {
  var i, codes = v.split("|").without("");
  for (i = 0; i < codes.length; i++) {
    codes[i] += '<button class="remove notext" type="button" onclick="ATCTokenField.remove(\''+codes[i]+'\')"></button>';
  }
  $("list_atc").update(codes.join(", "));
  $V(getForm("rechercheDossierClinique").keywords_atc, '');
}

function updateSection(name) {
  var sections = ['consult_section', 'sejour_section', 'operation_section'];
  
  sections.each(function(section_name) {
    if (section_name != name) {
      var section = $(section_name);
      section.select("input", "select", " button").invoke("setAttribute", "disabled", null);
      section.addClassName("opacity-30");
    }
  });
  
  var section = $(name);
  section.select("input", "select", " button").invoke("writeAttribute", "disabled", null);
  section.removeClassName("opacity-30");
}

function emptyProduit() {
  var form = getForm("rechercheDossierClinique");
  $V(form.code_cis, '');
  $V(form.code_ucd, '');
  $V(form.produit, '');
}

function emptyATC() {
  $V(getForm('rechercheDossierClinique').classes_atc, '');
  updateTokenATC('');
}

function emptyComposant() {
  var form = getForm("rechercheDossierClinique");
  $V(form.composant, '');
  $V(form.keywords_composant, '');
}

function emptyIndication() {
  var form = getForm("rechercheDossierClinique");
  $V(form.indication, '');
  $V(form.keywords_indication, '');
  $V(form.type_indication, '');
}

function emptyCommentaire() {
  var form = getForm("rechercheDossierClinique");
  $V(form.commentaire, '');
}

function exportResults() {
  var form = getForm("rechercheDossierClinique");
  $V(form.export, 1);
  $V(form.suppressHeaders, 1);
  form.submit();
  $V(form.export, 0);
  $V(form.suppressHeaders, 0);
}

Main.add(function() {
  Control.Tabs.create("tabs-prescription", true);
  
  var form = getForm("rechercheDossierClinique");
  
  // Pat Selector
  PatSelector.init = function(){
    this.sForm      = "rechercheDossierClinique";
    this.sId        = "patient_id";
    this.sView      = "_pat_name";
    this.pop();
  };
  
  // Autocomplete des medicaments
  var url = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
  url.addParam("produit_max", 40);
  url.autoComplete(form.produit, "produit_auto_complete", {
    minChars: 3,
    afterUpdateElement: function(input, selected) {
      var code_cis = selected.select(".code-cis")[0].innerHTML;
      if (code_cis != "") {
        $V(input.form.code_cis, code_cis);
      }
      // Si pas de cis, on recherche par ucd
      else {
        $V(input.form.code_ucd, selected.select(".code-ucd")[0].innerHTML);
      }
      var libelle_ucd = selected.select("small.libelle")[0].innerHTML;
      libelle_ucd = libelle_ucd.replace(/(^\s+|\s+$)/g, '').replace(/<em>|<\/em>/g, '');
      $V(input, libelle_ucd);
    }
  } );
  
  // Autocomplete et TokenField des classes ATC
  ATCTokenField = new TokenField(form.classes_atc, { 
    onChange : updateTokenATC
  });
  
  updateTokenATC($V(form.classes_atc));
  
  var urlATC = new Url("medicament", "ajax_atc_autocomplete");
  urlATC.autoComplete(form.keywords_atc, null, {
    minChars: 1,
    updateElement: function(selected) {
      var form = getForm("rechercheDossierClinique");
      $V(form.keywords_atc, selected.select(".view")[0].innerHTML.replace(/<em>|<\/em>/g, ''));
      ATCTokenField.add($V(form.keywords_atc), true);
    }
  });
  
  // Autocomplete des composants
  var urlComposant = new Url("medicament", "ajax_composant_autocomplete");
  urlComposant.autoComplete(form.keywords_composant, null, {
    minChars: 3,
    afterUpdateElement: function(input, selected) {
      var form = getForm("rechercheDossierClinique");
      $V(input, selected.select(".view")[0].innerHTML.replace(/<em>|<\/em>/g, ''));
      $V(form.composant, selected.get("code"));
    }
  });

  // Autocomplete des indications
  var urlIndication = new Url("medicament", "ajax_indication_autocomplete");
  urlIndication.autoComplete(form.keywords_indication, null, {
    minChars: 3,
    afterUpdateElement: function(input, selected) {
      var form = getForm("rechercheDossierClinique");
      $V(input, selected.select(".view")[0].innerHTML.replace(/<em>|<\/em>/g, ''));
      $V(form.indication, selected.get("code"));
      $V(form.type_indication, selected.get("type"));
    }
  });
  
  updateSection("consult_section");
});
</script>

<style type="text/css">
  @media print {
    #search-results {
      width: 100%;
      height: auto;
    }
  }
</style>

<form name="rechercheDossierClinique" method="get" action="?" target="_blank"
      onsubmit="Control.Modal.close(); var url = Url.update(this, null, {openModal: true}); modal_results = url.modalObject; return false;">
  <input type="hidden" name="m" value="patients" />
  <input type="hidden" name="a" value="ajax_recherche_dossier_clinique" />
  <input type="hidden" name="start" value="0" onchange="this.form.onsubmit()" />
  <input type="hidden" name="export" value="0"/>
  <input type="hidden" name="suppressHeaders" value="0"/>
  
  <table class="main layout">
    <tr>
      <td colspan="2">
      
        <table class="main form">
          <tr>
            <th>Praticien</th>
            <td>
              {{if $users_list|@count}}
                <select name="user_id">
                  {{mb_include module=mediusers template=inc_options_mediuser list=$users_list selected=$user_id}}
                </select>
              {{else}}
                <input type="hidden" name="user_id" value="{{$app->_ref_user->_id}}" />
                {{$app->_ref_user}}
              {{/if}}
            </td>
          </tr>
          <tr>
            <th>Date min</th>
            <td>{{mb_field object=$sejour field=entree register=true form=rechercheDossierClinique}}</td>
          </tr>
          <tr>
            <th>Date max</th>
            <td>{{mb_field object=$sejour field=sortie register=true form=rechercheDossierClinique}}</td>
          </tr>
          
          <tr>
            <th colspan="2" class="title">{{tr}}CPatient{{/tr}}</th>
          </tr>
          
          <tr>
            <th>{{mb_label class=CConsultation field=patient_id}}</th>
            <td>
              {{mb_field object=$patient field="patient_id" hidden=1 ondblclick="PatSelector.init()"}}
              <input type="text" name="_pat_name" style="width: 15em;" readonly="readonly" onfocus="PatSelector.init()" />
              <button class="search notext" type="button" onclick="PatSelector.init()">{{tr}}Search{{/tr}}</button>
              <button class="cancel notext" type="button" onclick="$V(this.form.patient_id, ''); $V(this.form._pat_name, '')">{{tr}}Delete{{/tr}}</button>
            </td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$patient field=sexe}}</th>
            <td>{{mb_field object=$patient field=sexe emptyLabel="Tous"}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$patient field=_age}} à l'epoque</th>
            <td>
              entre
              {{mb_field object=$patient field=_age_min increment=true form=rechercheDossierClinique size=2}}
              et
              {{mb_field object=$patient field=_age_max increment=true form=rechercheDossierClinique size=2}}
              ans
            </td>
          </tr>
          
          <tr>
            <th>Médecin correspondant</th>
            <td>
              <script type="text/javascript">
                Main.add(function () {
                  var formTraitant = getForm("rechercheDossierClinique");
                  var urlTraitant = new Url("dPpatients", "httpreq_do_medecins_autocomplete");
                  urlTraitant.autoComplete(formTraitant._view, null, {
                    minChars: 2,
                    updateElement : function(element) {
                      $V(formTraitant.medecin_traitant, element.id.split('-')[1]);
                      $V(formTraitant._view, element.down(".view").innerHTML.stripTags());
                    }
                  });
                });
              </script>
              <input type="text" name="_view" value="{{$patient->_ref_medecin_traitant}}" size="25" />
              {{mb_field object=$patient field=medecin_traitant hidden=true}}
              <button type="button" class="cancel notext" onclick="this.form.medecin_traitant.value='';this.form._view.value='';"></button>
              <br />
              <label><input type="checkbox" name="only_medecin_traitant" /> Seulement en tant que médecin traitant</label>
            </td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$antecedent field=rques}}</th>
            <td>{{mb_field object=$antecedent field=rques prop=str}}</td>
          </tr>
          {{*
          <tr>
            <th>{{mb_label object=$traitement field=traitement}}</th>
            <td>{{mb_field object=$traitement field=traitement prop=str}}</td>
          </tr>
           *}}
          </table>
       </td>
      </tr>
      <tr>
        <td style="width: 50%">
          <table class="form">
            <tr>
              <th colspan="2" class="title">
                <input type="radio" name="section_choose" value="consult"
                style="float: left;" checked onclick="updateSection('consult_section')"/> {{tr}}CConsultation{{/tr}}
              </th>
            </tr>
            <tbody id="consult_section">
              <tr>
                <th>{{mb_label object=$consult field=motif}}</th>
                <td>{{mb_field object=$consult field=motif prop=str}}</td>
              </tr>
              
              <!-- champ inexistant dans la class COperation (libelle = meme nom que le champ dans CSejour) -->
              <tr>
                <th><label for="_rques_consult">{{tr}}CConsultation-rques{{/tr}}</label></th>
                <td><input type="text" name="_rques_consult" value="{{$consult->_rques_consult}}" /></td>
              </tr>
              
              <!-- champ inexistant dans la class COperation (rques = meme nom que le champ dans CSejour) -->
              <tr>
                <th><label for="_examen_consult">{{tr}}CConsultation-examen{{/tr}}</label></th>
                <td><input type="text" name="_examen_consult" value="{{$consult->_examen_consult}}" /></td>
              </tr>
              
              <tr>
                <th>{{mb_label object=$consult field=conclusion}}</th>
                <td>{{mb_field object=$consult field=conclusion prop=str}}</td>
              </tr>
            </tbody>
            <tr>
              <th colspan="2" class="title">
                <input type="radio" name="section_choose" style="float: left;" value="sejour"
                onclick="updateSection('sejour_section')" /> {{tr}}CSejour{{/tr}}
              </th>
            </tr>
            <tbody id="sejour_section">
              <tr>
                <th>{{mb_label object=$sejour field=libelle}}</th>
                <td>{{mb_field object=$sejour field=libelle prop=str}}</td>
              </tr>
              
              <tr>
                <th>{{mb_label object=$sejour field=type}}</th>
                <td>{{mb_field object=$sejour field=type emptyLabel="Tous" canNull=true}}</td>
              </tr>
              
              <!-- champ inexistant dans la class CSejour (rques = meme nom que le champ dans CAntecedent) -->
              <tr>
                <th><label for="_rques_sejour">{{tr}}CSejour-rques{{/tr}}</label></th>
                <td><input type="text" name="_rques_sejour" value="{{$sejour->_rques_sejour}}" /></td>
              </tr>
              
              <tr>
                <th>{{mb_label object=$sejour field=convalescence}}</th>
                <td>{{mb_field object=$sejour field=convalescence prop=str}}</td>
              </tr>
            </tbody>
            
            
            <tr>
              <th colspan="2" class="title">
                <input type="radio" name="section_choose" style="float: left;" value="operation"
                onclick="updateSection('operation_section')" /> {{tr}}COperation{{/tr}}
              </th>
            </tr>
            <tbody id="operation_section">
              <!-- champ inexistant dans la class COperation (libelle = meme nom que le champ dans CSejour) -->
              <tr>
                <th><label for="_libelle_interv">{{tr}}COperation-libelle{{/tr}}</label></th>
                <td><input type="text" name="_libelle_interv" value="{{$interv->_libelle_interv}}" /></td>
              </tr>
              
              <!-- champ inexistant dans la class COperation (rques = meme nom que le champ dans CSejour) -->
              <tr>
                <th><label for="_rques_interv">{{tr}}COperation-rques{{/tr}}</label></th>
                <td><input type="text" name="_rques_interv" value="{{$interv->_rques_interv}}" /></td>
              </tr>
              
              <tr>
                <th>{{mb_label object=$interv field=examen}}</th>
                <td>{{mb_field object=$interv field=examen prop=str}}</td>
              </tr>
              
              <tr>
                <th>{{mb_label object=$interv field=materiel}}</th>
                <td>{{mb_field object=$interv field=materiel prop=str}}</td>
              </tr>

              <tr>
                <th>{{mb_label object=$interv field=exam_per_op}}</th>
                <td>{{mb_field object=$interv field=exam_per_op prop=str}}</td>
              </tr>

              <tr>
                <th>{{mb_label object=$interv field=codes_ccam}}</th>
                <td>
                  {{mb_field object=$interv field=codes_ccam size=12}} 
                  <button class="search notext" type="button" onclick="CCAMSelector.init()">Rechercher</button>
                  <script type="text/javascript">   
                    CCAMSelector.init = function(){
                      this.sForm = "rechercheDossierClinique";
                      this.sClass = "object_class";
                      this.sChir = "user_id";
                      this.sView = "codes_ccam";
                      this.pop();
                    }
                  </script>
                  <br />
                  (codes complets ou partiels séparés par des virgules)
                </td>
              </tr>
            </tbody>
          </table>
        </td>
        <td style="50%">
          <table class="form">
          <tr>
            <th colspan="2" class="title">{{tr}}CPrescription{{/tr}}</th>
          </tr>
          <tr>
            <th class="category" colspan="2">
              Produit  
            </th>
          </tr>
          <tr>
            <td colspan="2">
            <input type="hidden" name="code_cis" value="{{$line_med->code_cis}}"/>
            <input type="hidden" name="code_ucd" value="{{$line_med->code_ucd}}"/>
            <input type="text" name="produit"
              value="{{if $line_med->code_cis || $line_med->code_ucd}}{{$line_med->_ucd_view}}{{else}}&mdash; {{tr}}CPrescription.select_produit{{/tr}}{{/if}}" size="20"
              style="font-weight: bold; font-size: 1.3em; width: 300px;" class="autocomplete"
              onclick="emptyProduit(); emptyATC(); emptyComposant(); emptyIndication(); emptyCommentaire();"/>
            <div style="display:none; width: 350px;" class="autocomplete" id="produit_auto_complete"></div>
              </div>
            </td>
          </tr>
          <tr>
            <th class="category" colspan="2">Classes ATC</th>
          </tr>
          <tr>
            <td colspan="2">
              <input type="hidden" name="classes_atc" value="{{$classes_atc}}"/>
              <input type="text" name="keywords_atc" class="autocomplete" value="{{$keywords_atc}}"
                style="font-weight: bold; font-size: 1.3em; width: 300px;"
                onclick="emptyProduit(); emptyComposant(); emptyIndication(); emptyCommentaire();"/>
              <div id="list_atc"></div>
            </td>
          </tr>
          <tr>
            <th class="category" colspan="2">
              Composant
            </th>
          </tr>
          <tr>
            <td colspan="2">
              <input type="hidden" name="composant" value="{{$composant}}"/>
              <input type="text" name="keywords_composant" class="autocomplete" 
              onclick="emptyProduit(); emptyATC(); emptyComposant(); emptyIndication(); emptyCommentaire();"
              style="font-weight: bold; font-size: 1.3em; width: 300px;" value="{{$keywords_composant}}" />
            </td>
          </tr>
          <tr>
            <th class="category" colspan="2">Indication</th>
          </tr>
          <tr>
            <td colspan="2">
              <input type="hidden" name="indication" value="{{$indication}}"/>
              <input type="hidden" name="type_indication" value="{{$type_indication}}"/>
              <input type="text" name="keywords_indication" class="autocomplete"
                onclick="emptyProduit(); emptyATC(); emptyComposant(); emptyIndication(); emptyCommentaire();"
                style="font-weight: bold; font-size: 1.3em; width: 300px;" value="{{$keywords_indication}}"/>
            </td>
          </tr>
          <tr>
            <th class="category" colspan="2">Commentaire</th>
          </tr>
          <tr>
            <td colspan="2">
              <input type="text" name="commentaire" value="{{$commentaire}}"
                onclick="emptyProduit(); emptyATC(); emptyComposant(); emptyIndication();"
                style="font-size: 1.3em; width: 317px;"/>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="button" class="search" id="search_button"
          onclick="this.form.start.value=0; this.form.onsubmit()">
          {{tr}}Search{{/tr}}
        </button>
      </td>
    </tr>
  </table>
</form>