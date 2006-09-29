        <tr>
          <th>Timming<br/>Anesth�sie</th>
          <td>
            <form name="timing_anesth{{$selOp->operation_id}}" action="index.php?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="operation_id" value="{{$selOp->operation_id}}" />
            <input type="hidden" name="del" value="0" />
            <table class="form">
              <tr>
                <td class="button">
                  {{if $selOp->entree_bloc}}
                  Entr�e patient:
                  {{if $canEdit}}
                  <input name="entree_bloc" size="5" type="text" value="{{$selOp->entree_bloc|date_format:"%H:%M"}}">
                  <button class="tick notext" type="submit"></button>
                  {{else}}
                  <select name="entree_bloc" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.entree_bloc|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->entree_bloc}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  {{/if}}
                  <button class="cancel notext" type="submit" onclick="this.form.entree_bloc.value = ''"></button>
                  {{else}}
                  <input type="hidden" name="entree_bloc" value="" />
                  <button class="submit" type="submit" onclick="this.form.entree_bloc.value = 'current'">entr�e patient</button>
                  {{/if}}
                </td>
                <td class="button">
                  {{if $selOp->pose_garrot}}
                  Pose garrot:
                  {{if $canEdit}}
                  <input name="pose_garrot" size="5" type="text" value="{{$selOp->pose_garrot|date_format:"%H:%M"}}">
                  <button class="tick notext" type="submit"></button>
                  {{else}}
                  <select name="pose_garrot" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.pose_garrot|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->pose_garrot}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  {{/if}}
                  <button class="cancel notext" type="submit" onclick="this.form.pose_garrot.value = ''"></button>
                  {{else}}
                  <input type="hidden" name="pose_garrot" value="" />
                  <button class="submit" type="submit" onclick="this.form.pose_garrot.value = 'current'">pose garrot</button>
                  {{/if}}
                </td>
                <td class="button">
                  {{if $selOp->debut_op}}
                  D�but op�ration:
                  {{if $canEdit}}
                  <input name="debut_op" size="5" type="text" value="{{$selOp->debut_op|date_format:"%H:%M"}}">
                  <button class="tick notext" type="submit"></button>
                  {{else}}
                  <select name="debut_op" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.debut_op|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->debut_op}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  {{/if}}
                  <button class="cancel notext" type="submit" onclick="this.form.debut_op.value = ''"></button>
                  {{else}}
                  <input type="hidden" name="debut_op" value="" />
                  <button class="submit" type="submit" onclick="this.form.debut_op.value = 'current'">d�but intervention</button>
                  {{/if}}
                </td>
              </tr>
              <tr>
                <td class="button">
                  {{if $selOp->sortie_bloc}}
                  Sortie patient:
                  {{if $canEdit}}
                  <input name="sortie_bloc" size="5" type="text" value="{{$selOp->sortie_bloc|date_format:"%H:%M"}}">
                  <button class="tick notext" type="submit"></button>
                  {{else}}
                  <select name="sortie_bloc" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.sortie_bloc|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->sortie_bloc}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  {{/if}}
                  <button class="cancel notext" type="submit" onclick="this.form.sortie_bloc.value = ''"></button>
                  {{else}}
                  <input type="hidden" name="sortie_bloc" value="" />
                  <button class="submit" type="submit" onclick="this.form.sortie_bloc.value = 'current'">sortie patient</button>
                  {{/if}}
                </td>
                <td class="button">
                  {{if $selOp->retrait_garrot}}
                  Retrait garrot:
                  {{if $canEdit}}
                  <input name="retrait_garrot" size="5" type="text" value="{{$selOp->retrait_garrot|date_format:"%H:%M"}}">
                  <button class="tick notext" type="submit"></button>
                  {{else}}
                  <select name="retrait_garrot" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.retrait_garrot|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->retrait_garrot}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  {{/if}}
                  <button class="cancel notext" type="submit" onclick="this.form.retrait_garrot.value = ''"></button>
                  {{else}}
                  <input type="hidden" name="retrait_garrot" value="" />
                  <button class="submit" type="submit" onclick="this.form.retrait_garrot.value = 'current'">retrait garrot</button>
                  {{/if}}
                </td>
                <td class="button">
                  {{if $selOp->fin_op}}
                  Fin op�ration:
                  {{if $canEdit}}
                  <input name="fin_op" size="5" type="text" value="{{$selOp->fin_op|date_format:"%H:%M"}}">
                  <button class="tick notext" type="submit"></button>
                  {{else}}
                  <select name="fin_op" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.fin_op|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->fin_op}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  {{/if}}
                  <button class="cancel notext" type="submit" onclick="this.form.fin_op.value = ''"></button>
                  {{else}}
                  <input type="hidden" name="fin_op" value="" />
                  <button class="submit" type="submit" onclick="this.form.fin_op.value = 'current'">fin intervention</button>
                  {{/if}}
                </td>
              </tr>
            </table>
            <hr />
            <select name="type_anesth" onchange="submitFormAjax(this.form, 'systemMsg');">
              <option value="">&mdash; Type d'anesth�sie</option>
              {{foreach from=$listAnesthType item=curr_anesth}}
              <option value="{{$curr_anesth->type_anesth_id}}" {{if $selOp->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}} >
                {{$curr_anesth->name}}
              </option>
             {{/foreach}}
            </select>
            par le Dr.
            <select name="anesth_id" onchange="submit()">
              <option value="">&mdash; Anesth�siste</option>
              {{foreach from=$listAnesths item=curr_anesth}}
              <option value="{{$curr_anesth->user_id}}" {{if $selOp->_ref_anesth->user_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
                {{$curr_anesth->_view}}
              </option>
              {{/foreach}}
            </select>
            -
            {{if $selOp->induction}}
            Induction:
            {{if $canEdit}}
            <input name="induction" size="5" type="text" value="{{$selOp->induction|date_format:"%H:%M"}}">
            <button class="tick notext" type="submit"></button>
            {{else}}
            <select name="induction" onchange="this.form.submit()">
              <option value="">-</option>
              {{foreach from=$timing.induction|smarty:nodefaults item=curr_time}}
              <option value="{{$curr_time}}" {{if $curr_time == $selOp->induction}}selected="selected"{{/if}}>
                {{$curr_time|date_format:"%Hh%M"}}
              </option>
              {{/foreach}}
            </select>
            {{/if}}
            <button class="cancel notext" type="submit" onclick="this.form.induction.value = ''"></button>
            {{else}}
            <input type="hidden" name="induction" value="" />
            <button class="submit" type="submit" onclick="this.form.induction.value = 'current'">induction</button>
            {{/if}}
            </form>
          </td>
        </tr>