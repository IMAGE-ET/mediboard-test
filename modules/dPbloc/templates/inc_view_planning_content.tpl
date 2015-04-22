{{*
 * $Id$
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{foreach from=$listOperations item=curr_op}}
  {{if $salle_id != $curr_op->salle_id && $curr_plage_id == "hors_plage"}}
    {{assign var=salle_id value=$curr_op->salle_id}}
    <tr>
      <th class="section" colspan="{{$_materiel+$_extra+$_duree+$_coordonnees+13}}">
        {{$curr_op->_ref_salle->_view}}
      </th>
    </tr>
  {{/if}}
  <tr>
    {{if $show_duree_preop}}
      <td>{{mb_value object=$curr_op field=_heure_us}}</td>
    {{/if}}
    {{if $curr_op->annulee}}
      <td class="cancelled">ANNULEE</td>
    {{elseif $curr_op->rank || !$curr_op->plageop_id}}
      <td class="text">
        {{if $curr_plageop|is_array && $curr_op->salle_id}}
          {{$curr_op->_ref_salle->_view}} à
        {{/if}}
        {{$curr_op->time_operation|date_format:$conf.time}} <br/>({{$curr_op->temp_operation|date_format:$conf.time}})
      </td>
    {{else}}
      <td>NP</td>
    {{/if}}
    {{assign var=sejour value=$curr_op->_ref_sejour}}
    {{assign var=patient value=$sejour->_ref_patient}}
    {{assign var=suffixe value="_content"}}
    {{mb_include module=bloc template=inc_planning/$col1$suffixe}}
    {{mb_include module=bloc template=inc_planning/$col2$suffixe}}
    {{mb_include module=bloc template=inc_planning/$col3$suffixe}}
  </tr>
{{/foreach}}