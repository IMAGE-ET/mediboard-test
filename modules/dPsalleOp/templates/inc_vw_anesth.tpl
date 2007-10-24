
            <form name="anesth{{$selOp->operation_id}}" action="?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="operation_id" value="{{$selOp->operation_id}}" />
            <input type="hidden" name="del" value="0" />
              
          
         
          
            <table>
              <tr>
         
                <td rowspan="2" style="vertical-align: middle;">
                  {{if $can->edit || $modif_operation}}
                  <select name="type_anesth" onchange="submitAnesth(this.form);">
                    <option value="">&mdash; Type d'anesthésie</option>
                    {{foreach from=$listAnesthType item=curr_anesth}}
                    <option value="{{$curr_anesth->type_anesth_id}}" {{if $selOp->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}} >
                      {{$curr_anesth->name}}
                    </option>
                   {{/foreach}}
                  </select>
                  {{elseif $selOp->type_anesth}}
                    {{assign var="keyAnesth" value=$selOp->type_anesth}}
                    {{assign var="typeAnesth" value=$listAnesthType.$keyAnesth}}
                    {{$typeAnesth->name}}
                  {{else}}-{{/if}}
                  <br />par le Dr.
                  {{if $can->edit || $modif_operation}}
                  <select name="anesth_id" onchange="submitAnesth(this.form);">
                    <option value="">&mdash; Anesthésiste</option>
                    {{foreach from=$listAnesths item=curr_anesth}}
                    <option value="{{$curr_anesth->user_id}}" {{if $selOp->_ref_anesth->user_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
                      {{$curr_anesth->_view}}
                    </option>
                    {{/foreach}}
                  </select>
                  {{elseif $selOp->_ref_anesth->user_id}}
                    {{assign var="keyChir" value=$selOp->_ref_anesth->user_id}}
                    {{assign var="typeChir" value=$listAnesths.$keyChir}}
                    {{$typeChir->_view}}
                  {{else}}-{{/if}}
                </td>
                <td>
                  {{if $selOp->induction_debut}}
                  Début d'induction:
                  {{if $can->edit}}
                  <input name="induction_debut" size="5" type="text" value="{{$selOp->induction_debut|date_format:"%H:%M"}}" />
                  <button type="button" class="tick notext" onclick="submitAnesth(this.form);">{{tr}}Save{{/tr}}</button>
                  <button type="button" class="cancel notext" onclick="this.form.induction_debut.value = ''; submitAnesth(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{elseif $modif_operation}}
                  <select name="induction_debut" onchange="submitAnesth(this.form);">
                    <option value="">-</option>
                    {{foreach from=$timing.induction_debut|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->induction_debut}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button type="button" class="cancel notext" onclick="this.form.induction_debut.value = ''; submitAnesth(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{else}}
                    {{$selOp->induction_debut|date_format:"%Hh%M"}}
                  {{/if}}
            
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="induction_debut" value="" />
                  <button type="button" class="submit" onclick="this.form.induction_debut.value = 'current'; submitAnesth(this.form);">Début d'induction</button>
                  {{else}}-{{/if}}
                </td>
              </tr>
              <tr>
                <td>
                  {{if $selOp->induction_fin}}
                  Fin d'induction:
                  {{if $can->edit}}
                  <input name="induction_fin" size="5" type="text" value="{{$selOp->induction_fin|date_format:"%H:%M"}}" />
                  <button type="button" class="tick notext" onclick="submitAnesth(this.form);">{{tr}}Save{{/tr}}</button>
                  <button type="button" class="cancel notext" onclick="this.form.induction_fin.value = ''; submitAnesth(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{elseif $modif_operation}}
                  <select name="induction_fin" onchange="submitAnesth(this.form);">
                    <option value="">-</option>
                    {{foreach from=$timing.induction_fin|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->induction_fin}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button type="button" class="cancel notext" onclick="this.form.induction_fin.value = ''; submitAnesth(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{else}}
                    {{$selOp->induction_fin|date_format:"%Hh%M"}}
                  {{/if}}
            
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="induction_fin" value="" />
                  <button type="button" class="submit" onclick="this.form.induction_fin.value = 'current'; submitAnesth(this.form);">Fin d'induction</button>
                  {{else}}-{{/if}}
                </td>
              </tr>
            </table>
          
        </form>
  