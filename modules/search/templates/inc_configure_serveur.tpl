{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}
{{if $error == "mapping"}}
  <div class="small-error"> Un problème est survenu pour la création de l'index. Vérifiez l'état du service ElasticSearch</div>
{{/if}}
{{if $error == "index"}}
  <div class="small-error"> Un problème est survenu pour l'indexation des données. Vérifiez l'état du service ElasticSearch</div>
{{/if}}
<table class="main" id="table_main">
  <tr>
    <td>
      <form name="EditConfig-Search" action="?m={{$m}}&tab=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_configure" />
        <table class="form">
          <tr>
            <td id="optionnal" colspan="2">
              <input id="config_search" type="checkbox" onclick="
                                              Search.toggleElement($('first_indexing'));
                                              Search.toggleElement($('indexation'))"/>
              <label for="config_search"></label>
            </td>
          </tr>
          <tbody id="first_indexing" style="display:none;">
            <tr>
              <td  colspan="3">
                <input type="checkbox" name="names_types" id="CCompteRendu" value="CCompteRendu"/>
                <label for="CCompteRendu">Compte rendu</label>
              </td>
            </tr>
            <tr>
              <td>
                <input type="checkbox" name="names_types" id="CTransmissionMedicale" value="CTransmissionMedicale">
                <label for="CTransmissionMedicale"> Transmission Médicale</label>
              </td>
            </tr>
            <tr>
              <td>
                <input type="checkbox" name="names_types" id="CObservationMedicale" value="CObservationMedicale">
                <label for="CObservationMedicale"> Observation Médicale</label>
              </td>
            </tr>
            <tr>
              <td>
                <input type="checkbox" name="names_types" id="CConsultation" value="CConsultation">
                <label for="CConsultation"> Consultation de séjour</label>
              </td>
            </tr>
            <tr>
              <td>
                <input type="checkbox" name="names_types" id="CConsultAnesth" value="CConsultAnesth">
                <label for="CConsultAnesth"> Consultation anesthésique de séjour</label>
              </td>
            </tr>
            <tr>
              <td>
                <button class="new singleclick" onclick="Search.firstIndexing(null, true, document.body.select('input[name=names_types]:checked'))">Créer le schéma Nosql</button>
                <button class="new singleclick" onclick="Search.firstIndexing(true, null, document.body.select('input[name=names_types]:checked'));">Remplir table temporaire</button>
              </td>
            </tr>
          </tbody>
          <tr>
            <td  id="indexation" style="display:none;" colspan="2">
              <button class="new singleclick" onclick="Search.routineIndexing()">Indexer les données</button>
            </td>
          </tr>
          {{mb_include module=system template=inc_config_str var=client_host}}
          {{mb_include module=system template=inc_config_str var=client_port}}
          {{mb_include module=system template=inc_config_str var=index_name}}
          {{mb_include module=system template=inc_config_str var=interval_indexing}}
          <tr>
            <td class="button" colspan="2">
              <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>