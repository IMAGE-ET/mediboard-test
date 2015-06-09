<form name="addConsultImmediate" action="?" method="post">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_consult_now" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="patient_id" class="notNull ref" value="{{$patient->_id}}" />
  <input type="hidden" name="_operation_id" value="{{$operation_id}}" />

  <table class="form">
    <tr>
      <th style="width: 40%">{{mb_label object=$consult field="_datetime"}}</th>
      <td>{{mb_field object=$consult field="_datetime" canNull=false register=true form=addConsultImmediate}}</td>
    </tr>

    <tr>
      <th class="notNull">{{mb_label object=$consult field="_prat_id"}}</th>

      <td>
        <select name="_prat_id" class="notNull ref">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>

          {{mb_include module=mediusers template=inc_options_mediuser selected=$app->user_id list=$praticiens}}
        </select>
      </td>
    </tr>

    {{assign var=required_uf_soins value="dPplanningOp CSejour required_uf_soins"|conf:"CGroups-$g"}}
    {{assign var=create_consult_sejour value="dPcabinet CConsultation create_consult_sejour"}}

    {{if $required_uf_soins != "no" && $create_consult_sejour}}
    <!-- Selection de l'unité de soins -->
    <tr>
      <th>{{mb_label object=$consult field="_uf_soins_id"}}</th>
      <td>
        <select name="_uf_soins_id" class="ref {{if $required_uf_soins == "obl"}}notNull{{/if}}">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$ufs.soins item=_uf}}
            <option value="{{$_uf->_id}}">
              {{mb_value object=$_uf field=libelle}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    {{/if}}

    <tr>
      <td colspan="2" class="button">
        <button class="new" type="submit">{{tr}}CConsultation-action-Consult{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>