{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{main}}
  getForm('rechercheDossierClinique').onsubmit();
{{/main}}

<form name="rechercheDossierClinique" method="get" action="?" onsubmit="return Url.update(this, 'search-results')">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="a" value="ajax_recherche_dossier_clinique" />
  
  <table class="main layout">
    <tr>
      <td style="width: 40%;">
      
        <table class="main form">
          <tr>
            <th colspan="2" class="title">{{tr}}CPatient{{/tr}}</th>
          </tr>
          
          <tr>
            <th>{{mb_label object=$patient field=sexe}}</th>
            <td>{{mb_field object=$patient field=sexe emptyLabel="Tous"}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$patient field=_age}}
            agé a l'epoque</th>
            <td>
              entre
              {{mb_field object=$patient field=_age_min increment=true form=rechercheDossierClinique size=2}}
              et
              {{mb_field object=$patient field=_age_max increment=true form=rechercheDossierClinique size=2}}
              ans
            </td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$patient field=medecin_traitant}}</th>
            <td>
              <script type="text/javascript">
                Main.add(function () {
                  var formTraitant = getForm("rechercheDossierClinique");
                  urlTraitant = new Url("dPpatients", "httpreq_do_medecins_autocomplete");
                  urlTraitant.autoComplete(formTraitant._view, null, {
                    minChars: 2,
                    updateElement : function(element) {
                      $V(formTraitant.medecin_traitant, element.id.split('-')[1]);
                      $V(formTraitant._view, element.down(".view").innerHTML.stripTags());
                    }
                  });
                });
              </script>
              <input type="text" name="_view" value="{{$patient->_ref_medecin_traitant}}" />
              {{mb_field object=$patient field=medecin_traitant hidden=true}}
              <button type="button" class="cancel notext" onclick="this.form.medecin_traitant.value='';this.form._view.value='';"></button>
            </td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$dossier_medical field=codes_cim}}</th>
            <td>
              {{mb_field object=$dossier_medical field=codes_cim prop=str size=12}} <br />
              (codes complets ou partiels séparés par des virgules)
            </td>
          </tr>
          
          <tr>
            <th colspan="2" class="title">{{tr}}CSejour{{/tr}}</th>
          </tr>
          
          <tr>
            <th>{{mb_label object=$sejour field=type}}</th>
            <td>{{mb_field object=$sejour field=type emptyLabel="Tous"}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$sejour field=convalescence}}</th>
            <td>{{mb_field object=$sejour field=convalescence prop=str}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$sejour field=rques}}</th>
            <td>{{mb_field object=$sejour field=rques prop=str}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$sejour field=entree_reelle}}</th>
            <td>{{mb_field object=$sejour field=entree_reelle register=true form=rechercheDossierClinique}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$sejour field=sortie_reelle}}</th>
            <td>{{mb_field object=$sejour field=sortie_reelle register=true form=rechercheDossierClinique}}</td>
          </tr>
          
          <tr>
            <th colspan="2" class="title">{{tr}}COperation{{/tr}}</th>
          </tr>
          
          <tr>
            <th>{{mb_label object=$interv field=materiel}}</th>
            <td>{{mb_field object=$interv field=materiel prop=str}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$interv field=examen}}</th>
            <td>{{mb_field object=$interv field=examen prop=str}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$interv field=libelle}}</th>
            <td>{{mb_field object=$interv field=libelle prop=str}}</td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$interv field=codes_ccam}}</th>
            <td>
              {{mb_field object=$interv field=codes_ccam size=12}} <br />
              (codes complets ou partiels séparés par des virgules)
            </td>
          </tr>
          
          <tr>
            <td colspan="2" class="button">
              <button type="submit" class="search">
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