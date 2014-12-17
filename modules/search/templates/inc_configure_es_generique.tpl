{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<table class="form">
  <tr>
    <th class="category" colspan="3">Catégories de documents
      <label><input type="checkbox" name="check_all" id="check_all" onclick="Search.checkAllCheckboxes(this, 'names_types')"/></label>
    </th>
  </tr>
  <tr>
    <td>
      <input type="checkbox" name="names_types" id="CCompteRendu" value="CCompteRendu"/>
      <label for="CCompteRendu">Compte rendu</label>
    </td>
    <td>
      <input type="checkbox" name="names_types" id="CTransmissionMedicale" value="CTransmissionMedicale">
      <label for="CTransmissionMedicale"> Transmission Médicale</label>
    </td>
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
    <td>
      <input type="checkbox" name="names_types" id="CConsultAnesth" value="CConsultAnesth">
      <label for="CConsultAnesth"> Consultation anesthésique de séjour</label>
    </td>
    <td>
      <input type="checkbox" name="names_types" id="CFile" value="CFile">
      <label for="CFile"> Fichiers</label>
    </td>
  </tr>
  <tr>
    <td>
      <input type="checkbox" name="names_types" id="CPrescriptionLineMedicament" value="CPrescriptionLineMedicament">
      <label for="CPrescriptionLineMedicament">Prescriptions Médicales</label>
    </td>
    <td>
      <input type="checkbox" name="names_types" id="CPrescriptionLineMix" value="CPrescriptionLineMix">
      <label for="CPrescriptionLineMix">Prescriptions Mixtes</label>
    </td>
    <td>
      <input type="checkbox" name="names_types" id="CExObject" value="CExObject">
      <label for="CExObject"> Formulaires</label>
    </td>
  </tr>
  <tr>
  <tr>
    <th class="category" colspan="3"> 1 - Actions sur l'index</th>
  </tr>
  <tr>
    <td class="button">
      <span class="text"> Nom de l'index : {{$conf.db.std.dbname}}</span>
    </td>
  </tr>
  <tr>
    <td>
      <button class="new singleclick" type="submit" onclick="Search.firstIndexing(null, true, $V(this.form.elements.names_types))">Créer le schéma Nosql</button>
    </td>
    <td>
      <button class="change singleclick" type="submit" onclick="Search.updateIndex($V(this.form.elements.names_types))">{{tr}}Update{{/tr}} le schéma Nosql</button>
    </td>
  </tr>
  <tr>
    <th class="category" colspan="3"> 2 - Actions sur la table buffer</th>
  </tr>
  <tr>
    <td>
      <button class="new singleclick" type="submit" onclick="Search.firstIndexing(true, null, $V(this.form.elements.names_types));">Remplir table temporaire</button>
    </td>
  </tr>
  <tr>
    <th class="category" colspan="3"> 3 - Tests Indexation</th>
  </tr>
  <tr>
    <td id="indexation">
      <button class="new singleclick" onclick="Search.routineIndexing()">Indexer les données</button>
    </td>
  </tr>
</table>