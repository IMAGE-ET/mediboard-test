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

<script type="text/javascript">
function changePage (start) {
  $V(getForm('rechercheDossierClinique').start, start);
}
Main.add(function() {
  var form = getForm("rechercheDossierClinique");
  
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
      $V(input, selected.select(".libelle")[0].innerHTML);
    }
  } );
  form.onsubmit();
});
</script>

<form name="rechercheDossierClinique" method="get" action="?" onsubmit="return Url.update(this, 'search-results')">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="a" value="ajax_recherche_dossier_clinique" />
  <input type="hidden" name="start" value="0" onchange="this.form.onsubmit()" />
  <input type="hidden" name="object_class" value="COperation" />
  
  <table class="main layout">
    <tr>
      <td style="width: 40%;">
      
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
          
          <tr>
            <th>{{mb_label object=$traitement field=traitement}}</th>
            <td>{{mb_field object=$traitement field=traitement prop=str}}</td>
          </tr>
          
          <tr>
            <th colspan="2" class="title">{{tr}}CConsultation{{/tr}}</th>
          </tr>
          
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
          
          <tr>
            <th colspan="2" class="title">{{tr}}CSejour{{/tr}}</th>
          </tr>
          
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
          
          <tr>
            <th colspan="2" class="title">{{tr}}COperation{{/tr}}</th>
          </tr>
          
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
          
          <tr>
            <th colspan="2" class="title">{{tr}}CPrescription{{/tr}}</th>
          </tr>
          <tr>
            <th>{{mb_label object=$prescription field=type}}</th>
            <td>
              <label>
                <input type="radio" name="type_prescription" value="externe" checked/> Externe
              </label>
              <label>
                <input type="radio" name="type_prescription" value="pre_admission" /> Pré-admission
              </label>
              <label>
                <input type="radio" name="type_prescription" value="sejour" /> Séjour
              </label>
              <label>
                <input type="radio" name="type_prescription" value="sortie" /> Sortie
              </label>
            </td>
          </tr>
          <tr>
            <th>Produit</th>
            <td>
              <input type="hidden" name="code_cis" />
              <input type="hidden" name="code_ucd" />
              <input type="text" name="produit" value="&mdash; {{tr}}CPrescription.select_produit{{/tr}}" size="20"
                style="font-weight: bold; font-size: 1.3em; width: 300px;" class="autocomplete"
                onclick="$V(this, '')"/>
              <div style="display:none; width: 350px;" class="autocomplete" id="produit_auto_complete"></div>
            </td>
          </tr>
          
          <tr>
            <td colspan="2" class="button">
              <button type="submit" class="search" onclick="this.form.start.value=0">
                {{tr}}Search{{/tr}}
              </button>
            </td>
          </tr>
        </table>
        
      </td>
      <td id="search-results"></td>
    </tr>
  </table>
</form>