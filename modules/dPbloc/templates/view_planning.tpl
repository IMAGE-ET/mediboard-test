{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=show_duree_preop value=$conf.dPplanningOp.COperation.show_duree_preop}}

<table class="tbl">
  <tr class="clear">
    <th colspan="11">
      <h1>
        <a href="#" onclick="window.print()">
          Planning du {{$filter->_date_min|date_format:$conf.date}}
          {{if $filter->_date_min != $filter->_date_max}}
          au {{$filter->_date_max|date_format:$conf.date}}
          {{/if}}
          -
          {{$numOp}} intervention(s)
          {{if $operations|@count}}(dont {{$operations|@count}} hors plage){{/if}}
        </a>
      </h1>
    </th>
  </tr>
  {{foreach from=$listDates key=curr_date item=listPlages}}
  {{foreach from=$listPlages key=curr_plage_id item=curr_plageop}}
  <tr class="clear">
    <td colspan="11" class="text">
      {{if $curr_plage_id == "hors_plage"}}
        <h2>
          <strong>Interventions hors plage</strong>
          du {{$curr_date|date_format:"%d/%m/%Y"}}
        </h2>
      {{else}}
        <h2>
          <strong>
            {{$curr_plageop->_ref_salle->nom}}
            -
            {{if $curr_plageop->chir_id}}
              Dr {{$curr_plageop->_ref_chir->_view}}
            {{else}}
              {{$curr_plageop->_ref_spec->_view}}
            {{/if}}
            {{if $curr_plageop->anesth_id}}
              - Anesthesiste : Dr {{$curr_plageop->_ref_anesth->_view}}
            {{/if}}
          </strong>
          <div style="font-size: 70%">
            {{$curr_plageop->date|date_format:"%d/%m/%Y"}}
            {{$curr_plageop->_ref_salle->_view}}
            de {{$curr_plageop->debut|date_format:$conf.time}} à {{$curr_plageop->fin|date_format:$conf.time}}
            {{assign var="plageOp_id" value=$curr_plageop->_id}}
            <!-- Affichage du personnel prevu pour la plage operatoire -->
            {{foreach from=$affectations_plage.$plageOp_id key=type_affect item=_affectations}}
              {{if $_affectations|@count}}
                <strong>{{tr}}CPersonnel.emplacement.{{$type_affect}}{{/tr}} :</strong>
                {{foreach from=$_affectations item=_personnel}}
                  {{$_personnel->_ref_personnel->_ref_user->_view}};
                {{/foreach}}
              {{/if}}
            {{/foreach}}
          </div>
        </h2>
      {{/if}}
    </td>
  </tr>
  <tr>
    <th class="title" {{if $show_duree_preop}}colspan="2"{{/if}}></th>
    {{assign var="col1" value=$conf.dPbloc.CPlageOp.planning.col1}}
    {{assign var="col2" value=$conf.dPbloc.CPlageOp.planning.col2}}
    {{assign var="col3" value=$conf.dPbloc.CPlageOp.planning.col3}}
 
    {{assign var=suffixe value="_title.tpl"}}
    {{include file=inc_planning/$col1$suffixe}}
    {{include file=inc_planning/$col2$suffixe}}
    {{include file=inc_planning/$col3$suffixe}}
  </tr>
  <tr>
    {{assign var=suffixe value="_header.tpl"}}
    {{if $show_duree_preop}}<th>Heure US</th>{{/if}}
    <th>Heure</th>
    {{include file=inc_planning/$col1$suffixe}}
    {{include file=inc_planning/$col2$suffixe}}
    {{include file=inc_planning/$col3$suffixe}}
  </tr>
  {{if $curr_plage_id == "hors_plage"}}
    {{assign var=listOperations value=$curr_plageop}}
  {{else}}
    {{assign var=listOperations value=$curr_plageop->_ref_operations}}
  {{/if}}
  {{foreach from=$listOperations item=curr_op}}
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
        {{$curr_op->time_operation|date_format:$conf.time}}
      </td>
    {{else}}
      <td>NP</td>
    {{/if}}
    {{assign var=sejour value=$curr_op->_ref_sejour}}
    {{assign var=patient value=$sejour->_ref_patient}}
    {{assign var=suffixe value="_content.tpl"}}
    {{include file=inc_planning/$col1$suffixe}}
    {{include file=inc_planning/$col2$suffixe}}
    {{include file=inc_planning/$col3$suffixe}}
  </tr>
  {{/foreach}}
  {{/foreach}}
  {{/foreach}}
</table>