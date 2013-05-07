<table class="tbl">
  {{if !$user->_is_praticien && !$user->_is_secretaire}}
  <tr>
    <td class="text">
      <div class="big-info">
        N'�tant pas praticien, vous n'avez pas acc�s � la liste de tarifs personnels.
      </div>
    </td>
  </tr>
  {{/if}}
  
  {{if $user->_is_secretaire}}
  <tr>
    <td colspan="10">
      <form action="?" name="selectPrat" method="get">
        <input type="hidden" name="tarif_id" value="" />
        <input type="hidden" name="m" value="{{$m}}" />
        <select name="prat_id" onchange="this.form.submit()">
          <option value="">&mdash; Aucun praticien</option>
          {{mb_include module=mediusers template=inc_options_mediuser selected=$prat->_id list=$listPrat}}
        </select>
      </form>
    </td>
  </tr>
  {{/if}}
</table>

<table class="tbl">
  <tr>
    <th colspan="10" class="title">{{tr}}CMediusers-back-tarifs{{/tr}}</th>
  </tr>
  
  {{if $user->_is_praticien || $user->_is_secretaire}}
  {{mb_include module=cabinet template=inc_list_tarifs_by_owner tarifs=$listeTarifsChir}}
  {{/if}}

  <tr>
    <th colspan="10" class="title">{{tr}}CFunctions-back-tarifs{{/tr}}</th>
  </tr>
  {{mb_include module=cabinet template=inc_list_tarifs_by_owner tarifs=$listeTarifsSpe}}
  
  {{if $listeTarifsEtab|@count}}
    <tr>
      <th colspan="10" class="title">{{tr}}CGroups-back-tarif_group{{/tr}}</th>
    </tr>
    {{mb_include module=cabinet template=inc_list_tarifs_by_owner tarifs=$listeTarifsEtab}}
 {{/if}}
</table>
