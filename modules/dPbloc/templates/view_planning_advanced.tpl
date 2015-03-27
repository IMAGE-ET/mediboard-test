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

{{assign var=show_anesth_alerts value=$conf.dPbloc.CPlageOp.show_anesth_alerts}}


<h1 style="margin: auto; text-align: center;">
  <a href="#" onclick="window.print()">
    Planning du {{$filter->_datetime_min|date_format:$conf.date}} {{$filter->_datetime_min|date_format:$conf.time}}
    au {{$filter->_datetime_max|date_format:$conf.date}} {{$filter->_datetime_max|date_format:$conf.time}}
    -
    {{$numOp}} intervention(s)
    {{if $operations|@count && $_hors_plage}}
      (dont {{$operations|@count}} hors plage)
    {{/if}}
  </a>
</h1>

<br />

<table class="tbl">
  {{foreach from=$listDates key=curr_date item=listPlages}}
    {{foreach from=$listPlages key=curr_plage_id item=curr_plageop}}
      <tr class="clear">
        <td colspan="11">
          {{if $curr_plage_id == "hors_plage"}}
            <h2>
              <strong>Interventions {{if $_hors_plage}}hors plage{{/if}}</strong>
              du {{$curr_date|date_format:"%a %d/%m/%Y"}}
            </h2>
          {{else}}
            <h2>
              <strong>
                {{$curr_plageop->_ref_salle->nom}}
                -
                {{if $curr_plageop->chir_id}}
                  Dr {{$curr_plageop->_ref_chir}}
                {{else}}
                  {{$curr_plageop->_ref_spec}}
                {{/if}}
                {{if $curr_plageop->anesth_id}}
                  - Anesthesiste : Dr {{$curr_plageop->_ref_anesth}}
                {{/if}}
              </strong>
              <div style="font-size: 70%">
                {{$curr_plageop->date|date_format:"%a %d/%m/%Y"}}
                {{$curr_plageop->_ref_salle}}
                de {{$curr_plageop->debut|date_format:$conf.time}} à {{$curr_plageop->fin|date_format:$conf.time}}
                {{assign var="plageOp_id" value=$curr_plageop->_id}}
                <!-- Affichage du personnel prevu pour la plage operatoire -->
                {{foreach from=$affectations_plage.$plageOp_id key=type_affect item=_affectations}}
                  {{if $_affectations|@count}}
                    <strong>{{tr}}CPersonnel.emplacement.{{$type_affect}}{{/tr}} :</strong>
                    {{foreach from=$_affectations item=_personnel}}
                      {{$_personnel->_ref_personnel->_ref_user}};
                    {{/foreach}}
                  {{/if}}
                {{/foreach}}
              </div>
            </h2>
          {{/if}}
        </td>
      </tr>

      <tr>
        <th>H. entrée /<br/>Durée op.</th>
        <th class="text">Nom / Prénom</th>
        <th class="text">Age</th>
        <th>Sexe</th>
        <th class="text">Chirurgien /<br />Libellé</th>
        <th class="text">H. interv.</th>
        <th>Côté</th>
        <th>Anesthésie</th>
        <th>Rques</th>
        <th>Ordre de<br />passage</th>
        <th>Hospi. / Classe</th>
      </tr>

      {{if $curr_plage_id == "hors_plage"}}
        {{assign var=listOperations value=$curr_plageop}}
      {{else}}
        {{assign var=listOperations value=$curr_plageop->_ref_operations}}
      {{/if}}
      {{assign var=salle_id value=""}}

      {{foreach from=$listOperations item=_op}}
        {{assign var=sejour value=$_op->_ref_sejour}}
        {{assign var=patient value=$sejour->_ref_patient}}
        {{assign var=op_id value=$_op->_id}}
        {{if $salle_id != $_op->salle_id && $curr_plage_id == "hors_plage"}}
          {{assign var=salle_id value=$_op->salle_id}}
          <tr>
            <th class="section" colspan="11">
              {{$_op->_ref_salle}}
            </th>
          </tr>
        {{/if}}
        <tr>
          <td style="text-align: center;">
            {{$sejour->entree|date_format:$conf.time}}<br />
            {{$_op->temp_operation|date_format:$conf.time}}
          </td>
          <td class="text">
            <strong>
              {{mb_value object=$patient field=nom}} {{mb_value object=$patient field=prenom}}
            </strong>
          </td>
          <td>
            {{mb_value object=$patient field=_age}}<br />
            ({{mb_value object=$patient field=naissance}})
          </td>
          <td style="text-align: center;">
            {{$patient->sexe|strtoupper}}
          </td>
          <td class="text">
            {{if $_op->libelle}}
              <strong>{{$_op->libelle}}</strong>
              <br />
            {{/if}}
            {{foreach from=$_op->_ext_codes_ccam item=_code}}
              {{if !$_op->libelle}}
                {{if !$_code->_code7}}<strong>{{/if}}
                <em>{{$_code->code}}</em>
                {{if $filter->_ccam_libelle}}
                  : {{$_code->libelleLong|truncate:60:"...":false}}
                  <br/>
                {{else}}
                  ;
                {{/if}}
                {{if !$_code->_code7}}</strong>{{/if}}
              {{/if}}
            {{/foreach}}
            {{if $curr_plageop|is_array || $curr_plageop->spec_id}}
              Dr {{$_op->_ref_chir}}
              <br />
            {{/if}}
          </td>
          <td style="text-align: center;">
            {{mb_value object=$_op field=time_operation}}
          </td>
          <td style="text-align: center;">
            {{$_op->cote|truncate:1:""|capitalize}}
          </td>
          <td class="text">
            {{if $_op->type_anesth != null}}
              {{$_op->_lu_type_anesth}}
            {{/if}}
            {{if $_op->anesth_id}}
              <br /> {{$_op->_ref_anesth->_view}}
            {{/if}}
          </td>
          <td class="text">
            {{if $_op->exam_extempo}}
              <strong>{{mb_title object=$_op field=exam_extempo}}</strong>
              <br />
            {{/if}}
            {{assign var=consult_anesth value=$_op->_ref_consult_anesth}}
            {{mb_include module=bloc template=inc_rques_intub operation=$_op}}
          </td>
          <td style="text-align: center;">
            {{$ordre_passage.$op_id}}
          </td>
          <td>
            {{$sejour->type|truncate:1:""|capitalize}}
            {{if $sejour->type == "comp"}}
              - {{$sejour->_duree_prevue}}j
            {{/if}}
            {{if $_op->_liaisons_prestation}}
              <br />
              {{$_op->_liaisons_prestation}}
            {{/if}}
          </td>
        </tr>
      {{/foreach}}
      <tr class="clear">
        <td colspan="11">
          <hr />
        </td>
      </tr>
    {{/foreach}}
  {{/foreach}}
</table>