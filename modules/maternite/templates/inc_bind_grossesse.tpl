{{if $grossesse->_id}}
  <button type="button" class="tick" onclick="submitAfterGrossesse('{{$grossesse->_id}}')">
    Associer à la grossesse courante
  </button>
{{else}}
  <form name="createGrossesse" action="?" method="post"
    onsubmit="return onSubmitFormAjax(this)">
    <input type="hidden" name="m" value="maternite" />
    <input type="hidden" name="dosql" value="do_grossesse_aed" />
    {{mb_key object=$grossesse}}
    {{mb_field object=$grossesse field=parturiente_id hidden=true}}
    <input type="hidden" name="callback" value="submitAfterGrossesse" />
    <table>
      <tr>
        <th>
          {{mb_label object=$grossesse field=terme_prevu}}
        </th>
        <td>
          {{mb_field object=$grossesse field=terme_prevu form=createGrossesse register=true}}
        </td>
      </tr>
      <tr>
        <td class="button" colspan="2">
          <button class="new" type="button" onclick="this.form.onsubmit();">Créer la grossesse</button>
        </td>
      </tr>
    </table>
  </form>
{{/if}}
