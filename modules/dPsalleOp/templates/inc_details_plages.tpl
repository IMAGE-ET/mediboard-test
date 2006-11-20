      {{foreach from=$plages item=curr_plage}}
      <hr />
      
      <form name="anesth{{$curr_plage->plageop_id}}" action="index.php" method="post">

      <input type="hidden" name="m" value="dPbloc" />
      <input type="hidden" name="otherm" value="{{$m}}" />
      <input type="hidden" name="dosql" value="do_plagesop_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="_repeat" value="1" />
      <input type="hidden" name="plageop_id" value="{{$curr_plage->plageop_id}}" />
      <input type="hidden" name="chir_id" value="{{$curr_plage->chir_id}}" />

      <table class="form">
        <tr>
          <th class="category{{if $vueReduite}} text{{/if}}" colspan="2">
            <a href="?m=dPbloc&amp;tab=vw_edit_interventions&amp;plageop_id={{$curr_plage->plageop_id}}" title="Administrer la plage">
              Chir : Dr. {{$curr_plage->_ref_chir->_view}}
              {{if $vueReduite}}
                <br />
              {{else}}
                -
              {{/if}}
              {{$curr_plage->debut|date_format:"%Hh%M"}} à {{$curr_plage->fin|date_format:"%Hh%M"}}
            </a>
          </th>
        </tr>
        
        <tr>
          {{if $vueReduite}}
          <th class="category" colspan="2">
            {{if $curr_plage->anesth_id}}
              Anesth : Dr. {{$curr_plage->_ref_anesth->_view}}
            {{else}}
              -
            {{/if}}
          </th>
          {{else}}
          <th><label for="anesth_id" title="Anesthésiste associé à la plage d'opération">Anesthésiste</label></th>
          <td>
            <select name="anesth_id" onchange="submit()">
              <option value="">&mdash; Choisir un anesthésiste</option>
              {{foreach from=$listAnesths item=curr_anesth}}
              <option value="{{$curr_anesth->user_id}}" {{if $curr_plage->anesth_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
                {{$curr_anesth->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
          {{/if}}
        </tr>
        
      </table>

      </form>

       <table class="tbl">
        <tr>
          <th>Heure</th>
          <th>Patient</th>
          {{if !$vueReduite}}
          <th>Intervention</th>
          <th>Coté</th>
          <th>Durée</th>
          {{/if}}
        </tr>
        {{foreach from=$curr_plage->_ref_operations item=curr_operation}}
        <tr>
          {{if $curr_operation->entree_salle && $curr_operation->sortie_salle}}
          <td style="background-image:url(modules/dPsalleOp/images/ray.gif); background-repeat:repeat;">
          {{elseif $curr_operation->entree_salle}}
          <td style="background-color:#cfc">
          {{elseif $curr_operation->sortie_salle}}
          <td style="background-color:#fcc">
          {{elseif $curr_operation->entree_bloc}}
          <td style="background-color:#ffa">
          {{else}}
          <td>
          {{/if}}
            <a href="index.php?m=dPsalleOp&amp;tab=vw_operations&amp;salle={{$salle}}&amp;op={{$curr_operation->operation_id}}" title="Coder l'intervention">
              {{$curr_operation->time_operation|date_format:"%Hh%M"}}
            </a>
          </td>
          <td {{if $vueReduite}}class="text"{{/if}}>
            {{if $vueReduite}}
            <button class="print notext" onclick="printFeuilleBloc({{$curr_operation->operation_id}})" style="float:right;"></button>
            {{/if}}
            <a href="index.php?m=dPsalleOp&amp;tab=vw_operations&amp;salle={{$salle}}&amp;op={{$curr_operation->operation_id}}" title="Coder l'intervention">
              {{$curr_operation->_ref_sejour->_ref_patient->_view}}
            </a>
          </td>
          {{if !$vueReduite}}
          <td>
            <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_operation->operation_id}}" title="Modifier l'intervention">
              {{foreach from=$curr_operation->_ext_codes_ccam item=curr_code}}
              {{$curr_code->code}}<br />
              {{/foreach}}
            </a>
          </td>
          <td>{{tr}}COperation.cote.{{$curr_operation->cote}}{{/tr}}</td>
          <td>{{$curr_operation->temp_operation|date_format:"%Hh%M"}}</td>
          {{/if}}
        </tr>
        {{/foreach}}
      </table>
      {{/foreach}}

      {{if $urgences|@count}}
      
      <hr />

      <table class="form">
        <tr>
          <th class="category" colspan="2">
            Urgences
          </th>
        </tr>        
      </table>
      <table class="tbl">
        <tr>
          <th>praticien</th>
          <th>Patient</th>
          {{if !$vueReduite}}
          <th>Intervention</th>
          <th>Coté</th>
          {{/if}}
        </tr>
        {{foreach from=$urgences item=curr_operation}}
        <tr>
          {{if $curr_operation->entree_salle && $curr_operation->sortie_salle}}
          <td class="text" style="background-image:url(modules/dPsalleOp/images/ray.gif); background-repeat:repeat;">
          {{elseif $curr_operation->entree_salle}}
          <td class="text" style="background-color:#cfc">
          {{elseif $curr_operation->sortie_salle}}
          <td class="text" style="background-color:#fcc">
          {{else}}
          <td class="text">
          {{/if}}
            <a href="index.php?m=dPsalleOp&amp;tab=vw_operations&amp;op={{$curr_operation->operation_id}}" title="Coder l'intervention">
              {{$curr_operation->_ref_chir->_view}}
            </a>
          </td>
          <td>
            <a href="index.php?m=dPsalleOp&amp;tab=vw_operations&amp;op={{$curr_operation->operation_id}}" title="Coder l'intervention">
              {{$curr_operation->_ref_sejour->_ref_patient->_view}}
            </a>
          </td>
          {{if !$vueReduite}}
          <td>
            <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_operation->operation_id}}" title="Modifier l'intervention">
              {{foreach from=$curr_operation->_ext_codes_ccam item=curr_code}}
              {{$curr_code->code}}<br />
              {{/foreach}}
            </a>
          </td>
          <td>{{tr}}COperation.cote.{{$curr_operation->cote}}{{/tr}}</td>
          {{/if}}
        </tr>
        {{/foreach}}
      </table>
      {{/if}}