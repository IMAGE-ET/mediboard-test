<table class="tbl">
  <tr>
    <th colspan="10" class="title">{{tr}}CMediusers-back-tarifs{{/tr}}</th>
  </tr>
  
  {{if !$user->_is_praticien && !$user->_is_secretaire}}
  <tr>
    <td class="text">
      <div class="big-info">
        N'étant pas praticien, vous n'avez pas accès à la liste de tarifs personnels.
      </div>
    </td>
  </tr>
  {{/if}}
  
  {{if $user->_is_secretaire}}
  <tr>
    <td colspan="10">
      <form action="?" name="selection" method="get">
        <input type="hidden" name="tarif_id" value="" />
        <input type="hidden" name="m" value="{{$m}}" />
        <select name="prat_id" onchange="this.form.submit()">
          <option value="">&mdash; Aucun praticien</option>
          {{foreach from=$listPrat item=_prat}}
          <option class="mediuser" style="border-color: #{{$_prat->_ref_function->color}};" value="{{$_prat->_id}}"
          {{if $_prat->_id == $prat->_id}}selected="selected"{{/if}}>
            {{$_prat}}
          </option>
          {{/foreach}}
        </select>
      </form>
    </td>
  </tr>
  {{/if}}
  

  {{if $user->_is_praticien || $user->_is_secretaire}}
	{{mb_include template=inc_list_tarifs_by_owner tarifs=$listeTarifsChir}}
  {{/if}}
</table>

<table class="tbl">
  <tr><th colspan="10" class="title">{{tr}}CFunctions-back-tarifs{{/tr}}</th></tr>
  {{mb_include template=inc_list_tarifs_by_owner tarifs=$listeTarifsSpe}}
</table>
