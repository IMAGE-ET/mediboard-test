      <table class="form">
        <tr>
          <th class="category">Intervention</th>
          <th class="category">Position</th>
        </tr>
        <tr>
          {{if $consult_anesth->consultation_anesth_id}}
          <td class="text">
            <form name="editOpFrm" action="?m=dPcabinet" method="post">

            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="operation_id" value="{{$consult_anesth->_ref_operation->operation_id}}" />
            <label for="type_anesth" title="Type d'anesthésie pour l'intervention">Type d'anesthésie</label>
            <select name="type_anesth" onchange="submitFormAjax(this.form, 'systemMsg')">
              <option value="">&mdash; Choisir un type d'anesthésie</option>
              {{html_options options=$anesth selected=$consult_anesth->_ref_operation->type_anesth}}
            </select>

            </form>
            <br />
            Intervention le <strong>{{$consult_anesth->_ref_operation->_datetime|date_format:"%a %d %b %Y"}}</strong>
            par le <strong>Dr. {{$consult_anesth->_ref_operation->_ref_chir->_view}}</strong> (coté {{$consult->_ref_consult_anesth->_ref_operation->cote}})<br />
            <ul>
              {{foreach from=$consult_anesth->_ref_operation->_ext_codes_ccam item=curr_code}}
              <li><em>{{$curr_code->libelleLong}}</em> ({{$curr_code->code}})</li>
              {{/foreach}}
            </ul>
          </td>
          
          <td class="text" width="200">
            <form name="EditPosFrm" action="?m=dPcabinet" method="post">
            <input type="hidden" name="m" value="{{$m}}" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
            <input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->consultation_anesth_id}}" />
            
            <table class="text">
              {{section name=rows loop=$consult->_ref_consult_anesth->_enums.position}}
              {{cycle name=debut values="<tr><td class='text'>,<td class='text'>"}}
              <input type="radio" name="position" onClick="submitFormAjax(this.form, 'systemMsg')" value="{{$consult->_ref_consult_anesth->_enums.position[rows]}}" {{if $consult->_ref_consult_anesth->position == $consult->_ref_consult_anesth->_enums.position[rows]}}checked="checked"{{/if}} /><label for="position_{{$consult->_ref_consult_anesth->_enums.position[rows]}}" title="{{tr}}{{$consult->_ref_consult_anesth->_enums.position[rows]}}{{/tr}}">{{tr}}{{$consult->_ref_consult_anesth->_enums.position[rows]}}{{/tr}}</label>
              {{cycle name=fin values="</td>,</td></tr>"}}
              {{/section}}
            </table>
            
            </form>
          </td>
          {{else}}
          <td colspan="4" class="text">
            Vous devez d'abord sélectionner une intervention pour ce patient
          </td>
          {{/if}}
        </tr>
      </table>