{{*
  * Find candidates
  *
  * @category sip
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  * @version  SVN: $Id:$
  * @link     http://www.mediboard.org
*}}

{{mb_script module="sip" script="SIP" ajax=true}}

<table class="main">
  <tr>
    <td class="halfPane">
      <form name="find" action="?m=sip&a=ajax_find_candidates" method="post" onsubmit="return SIP.findCandidates(this)" class="prepared">
        <input type="hidden" name="pointer"  value="{{$pointer}}" />
        <input type="hidden" name="continue" value="" />

        <table class="form">
          <tr>
            <th class="category" colspan="4">Recherche d'un dossier patient sur le SIP</th>
          </tr>

          <tr>
            <td colspan="4">
              <fieldset>
                <legend>Informations démographiques</legend>

                <table class="form">
                  <tr>
                    <th><label for="nom" title="Nom du patient à rechercher, au moins les premières lettres">Nom</label></th>
                    <td><input tabindex="1" type="text" name="nom" value="{{$nom|stripslashes}}" /></td>

                    <th><label for="cp" title="Code postal du patient à rechercher">Code postal</label></th>
                    <td><input tabindex="4" type="text" name="cp" value="{{$cp|stripslashes}}" /></td>
                  </tr>

                  <tr>
                    <th><label for="prenom" title="Prénom du patient à rechercher, au moins les premières lettres">Prénom</label></th>
                    <td><input tabindex="2" type="text" name="prenom" value="{{$prenom|stripslashes}}" /></td>

                    <th><label for="ville" title="Ville du patient à rechercher">Ville</label></th>
                    <td><input tabindex="5" type="text" name="ville" value="{{$ville|stripslashes}}" /></td>
                  </tr>

                  <tr>
                    <th><label for="nom_jeune_fille" title="Nom de naissance">Nom de naissance</label></th>
                    <td><input tabindex="3" type="text" name="nom_jeune_fille" value="{{$nom_jeune_fille|stripslashes}}" /></td>

                    <th> <label for="Date_Day" title="Date de naissance du patient à rechercher"> Date de naissance </label> </th>
                    <td> {{mb_include module=patients template=inc_select_date date="--" tabindex=6}} </td>
                  </tr>

                  <tr>
                    <th><label for="sexe" title="Sexe">Sexe</label></th>
                    <td>
                      <select name="sexe" tabindex="7">
                        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                        <option value="m" {{if $sexe == "m"}}selected{{/if}}>
                        {{tr}}CPatient.sexe.m{{/tr}}
                        </option>
                        <option value="f" {{if $sexe == "f"}}selected{{/if}}>
                        {{tr}}CPatient.sexe.f{{/tr}}
                        </option>
                      </select>
                    </td>

                    <td colspan="2"></td>
                  </tr>

                  <tr>
                    <th>Identifiant du patient</th>
                    <td colspan="4">
                      <input tabindex="8" type="text" name="person_id_number" value="" size="15" placeholder="ID"/> ^^^
                      <input tabindex="9" type="text" name="person_namespace_id" value="" size="30" placeholder="espace de noms du domaine"/> &
                      <input tabindex="10" type="text" name="person_universal_id" value="" size="30" placeholder="ID universel du domaine"/> &
                      <input tabindex="11" type="text" name="person_universal_id_type" value="" size="35" placeholder="Type de l'ID universel du domaine"/>
                      <input tabindex="12" type="text" name="person_identifier_type_code" value="" size="15" placeholder="Type de code"/>
                    </td>
                  </tr>
                </table>
              </fieldset>
            </td>
          </tr>

          <tr>
            <td colspan="4">
              <fieldset>
                <legend>Informations complémentaires</legend>

                <table class="form">
                  <tr>
                    <th>Quels domaines retourner</th>
                    <td colspan="4">
                      <input tabindex="13" type="text" name="domains_returned_namespace_id" value="" size="30" placeholder="espace de noms du domaine"/> &
                      <input tabindex="14" type="text" name="domains_returned_universal_id" value="" size="30" placeholder="ID universel du domaine"/> &
                      <input tabindex="15" type="text" name="domains_returned_universal_id_type" value="" size="35" placeholder="Type de l'ID universel du domaine"/>
                    </td>
                  </tr>

                  <tr>
                    <th><label for="quantity_limited_request" title="Limite des résultats">Limite des résultats recherchés</label></th>
                    <td><input tabindex="16" type="text" name="quantity_limited_request" value="{{$quantity_limited_request}}" /></td>

                    <td colspan="2"></td>
                  </tr>
                </table>

              </fieldset>
            </td>
          </tr>

          <tr>
            <td class="button" colspan="4">
              <button class="search singleclick"onclick="$V(this.form.continue, 0)"> {{tr}}Search{{/tr}} </button>
              <button class="tick singleclick" name="finder" {{if !$pointer}}disabled{{/if}} onclick="$V(this.form.continue, 1)"> {{tr}}Continue{{/tr}} </button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  <tr>
    <td id="find_candidates">

    </td>
  </tr>
</table>