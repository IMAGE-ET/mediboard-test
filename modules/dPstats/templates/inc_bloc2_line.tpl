{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <td class="text">{{$_operation->_datetime|date_format:$conf.date}}</td>
  <td class="text">{{$_operation->_ref_salle_prevue}}</td>
  <td class="text">{{$_operation->_ref_salle_reelle}}</td>
  <td class="text">{{$_operation->_deb_plage|date_format:$conf.time}}</td>
  <td class="text">{{$_operation->_fin_plage|date_format:$conf.time}}</td>
  <td class="text">
    {{if $_operation->rank}}
      #{{$_operation->rank}} à {{$_operation->time_operation|date_format:$conf.time}}
    {{else}}
      Non validé
    {{/if}}
  </td>
  <td class="text">
    {{if $_operation->_rank_reel}}
      #{{$_operation->_rank_reel}} à {{$_operation->entree_salle|date_format:$conf.time}}
    {{else}}
      Non renseigné
    {{/if}}
  </td>
  <td class="text">{{$_operation->_ref_sejour->_ref_patient->_view}} ({{$_operation->_ref_sejour->_ref_patient->_age}})</td>
  <td class="text">{{tr}}CSejour.type.{{$_operation->_ref_sejour->type}}{{/tr}}</td>
  <td class="text">{{$_operation->_ref_sejour->entree_prevue|date_format:$conf.datetime}}</td>
  <td class="text">
    {{if $_operation->_ref_sejour->entree_reelle}}
      {{$_operation->_ref_sejour->entree_reelle|date_format:$conf.datetime}}
    {{else}}
      Non renseigné
    {{/if}}
  </td>
  <td class="text">Dr {{$_operation->_ref_chir->_view}}</td>
  <td class="text">
    {{if $_operation->_ref_anesth->_id}}
      Dr {{$_operation->_ref_anesth->_view}}
    {{/if}}
  </td>
  <td class="text">{{$_operation->libelle}}</td>
  <td class="text">{{$_operation->_ref_sejour->DP}}</td>
  <td class="text">{{$_operation->codes_ccam|replace:'|':' '}}</td>
  <td class="text">{{$_operation->_lu_type_anesth}}</td>
  <td class="text">{{$_operation->ASA}}</td>
  <td class="text">{{$_operation->_ref_workflow->date_creation|date_format:$conf.datetime}}</td>
  <td class="text">{{$_operation->entree_salle|date_format:$conf.time}}</td>
  <td class="text">{{$_operation->induction_debut|date_format:$conf.time}}</td>
  <td class="text">{{$_operation->induction_fin|date_format:$conf.time}}</td>
  <td class="text">{{$_operation->pose_garrot|date_format:$conf.time}}</td>
  <td class="text">{{$_operation->debut_op|date_format:$conf.time}}</td>
  <td class="text">{{$_operation->fin_op|date_format:$conf.time}}</td>
  <td class="text">{{$_operation->retrait_garrot|date_format:$conf.time}}</td>
  <td class="text">{{$_operation->sortie_salle|date_format:$conf.time}}</td>
  <td class="text">{{$_operation->_pat_next|date_format:$conf.time}}</td>
  <td class="text">{{$_operation->entree_reveil|date_format:$conf.time}}</td>
  <td class="text">{{$_operation->sortie_reveil_possible|date_format:$conf.time}}</td>
</tr>