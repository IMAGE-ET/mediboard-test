        <tr>
          <th>Timming<br/>Anesthésie</th>
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
                  Entrée patient:
                  {{if $canEdit}}
                  <input name="entree_bloc" size="5" type="text" value="{{$selOp->entree_bloc|date_format:"%H:%M"}}">
                  <button class="tick" type="submit"></button>
                  {{else}}
                  <select name="entree_bloc" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.entree_bloc item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->entree_bloc}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  {{/if}}
                  <button class="cancel" type="submit" onclick="this.form.entree_bloc.value = ''"></button>
                  {{else}}
                  <input type="hidden" name="entree_bloc" value="" />
                  <button class="submit" type="submit" onclick="this.form.entree_bloc.value = 'current'">entrée patient</button>
                  {{/if}}
                </td>
                <td class="button">
                  {{if $selOp->pose_garrot}}
                  Pose garrot:
                  {{if $canEdit}}
                  <input name="pose_garrot" size="5" type="text" value="{{$selOp->pose_garrot|date_format:"%H:%M"}}">
                  <button class="tick" type="submit"></button>
                  {{else}}
                  <select name="pose_garrot" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.pose_garrot item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->pose_garrot}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  {{/if}}
                  <button class="cancel" type="submit" onclick="this.form.pose_garrot.value = ''"></button>
                  {{else}}
                  <input type="hidden" name="pose_garrot" value="" />
                  <button class="submit" type="submit" onclick="this.form.pose_garrot.value = 'current'">pose garrot</button>
                  {{/if}}
                </td>
                <td class="button">
                  {{if $selOp->debut_op}}
                  Début opération:
                  {{if $canEdit}}
                  <input name="debut_op" size="5" type="text" value="{{$selOp->debut_op|date_format:"%H:%M"}}">
                  <button class="tick" type="submit"></button>
                  {{else}}
                  <select name="debut_op" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.debut_op item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->debut_op}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  {{/if}}
                  <button class="cancel" type="submit" onclick="this.form.debut_op.value = ''"></button>
                  {{else}}
                  <input type="hidden" name="debut_op" value="" />
                  <button class="submit" type="submit" onclick="this.form.debut_op.value = 'current'">début intervention</button>
                  {{/if}}
                </td>
              </tr>
              <tr>
                <td class="button">
                  {{if $selOp->fin_op}}
                  Fin opération:
                  {{if $canEdit}}
                  <input name="fin_op" size="5" type="text" value="{{$selOp->fin_op|date_format:"%H:%M"}}">
                  <button class="tick" type="submit"></button>
                  {{else}}
                  <select name="fin_op" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.fin_op item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->fin_op}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  {{/if}}
                  <button class="cancel" type="submit" onclick="this.form.fin_op.value = ''"></button>
                  {{else}}
                  <input type="hidden" name="fin_op" value="" />
                  <button class="submit" type="submit" onclick="this.form.fin_op.value = 'current'">fin intervention</button>
                  {{/if}}
                </td>
                <td class="button">
                  {{if $selOp->retrait_garrot}}
                  Retrait garrot:
                  {{if $canEdit}}
                  <input name="retrait_garrot" size="5" type="text" value="{{$selOp->retrait_garrot|date_format:"%H:%M"}}">
                  <button class="tick" type="submit"></button>
                  {{else}}
                  <select name="retrait_garrot" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.retrait_garrot item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->retrait_garrot}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  {{/if}}
                  <button class="cancel" type="submit" onclick="this.form.retrait_garrot.value = ''"></button>
                  {{else}}
                  <input type="hidden" name="retrait_garrot" value="" />
                  <button class="submit" type="submit" onclick="this.form.retrait_garrot.value = 'current'">retrait garrot</button>
                  {{/if}}
                </td>
                <td class="button">
                  {{if $selOp->sortie_bloc}}
                  Sortie patient:
                  {{if $canEdit}}
                  <input name="sortie_bloc" size="5" type="text" value="{{$selOp->sortie_bloc|date_format:"%H:%M"}}">
                  <button class="tick" type="submit"></button>
                  {{else}}
                  <select name="sortie_bloc" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.sortie_bloc item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->sortie_bloc}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  {{/if}}
                  <button class="cancel" type="submit" onclick="this.form.sortie_bloc.value = ''"></button>
                  {{else}}
                  <input type="hidden" name="sortie_bloc" value="" />
                  <button class="submit" type="submit" onclick="this.form.sortie_bloc.value = 'current'">sortie patient</button>
                  {{/if}}
                </td>
              </tr>
            </table>
            <hr />
            <select name="type_anesth" onchange="submitFormAjax(this.form, 'systemMsg');">
              <option value="">&mdash; Type d'anesthésie</option>
              {{foreach from=$listAnesthType item=curr_type key=curr_key}}
              <option value="{{$curr_key}}" {{if $selOp->_lu_type_anesth == $curr_type}} selected="selected" {{/if}}>
                {{$curr_type}}
              </option>
              {{/foreach}}
            </select>
            par le Dr.
            <select name="anesth_id" onchange="submit()">
              <option value="0">&mdash; Anesthésiste</option>
              {{foreach from=$listAnesths item=curr_anesth}}
              <option value="{{$curr_anesth->user_id}}" {{if $selOp->_ref_anesth->user_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
                {{$curr_anesth->_view}}
              </option>
              {{/foreach}}
            </select>
            </form>
          </td>
        </tr>