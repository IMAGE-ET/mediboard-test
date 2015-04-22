{{assign var=show_duree_preop value=$conf.dPplanningOp.COperation.show_duree_preop}}
{{assign var="col1" value=$conf.dPbloc.CPlageOp.planning.col1}}
{{assign var="col2" value=$conf.dPbloc.CPlageOp.planning.col2}}
{{assign var="col3" value=$conf.dPbloc.CPlageOp.planning.col3}}

<table class="tbl">
  {{mb_include module=bloc template=inc_view_planning_header}}

  {{foreach from=$listDates key=curr_date item=listPlages}}
    {{foreach from=$listPlages key=curr_plage_id item=curr_plageop}}
      <tr class="clear">
        <td colspan="{{$_materiel+$_extra+$_duree+$_coordonnees+12}}" class="text">
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
                  Dr {{$curr_plageop->_ref_chir->_view}}
                {{else}}
                  {{$curr_plageop->_ref_spec->_view}}
                {{/if}}
                {{if $curr_plageop->anesth_id}}
                  - Anesthesiste : Dr {{$curr_plageop->_ref_anesth->_view}}
                {{/if}}
              </strong>
              <div style="font-size: 70%">
                {{$curr_plageop->date|date_format:"%a %d/%m/%Y"}}
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

      {{mb_include module=bloc template=inc_view_planning_title}}

      {{if $curr_plage_id == "hors_plage"}}
        {{assign var=listOperations value=$curr_plageop}}
      {{else}}
        {{assign var=listOperations value=$curr_plageop->_ref_operations}}
      {{/if}}
      {{assign var=salle_id value=""}}

      {{mb_include module=bloc template=inc_view_planning_content}}
    {{/foreach}}
  {{/foreach}}
</table>