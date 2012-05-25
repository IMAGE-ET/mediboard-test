{{mb_default var=callback value=''}}

<script type="text/javascript">
  refreshAidesPreAnesth = function(user_id, user_view) {
    aideRquesAnesth.options.contextUserId = user_id;
    aideRquesAnesth.options.contextUserView = user_view;
    aideRquesAnesth.init();
  };
  
  Main.add(function() {
    var oFormVisiteAnesth = getForm("visiteAnesth");
    {{if $selOp->prat_visite_anesth_id}}
      var oForm = getForm('visiteAnesth');
      var contextUserId = {{$selOp->prat_visite_anesth_id}};
      {{if $selOp->date_visite_anesth || $currUser->_is_anesth}}
        var contextUserView = "{{mb_value object=$selOp field='prat_visite_anesth_id'}}"
      {{else}}
        var contextUserView = oForm.prat_visite_anesth_id.options[oForm.prat_visite_anesth_id.selectedIndex].innerHTML.trim();
      {{/if}}
    {{else}}
      var contextUserId = User.id;
      var contextUserId = User.view;
    {{/if}}
    aideRquesAnesth = new AideSaisie.AutoComplete(oFormVisiteAnesth.rques_visite_anesth, {
      objectClass: "COperation",
      timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
      {{if $selOp->prat_visite_anesth_id}}
      contextUserId: contextUserId,
      contextUserView: contextUserView,
      {{/if}}
      validateOnBlur:0
    });
    {{if !$selOp->date_visite_anesth}}
      Calendar.regField(oFormVisiteAnesth.date_visite_anesth);
      
      // Initialisation du champ date
      $("visiteAnesth_date_visite_anesth_da").value = "Date actuelle";
      $V(oFormVisiteAnesth.date_visite_anesth, "current");
    {{/if}}
  });
</script>

<form name="visiteAnesth" action="?m={{$m}}" method="post" onsubmit="{{if $callback}}return false;{{elseif $onSubmit}}{{$onSubmit}}{{/if}}">
  <input type="hidden" name="dosql" value="do_planning_aed" />
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$selOp}}
  {{if $selOp->date_visite_anesth}}
    <input name="prat_visite_anesth_id" type="hidden" value="{{$selOp->prat_visite_anesth_id}}" />
    <input name="date_visite_anesth"    type="hidden" value="" />
  {{/if}}
  {{if $callback}}
    <input type="hidden" name="ajax" value="1" />
    <input type="hidden" name="callback" value="{{$callback}}" />
  {{/if}}
<table class="form">
  <tr>
    <th class="title" colspan="2">Visite de pré-anesthésie</th>
  </tr>
  <tr>
    <th>{{mb_label object=$selOp field="date_visite_anesth"}}</th>
    <td>
      {{if $selOp->date_visite_anesth}}
        {{mb_value object=$selOp field="date_visite_anesth"}}
      {{else}}
        {{mb_field object=$selOp field="date_visite_anesth"}}
      {{/if}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$selOp field="prat_visite_anesth_id"}}</th>
    <td>
      {{if $selOp->date_visite_anesth}}
      Dr {{mb_value object=$selOp field="prat_visite_anesth_id"}}
      {{elseif $currUser->_is_anesth}}
      <input name="prat_visite_anesth_id" type="hidden" value="{{$currUser->_id}}" />
      Dr {{$currUser->_view}}
      {{else}}
      <select name="prat_visite_anesth_id" class="notNull" onchange="refreshAidesPreAnesth($V(this), this.options[this.selectedIndex].innerHTML.trim())">
        <option value="">&mdash; Anesthésiste</option>
        {{foreach from=$listAnesths item=curr_anesth}}
        <option value="{{$curr_anesth->user_id}}" {{if $selOp->prat_visite_anesth_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
          {{$curr_anesth->_view}}
        </option>
        {{/foreach}}
      </select>
    {{/if}}
    </td>
  </tr>
  <tr>
    <th>
      {{mb_label object=$selOp field="rques_visite_anesth"}}
    </th>
    <td class="text">
      {{if $selOp->date_visite_anesth}}
        {{mb_value object=$selOp field="rques_visite_anesth"}}
      {{else}}
        {{mb_field object=$selOp field="rques_visite_anesth"}}
      {{/if}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$selOp field="autorisation_anesth"}}</th>
    <td>
      {{if $selOp->date_visite_anesth}}
        {{mb_value object=$selOp field="autorisation_anesth"}}
      {{else}}
        {{mb_field object=$selOp field="autorisation_anesth"}}
      {{/if}}
    </td>
  </tr>
  {{if !$selOp->date_visite_anesth && !$currUser->_is_anesth}}
  <tr>
    <th>{{mb_label object=$selOp field="_password_visite_anesth"}}</th>
    <td>{{mb_field object=$selOp field="_password_visite_anesth"}}</td>
  </tr>
  {{/if}}
  {{if !$selOp->date_visite_anesth}}
  <tr>
    <td class="button" colspan="2">
      <button class="submit" {{if $callback}}type="button" onclick="onSubmitFormAjax(this.form)"{{/if}}>
        {{tr}}Validate{{/tr}}
      </button>
    </td>
  </tr>
  {{else}}
    {{if $currUser->_id != $selOp->prat_visite_anesth_id}}
      <tr>
        <th>{{mb_label object=$selOp field="_password_visite_anesth"}}</th>
        <td>{{mb_field object=$selOp field="_password_visite_anesth"}}</td>
      </tr>
    {{/if}}
    <tr>
      <td class="button" colspan="2">
        {{mb_field class="COperation" hidden="hidden" field="date_visite_anesth"}}
        {{mb_field class="COperation" hidden="hidden" field="rques_visite_anesth"}}
        {{mb_field class="COperation" hidden="hidden" field="autorisation_anesth"}}
        <button class="trash" {{if $callback}}type="button" onclick="onSubmitFormAjax(this.form)"{{/if}}>
          {{tr}}Cancel{{/tr}}
        </button>
      </td>
    </tr>
  {{/if}}
</table>
</form>