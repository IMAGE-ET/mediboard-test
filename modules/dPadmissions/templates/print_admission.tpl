{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
  
{{assign var=patient value=$admission->_ref_patient}}

<table class="form" id="admission">
  <tr>
    <th class="title" colspan="4">
      <a href="#" onclick="window.print()">Admission de {{$patient->_view}}
      <br />par le Dr {{$admission->_ref_praticien->_view}}</a>
    </th>
  </tr>
  <tr>
    <th class="category" colspan="4">Patient [{{$patient->_IPP}}]</th>
  </tr>

  <tr>
    <td colspan="4" style="text-align: center;">né(e) le {{mb_value object=$patient field="naissance"}} ({{$patient->_age}} ans) de sexe {{tr}}CPatient.sexe.{{$patient->sexe}}{{/tr}}</td>
  </tr>

  <tr>
    <th>Téléphone: </th>
    <td>{{mb_value object=$patient field=tel}}</td>
    <th>Portable :</th>
    <td>{{mb_value object=$patient field=tel2}}</td>
  </tr>

  <tr>
    <th>Adresse :</th>
    <td>{{$patient->adresse}} &mdash; {{$patient->cp}} {{$patient->ville}}</td>
    <th>Numero d'assuré social :</th>
    <td>{{mb_value object=$patient field=matricule}}</td>
  </tr>

  <tr>
    <th>Remarques :</th>
    <td>{{$patient->rques|nl2br:php}}</td>
    <th>Incapable majeur :</th>
    <td>{{tr}}CPatient.incapable_majeur.{{$patient->incapable_majeur}}{{/tr}}</td>
  </tr>

  <tr>
    {{if $patient->_ref_medecin_traitant->medecin_id}}
    <th>Médecin traitant :</th>
    <td>
      {{$patient->_ref_medecin_traitant->_view}}<br />
      {{$patient->_ref_medecin_traitant->adresse|nl2br}}<br />
      {{$patient->_ref_medecin_traitant->cp}} {{$patient->_ref_medecin_traitant->ville}}
    </td>
    {{/if}}

    {{if $patient->_ref_medecins_correspondants|@count}}
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
    {{/if}}
  </tr>

  <tr>
    <th class="category" colspan="4">Admission [{{$admission->_num_dossier}}]</th>
  </tr>
  
  <tr>
    <th>Date d'admission :</th>
    <td>{{$admission->entree_prevue|date_format:$dPconfig.datetime}}</td>
    <th>Intervention :</th>
    <td>
      {{foreach from=$admission->_ref_operations item=curr_op}}
        le {{$curr_op->_datetime|date_format:"%d/%m/%Y"}}
        <br />
      {{foreachelse}}
        Pas d'intervention
      {{/foreach}}
    </td>
  </tr>

  <tr>
    <th>Admission en :</th>
    <td>{{tr}}CSejour.type.{{$admission->type}}{{/tr}}</td>
    <th>Durée d'hospitalisation :</th>
    <td>{{$admission->_duree_prevue}} jour(s)</td>
  </tr>

  <tr>
    <th>Chambre particulière :</th>
    <td>{{tr}}CSejour.chambre_seule.{{$admission->chambre_seule}}{{/tr}}</td>
    <th>Remarques :</th>
    <td>{{$admission->rques|nl2br}}</td>
  </tr>
  
</table>