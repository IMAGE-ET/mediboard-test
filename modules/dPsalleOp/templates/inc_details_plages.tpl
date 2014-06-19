{{mb_default var=redirect_tab value=0}}

<!-- Plages -->
{{foreach from=$salle->_ref_plages item=_plage}}
  <hr />

  <form name="anesth{{$_plage->_id}}" action="?" method="post" class="{{$_plage->_spec}}">
    <input type="hidden" name="m" value="dPbloc" />
    <input type="hidden" name="otherm" value="{{$m}}" />
    <input type="hidden" name="dosql" value="do_plagesop_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="_repeat" value="1" />
    <input type="hidden" name="plageop_id" value="{{$_plage->_id}}" />
    <input type="hidden" name="chir_id" value="{{$_plage->chir_id}}" />
    <input type="hidden" name="spec_id" value="{{$_plage->spec_id}}" />

    <table class="form">
      <tr>
        <th class="category text" colspan="2">
          {{mb_include module=system template=inc_object_notes object=$_plage}}
          <a onclick="EditPlanning.order('{{$_plage->_id}}');" href="#" title="Agencer les interventions">
            {{if $_plage->chir_id}}
              Chir : Dr {{$_plage->_ref_chir}}
            {{else}}
              {{$_plage->_ref_spec}}
            {{/if}}
            <br />
            {{$_plage->debut|date_format:$conf.time}} à {{$_plage->fin|date_format:$conf.time}}
          </a>
        </th>
      </tr>

      {{if $vueReduite}}
        <tr>
          {{if $_plage->anesth_id}}
          <th class="category" colspan="2">
            Anesth : Dr {{$_plage->_ref_anesth}}
          </th>
          {{/if}}
        </tr>
        {{assign var=affectations value=$_plage->_ref_affectations_personnel}}
        {{if $affectations|@is_array}}
          {{foreach from=$affectations key=type item=list_aff}}
            {{if $list_aff|@count}}
              <tr>
                <td>
                  <strong>
                    {{tr}}CPersonnel.emplacement.{{$type}}{{/tr}} :
                  </strong>
                  <div class="compact">
                    <ul>
                    {{foreach from=$list_aff item=_affectation}}
                      <li>{{$_affectation->_ref_personnel->_ref_user}}</li>
                    {{/foreach}}
                    </ul>
                  </div>
                </td>
              </tr>
            {{/if}}
          {{/foreach}}
        {{/if}}
      {{else}}
        <tr>
          <th><label for="anesth_id" title="Anesthésiste associé à la plage d'opération">Anesthésiste</label></th>
          <td>
            <select name="anesth_id" onchange="this.form.submit()">
              <option value="">&mdash; Choisir un anesthésiste</option>
              {{foreach from=$listAnesths item=_anesth}}
                <option value="{{$_anesth->user_id}}" {{if $_plage->anesth_id == $_anesth->user_id}} selected="selected" {{/if}}>
                  {{$_anesth}}
                </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        {{if $conf.dPsalleOp.COperation.modif_actes == "button" && !$_plage->actes_locked}}
          <tr>
            <td class="button" colspan="2">
              <input type="hidden" name="actes_locked" value="{{$_plage->actes_locked}}" />
              <button class="submit" type="button" onclick="$V(this.form.actes_locked, 1); if(confirmeCloture()) {this.form.submit()}">Cloturer le codage</button>
            </td>
          </tr>
        {{elseif $_plage->actes_locked}}
          <tr>
            <th class="category" colspan="2">Codage cloturé</th>
          </tr>
        {{/if}}
      {{/if}}
    </table>
  </form>

  <table class="tbl">
    {{if $_plage->_ref_operations}}
      {{mb_include module=salleOp template=inc_liste_operations urgence=0 operations=$_plage->_ref_operations}}
    {{else}}
      <tr><td class="empty" colspan="10">Aucune intervention placée</td></tr>
    {{/if}}

    {{if $_plage->_unordered_operations}}
      <tr>
        <th class="section" colspan="10">Non placées</th>
      </tr>
      {{mb_include module=salleOp template=inc_liste_operations urgence=0 operations=$_plage->_unordered_operations}}
    {{/if}}
  </table>
  {{foreachelse}}
  <table class="tbl">
    <tr>
      <td class="empty">{{tr}}CPlageOp.none{{/tr}}</td>
    </tr>
  </table>
{{/foreach}}

<!-- Déplacées -->
{{if $salle->_ref_deplacees|@count}}
  <hr />
  <table class="tbl">
    <tr>
      <th class="section">
        Déplacées
      </th>
    </tr>
  </table>
  <table class="tbl">
    {{mb_include module=salleOp template=inc_liste_operations urgence=1 operations=$salle->_ref_deplacees}}
  </table>
{{/if}}

<!-- Urgences -->
{{if $salle->_ref_urgences|@count}}
  <hr />
  <table class="tbl">
    <tr>
      <th class="section">
        Hors plage
      </th>
    </tr>
  </table>
  <table class="tbl">
    {{mb_include module=salleOp template=inc_liste_operations urgence=1 operations=$salle->_ref_urgences}}
  </table>
{{/if}}