<form name="timing{{$selOp->operation_id}}" action="?m={{$m}}" method="post">
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
        <input name="entree_salle" size="5" type="text" value="{{$selOp->entree_salle|date_format:"%H:%M"}}" />
        <button type="button" class="tick notext" onclick="submitTiming(this.form); submitAnesth(anesth{{$selOp->operation_id}});">{{tr}}Save{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="this.form.entree_salle.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{elseif $modif_operation}}
        <select name="entree_salle" onchange="submitTiming(this.form);submitAnesth(anesth{{$selOp->operation_id}});">
          <option value="">-</option>
          {{foreach from=$timing.entree_salle|smarty:nodefaults item=curr_time}}
          <option value="{{$curr_time}}" {{if $curr_time == $selOp->entree_salle}}selected="selected"{{/if}}>
            {{$curr_time|date_format:"%Hh%M"}}
          </option>
          {{/foreach}}
        </select>
        <button type="button" class="cancel notext" onclick="this.form.entree_salle.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{else}}
          {{$selOp->entree_salle|date_format:"%Hh%M"}}
        {{/if}}
        {{elseif $can->edit || $modif_operation}}
        <input type="hidden" name="entree_salle" value="" />
        <button type="button" class="submit" onclick="this.form.entree_salle.value = 'current'; submitTiming(this.form); submitAnesth(anesth{{$selOp->operation_id}});">entrée patient</button>
        {{else}}-{{/if}}
      </td>
      <td class="button">
        {{if $selOp->pose_garrot}}
        Pose garrot:
        {{if $can->edit}}
        <input name="pose_garrot" size="5" type="text" value="{{$selOp->pose_garrot|date_format:"%H:%M"}}" />
        <button type="button" class="tick notext" onclick="submitTiming(this.form);">{{tr}}Save{{/tr}}</button>
        <button type="button" class="cancel notext" style="padding-left: 20px;" onclick="this.form.pose_garrot.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{elseif $modif_operation}}
        <select name="pose_garrot" onchange="submitTiming(this.form);">
          <option value="">-</option>
          {{foreach from=$timing.pose_garrot|smarty:nodefaults item=curr_time}}
          <option value="{{$curr_time}}" {{if $curr_time == $selOp->pose_garrot}}selected="selected"{{/if}}>
            {{$curr_time|date_format:"%Hh%M"}}
          </option>
          {{/foreach}}
        </select>
        <button type="button" class="cancel notext" onclick="this.form.pose_garrot.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{else}}
          {{$selOp->pose_garrot|date_format:"%Hh%M"}}
        {{/if}}
        
        {{elseif $can->edit || $modif_operation}}
        <input type="hidden" name="pose_garrot" value="" />
        <button type="button" class="submit" onclick="this.form.pose_garrot.value = 'current'; submitTiming(this.form);">pose garrot</button>
        {{else}}-{{/if}}
      </td>
      <td class="button">
        {{if $selOp->debut_op}}
        Début intervention:
        {{if $can->edit}}
        <input name="debut_op" size="5" type="text" value="{{$selOp->debut_op|date_format:"%H:%M"}}" />
        <button type="button" class="tick notext" onclick="submitTiming(this.form);">{{tr}}Save{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="this.form.debut_op.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{elseif $modif_operation}}
        <select name="debut_op" onchange="submitTiming(this.form);">
          <option value="">-</option>
          {{foreach from=$timing.debut_op|smarty:nodefaults item=curr_time}}
          <option value="{{$curr_time}}" {{if $curr_time == $selOp->debut_op}}selected="selected"{{/if}}>
            {{$curr_time|date_format:"%Hh%M"}}
          </option>
          {{/foreach}}
        </select>
        <button type="button" class="cancel notext" onclick="this.form.debut_op.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{else}}
          {{$selOp->debut_op|date_format:"%Hh%M"}}
        {{/if}}
        
        {{elseif $can->edit || $modif_operation}}
        <input type="hidden" name="debut_op" value="" />
        <button type="button" class="submit" onclick="this.form.debut_op.value = 'current'; submitTiming(this.form);">début intervention</button>
        {{else}}-{{/if}}
      </td>
    </tr>
    <tr>
      <td class="button">
        {{if $selOp->sortie_salle}}
        Sortie patient:
        {{if $can->edit}}
        <input name="sortie_salle" size="5" type="text" value="{{$selOp->sortie_salle|date_format:"%H:%M"}}" />
        <button type="button" class="tick notext" onclick="submitTiming(this.form);">{{tr}}Save{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="this.form.sortie_salle.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{elseif $modif_operation}}
        <select name="sortie_salle" onchange="submitTiming(this.form);">
          <option value="">-</option>
          {{foreach from=$timing.sortie_salle|smarty:nodefaults item=curr_time}}
          <option value="{{$curr_time}}" {{if $curr_time == $selOp->sortie_salle}}selected="selected"{{/if}}>
            {{$curr_time|date_format:"%Hh%M"}}
          </option>
          {{/foreach}}
        </select>
        <button type="button" class="cancel notext" onclick="this.form.sortie_salle.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{else}}
          {{$selOp->sortie_salle|date_format:"%Hh%M"}}
        {{/if}}
        
        {{elseif $can->edit || $modif_operation}}
        <input type="hidden" name="sortie_salle" value="" />
        <button type="button" class="submit" onclick="this.form.sortie_salle.value = 'current'; submitTiming(this.form);">sortie patient</button>
        {{else}}-{{/if}}
      </td>
      <td class="button">
        {{if $selOp->retrait_garrot}}
        Retrait garrot:
        {{if $can->edit}}
        <input name="retrait_garrot" size="5" type="text" value="{{$selOp->retrait_garrot|date_format:"%H:%M"}}" />
        <button type="button" class="tick notext" onclick="submitTiming(this.form);">{{tr}}Save{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="this.form.retrait_garrot.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{elseif $modif_operation}}
        <select name="retrait_garrot" onchange="submitTiming(this.form);">
          <option value="">-</option>
          {{foreach from=$timing.retrait_garrot|smarty:nodefaults item=curr_time}}
          <option value="{{$curr_time}}" {{if $curr_time == $selOp->retrait_garrot}}selected="selected"{{/if}}>
            {{$curr_time|date_format:"%Hh%M"}}
          </option>
          {{/foreach}}
        </select>
        <button type="button" class="cancel notext" onclick="this.form.retrait_garrot.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{else}}
          {{$selOp->retrait_garrot|date_format:"%Hh%M"}}
        {{/if}}
        
        {{elseif $can->edit || $modif_operation}}
        <input type="hidden" name="retrait_garrot" value="" />
        <button type="button" class="submit" onclick="this.form.retrait_garrot.value = 'current'; submitTiming(this.form);">retrait garrot</button>
        {{else}}-{{/if}}
      </td>
      <td class="button">
        {{if $selOp->fin_op}}
        Fin intervention:
        {{if $can->edit}}
        <input name="fin_op" size="5" type="text" value="{{$selOp->fin_op|date_format:"%H:%M"}}" />
        <button type="button" class="tick notext" onclick="submitTiming(this.form);">{{tr}}Save{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="this.form.fin_op.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{elseif $modif_operation}}
        <select name="fin_op" onchange="submitTiming(this.form);">
          <option value="">-</option>
          {{foreach from=$timing.fin_op|smarty:nodefaults item=curr_time}}
          <option value="{{$curr_time}}" {{if $curr_time == $selOp->fin_op}}selected="selected"{{/if}}>
            {{$curr_time|date_format:"%Hh%M"}}
          </option>
          {{/foreach}}
        </select>
        <button type="button" class="cancel notext" onclick="this.form.fin_op.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
        {{else}}
          {{$selOp->fin_op|date_format:"%Hh%M"}}
        {{/if}}
        
        {{elseif $can->edit || $modif_operation}}
        <input type="hidden" name="fin_op" value="" />
        <button type="button" class="submit" onclick="this.form.fin_op.value = 'current'; submitTiming(this.form);">fin intervention</button>
        {{else}}-{{/if}}
      </td>
    </tr>
  </table>
</form>
