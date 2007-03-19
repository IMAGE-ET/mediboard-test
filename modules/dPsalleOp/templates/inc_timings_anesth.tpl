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
                  {{if $selOp->entree_salle}}
                  Entrée patient:
                  {{if $can->edit}}
                  <input name="entree_salle" size="5" type="text" value="{{$selOp->entree_salle|date_format:"%H:%M"}}">
                  <button class="tick notext" type="submit"></button>
                  <button class="cancel notext" type="submit" onclick="this.form.entree_salle.value = ''"></button>
                  {{elseif $modif_operation}}
                  <select name="entree_salle" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.entree_salle|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->entree_salle}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button class="cancel notext" type="submit" onclick="this.form.entree_salle.value = ''"></button>
                  {{else}}
                    {{$selOp->entree_salle|date_format:"%Hh%M"}}
                  {{/if}}
                  
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="entree_salle" value="" />
                  <button class="submit" type="submit" onclick="this.form.entree_salle.value = 'current'">entrée patient</button>
                  {{else}}-{{/if}}
                </td>
                <td class="button">
                  {{if $selOp->pose_garrot}}
                  Pose garrot:
                  {{if $can->edit}}
                  <input name="pose_garrot" size="5" type="text" value="{{$selOp->pose_garrot|date_format:"%H:%M"}}">
                  <button class="tick notext" type="submit"></button>
                  <button class="cancel notext" type="submit" onclick="this.form.pose_garrot.value = ''"></button>
                  {{elseif $modif_operation}}
                  <select name="pose_garrot" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.pose_garrot|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->pose_garrot}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button class="cancel notext" type="submit" onclick="this.form.pose_garrot.value = ''"></button>
                  {{else}}
                    {{$selOp->pose_garrot|date_format:"%Hh%M"}}
                  {{/if}}
                  
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="pose_garrot" value="" />
                  <button class="submit" type="submit" onclick="this.form.pose_garrot.value = 'current'">pose garrot</button>
                  {{else}}-{{/if}}
                </td>
                <td class="button">
                  {{if $selOp->debut_op}}
                  Début opération:
                  {{if $can->edit}}
                  <input name="debut_op" size="5" type="text" value="{{$selOp->debut_op|date_format:"%H:%M"}}">
                  <button class="tick notext" type="submit"></button>
                  <button class="cancel notext" type="submit" onclick="this.form.debut_op.value = ''"></button>
                  {{elseif $modif_operation}}
                  <select name="debut_op" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.debut_op|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->debut_op}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button class="cancel notext" type="submit" onclick="this.form.debut_op.value = ''"></button>
                  {{else}}
                    {{$selOp->debut_op|date_format:"%Hh%M"}}
                  {{/if}}
                  
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="debut_op" value="" />
                  <button class="submit" type="submit" onclick="this.form.debut_op.value = 'current'">début intervention</button>
                  {{else}}-{{/if}}
                </td>
              </tr>
              <tr>
                <td class="button">
                  {{if $selOp->sortie_salle}}
                  Sortie patient:
                  {{if $can->edit}}
                  <input name="sortie_salle" size="5" type="text" value="{{$selOp->sortie_salle|date_format:"%H:%M"}}">
                  <button class="tick notext" type="submit"></button>
                  <button class="cancel notext" type="submit" onclick="this.form.sortie_salle.value = ''"></button>
                  {{elseif $modif_operation}}
                  <select name="sortie_salle" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.sortie_salle|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->sortie_salle}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button class="cancel notext" type="submit" onclick="this.form.sortie_salle.value = ''"></button>
                  {{else}}
                    {{$selOp->sortie_salle|date_format:"%Hh%M"}}
                  {{/if}}
                  
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="sortie_salle" value="" />
                  <button class="submit" type="submit" onclick="this.form.sortie_salle.value = 'current'">sortie patient</button>
                  {{else}}-{{/if}}
                </td>
                <td class="button">
                  {{if $selOp->retrait_garrot}}
                  Retrait garrot:
                  {{if $can->edit}}
                  <input name="retrait_garrot" size="5" type="text" value="{{$selOp->retrait_garrot|date_format:"%H:%M"}}">
                  <button class="tick notext" type="submit"></button>
                  <button class="cancel notext" type="submit" onclick="this.form.retrait_garrot.value = ''"></button>
                  {{elseif $modif_operation}}
                  <select name="retrait_garrot" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.retrait_garrot|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->retrait_garrot}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button class="cancel notext" type="submit" onclick="this.form.retrait_garrot.value = ''"></button>
                  {{else}}
                    {{$selOp->retrait_garrot|date_format:"%Hh%M"}}
                  {{/if}}
                  
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="retrait_garrot" value="" />
                  <button class="submit" type="submit" onclick="this.form.retrait_garrot.value = 'current'">retrait garrot</button>
                  {{else}}-{{/if}}
                </td>
                <td class="button">
                  {{if $selOp->fin_op}}
                  Fin opération:
                  {{if $can->edit}}
                  <input name="fin_op" size="5" type="text" value="{{$selOp->fin_op|date_format:"%H:%M"}}">
                  <button class="tick notext" type="submit"></button>
                  <button class="cancel notext" type="submit" onclick="this.form.fin_op.value = ''"></button>
                  {{elseif $modif_operation}}
                  <select name="fin_op" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.fin_op|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->fin_op}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button class="cancel notext" type="submit" onclick="this.form.fin_op.value = ''"></button>
                  {{else}}
                    {{$selOp->fin_op|date_format:"%Hh%M"}}
                  {{/if}}
                  
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="fin_op" value="" />
                  <button class="submit" type="submit" onclick="this.form.fin_op.value = 'current'">fin intervention</button>
                  {{else}}-{{/if}}
                </td>
              </tr>
            </table>
            <hr />
            
            <table>
              <tr>
                <td rowspan="2" style="vertical-align: middle;">
                  {{if $can->edit || $modif_operation}}
                  <select name="type_anesth" onchange="submitFormAjax(this.form, 'systemMsg');">
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
                  par le Dr.
                  {{if $can->edit || $modif_operation}}
                  <select name="anesth_id" onchange="submit()">
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
                  <input name="induction_debut" size="5" type="text" value="{{$selOp->induction_debut|date_format:"%H:%M"}}">
                  <button class="tick notext" type="submit"></button>
                  <button class="cancel notext" type="submit" onclick="this.form.induction_debut.value = ''"></button>
                  {{elseif $modif_operation}}
                  <select name="induction_debut" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.induction_debut|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->induction_debut}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button class="cancel notext" type="submit" onclick="this.form.induction_debut.value = ''"></button>
                  {{else}}
                    {{$selOp->induction_debut|date_format:"%Hh%M"}}
                  {{/if}}
            
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="induction_debut" value="" />
                  <button class="submit" type="submit" onclick="this.form.induction_debut.value = 'current'">Début d'induction</button>
                  {{else}}-{{/if}}
                </td>
              </tr>
              <tr>
                <td>
                  {{if $selOp->induction_fin}}
                  Fin d'induction:
                  {{if $can->edit}}
                  <input name="induction_fin" size="5" type="text" value="{{$selOp->induction_fin|date_format:"%H:%M"}}">
                  <button class="tick notext" type="submit"></button>
                  <button class="cancel notext" type="submit" onclick="this.form.induction_fin.value = ''"></button>
                  {{elseif $modif_operation}}
                  <select name="induction_fin" onchange="this.form.submit()">
                    <option value="">-</option>
                    {{foreach from=$timing.induction_fin|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->induction_fin}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button class="cancel notext" type="submit" onclick="this.form.induction_fin.value = ''"></button>
                  {{else}}
                    {{$selOp->induction_fin|date_format:"%Hh%M"}}
                  {{/if}}
            
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="induction_fin" value="" />
                  <button class="submit" type="submit" onclick="this.form.induction_fin.value = 'current'">Fin d'induction</button>
                  {{else}}-{{/if}}
                </td>
              </tr>
            </table>
            
            </form>
          </td>
        </tr>