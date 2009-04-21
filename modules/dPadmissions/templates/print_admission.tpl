{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="form" id="admission">
  <tr>
    <th class="title" colspan="2">
      <a href="#" onclick="window.print()">Récapitulatif admission</a>
    </th>
  </tr>

  <tr>
    <th>Praticien : </th>
    <td>Dr {{$admission->_ref_praticien->_view}}</td>
  </tr>
  
  {{assign var=patient value=$admission->_ref_patient}}
  <tr>
    <th class="category" colspan="2">Informations sur le patient</th>
  </tr>
  
  <tr>
    <th>Nom / Prénom :</th>
    <td>{{$patient->_view}}</td>
  </tr>

  <tr>
    <th>Date de naissance / Sexe : </th>
    <td>né(e) le {{mb_value object=$patient field="naissance"}} ({{$patient->_age}} ans) de sexe {{tr}}CPatient.sexe.{{$patient->sexe}}{{/tr}}</td>
  </tr>

  <tr>
    <th>Incapable majeur :</th>
    <td>{{tr}}CPatient.incapable_majeur.{{$patient->incapable_majeur}}{{/tr}}</td>
  </tr>

  <tr>
    <th>Téléphone: </th>
    <td>{{mb_value object=$patient field=tel}}</td>
  </tr>

  <tr>
    <th>Portable :</th>
    <td>{{mb_value object=$patient field=tel2}}</td>
  </tr>

  <tr>
    <th>Adresse :</th>
    <td>{{$patient->adresse}} &mdash; {{$patient->cp}} {{$patient->ville}}</td>
  </tr>

  <tr>
    <th>Numero d'assuré social :</th>
    <td>{{mb_value object=$patient field=matricule}}</td>
  </tr>

  <tr>
    <th>Remarques :</th>
    <td>{{$patient->rques|nl2br:php}}</td>
  </tr>

  {{if $patient->_ref_medecin_traitant->medecin_id}}
  <tr>
    <th>Médecin traitant :</th>
    <td>
      {{$patient->_ref_medecin_traitant->_view}}<br />
      {{$patient->_ref_medecin_traitant->adresse|nl2br}}<br />
      {{$patient->_ref_medecin_traitant->cp}} {{$patient->_ref_medecin_traitant->ville}}
    </td>
  </tr>
  {{/if}}

  {{if $patient->_ref_medecins_correspondants|@count}}
  <tr>
    <th>Médecins correspondants :</th>
    <td>
    {{foreach from=$patient->_ref_medecins_correspondants item=curr_corresp}}
      <div style="float: left; margin-right: 1.5em; margin-bottom: 0.2em;">
        {{$curr_corresp->_ref_medecin->_view}}<br />
        {{$curr_corresp->_ref_medecin->adresse|nl2br}}<br />
        {{$curr_corresp->_ref_medecin->cp}} {{$curr_corresp->_ref_medecin->ville}}
      </div>
    {{/foreach}}
    </td>
  </tr>
  {{/if}}

  <tr>
    <th class="category" colspan="2">Informations sur l'admission</th>
  </tr>
  
  <tr>
    <th>Date d'admission :</th>
    <td>{{$admission->entree_prevue|date_format:$dPconfig.datetime}}</td>
  </tr>

  <tr>
    <th>Durée d'hospitalisation :</th>
    <td>{{$admission->_duree_prevue}} jour(s)</td>
  </tr>

  <tr>
    <th>Admission en :</th>
    <td>{{tr}}CSejour.type.{{$admission->type}}{{/tr}}</td>
  </tr>

  <tr>
    <th>Chambre particulière :</th>
    <td>{{tr}}CSejour.chambre_seule.{{$admission->chambre_seule}}{{/tr}}</td>
  </tr>

  <tr>
    <th>Remarques :</th>
    <td>{{$admission->rques|nl2br}}</td>
  </tr>
  {{foreach from=$admission->_ref_operations item=curr_op}}
  <tr>
    <th class="category" colspan="2">
      Informations sur l'intervention du {{$curr_op->_datetime|date_format:"%d/%m/%Y"}}
    </th>
  </tr>

  <tr>
    <th>Chirurgien :</th>
    <td class="text">Dr {{$curr_op->_ref_chir->_view}}</td>
  </tr>

  <tr>
    <th>Bilan pré-opératoire :</th>
    <td class="text">{{$curr_op->examen}}</td>
  </tr>
  
  {{if $curr_op->libelle}}
  <tr>
    <th>Libellé :</th>
    <td class="text">{{$curr_op->libelle}}</td>
  </tr>
  {{/if}}

  {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
  <tr>
    <th>Acte médical :</th>
    <td class="text">{{$curr_code->libelleLong}} <em>({{$curr_code->code}})</em></td>
  </tr>
  {{/foreach}}

  <tr>
    <th>Côté :</th>
    <td>{{tr}}COperation.cote.{{$curr_op->cote}}{{/tr}}</td>
  </tr>

  <tr>
    <th>Remarques :</th>
    <td>{{$curr_op->rques}}</td>
  </tr>
  
  {{foreachelse}}
  <tr>
    <th class="category" colspan="2">Pas d'intervention</th>
  </tr>
  {{/foreach}}

</table>