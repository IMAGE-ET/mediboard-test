<form name="editFrm" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete : Control.Modal.close});">
  <input type="hidden" name="m" value="mediusers" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_discipline_aed" />
  {{mb_key object=$discipline}}

  <table class="form">
    <tr>
      {{mb_include module=system template=inc_form_table_header object=$discipline}}
    </tr>
    <tr>
      <th style="width: 40%">{{mb_label object=$discipline field="text"}}</th>
      <td>{{mb_field object=$discipline field="text" size="50"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$discipline field="categorie"}}</th>
      <td>{{mb_field object=$discipline field="categorie" emptyLabel="Choose" }}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        {{if $discipline->_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="button" onclick="confirmDeletion(this.form,
            {typeName:'la spécialité',objName:'{{$discipline->text|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>

        {{else}}
          <button class="submit" name="btnFuseAction" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>

<hr />

{{if $discipline->_id}}
  <table class="tbl">
    <tr>
      <th class="title" colspan="4">{{tr}}CDiscipline-back-users{{/tr}}</th>
    </tr>
    <tr>
      <th>{{tr}}CMediusers-_p_last_name{{/tr}} {{tr}}CMediusers-_p_first_name{{/tr}}</th>
    </tr>
    {{foreach from=$discipline->_ref_users item=_user}}
      <tr>
        <td class="text"><a href="?m=mediusers&amp;tab=vw_idx_mediusers&amp;user_id={{$_user->user_id}}" title="Modifier cet utilisateur">Dr {{$_user->_view}}</a></td>
      </tr>
      {{foreachelse}}
      <tr>
        <td class="button" colspan="4">{{tr}}CDiscipline-back-users.empty{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
{{/if}}