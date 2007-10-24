{{mb_include_script module="dPpatients" script="autocomplete"}}

<script type="text/javascript">
function submitForm(oForm){
  submitFormAjax(oForm, 'systemMsg')
}

function pageMain() {
  initInseeFields("editFct", "cp", "ville");
}
</script>
<table class="main">
  <tr>
   <th class="title" colspan="2">
     {{tr}}menu-myInfo{{/tr}}
   </th>
  </tr>
  
  <tr>
    <td class="halfPane">
      <form name="editUser" action="?m={{$m}}&amp;a=edit_infos" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="dosql" value="do_mediusers_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="user_id" value="{{$user->user_id}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="modifDroit" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
            {{$user->_view}}
          </th>
        </tr>
        <tr>
          <th>{{mb_label object=$user field="_user_last_name"}}</th>
          <td>{{mb_field object=$user field="_user_last_name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$user field="_user_first_name"}}</th>
          <td>{{mb_field object=$user field="_user_first_name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$user field="discipline_id"}}</th>
          <td>
            <select name="discipline_id" class="{{$user->_props.discipline_id}}">
              <option value="">&mdash; Choisir une spécialité &mdash;</option>
              {{foreach from=$disciplines item=curr_discipline}}
              <option value="{{$curr_discipline->discipline_id}}" {{if $curr_discipline->discipline_id == $user->discipline_id}} selected="selected" {{/if}}>
                {{$curr_discipline->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$user field="spec_cpam_id"}}</th>
          <td>
            <select name="spec_cpam_id" class="{{$user->_props.spec_cpam_id}}">
              <option value="">&mdash; Choisir une spécialité &mdash;</option>
              {{foreach from=$spec_cpam item=curr_spec}}
              <option value="{{$curr_spec->spec_cpam_id}}" {{if $curr_spec->spec_cpam_id == $user->spec_cpam_id}} selected="selected" {{/if}}>
                {{$curr_spec->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$user field="adeli"}}</th>
          <td>{{mb_field object=$user field="adeli"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$user field="titres"}}</th>
          <td>{{mb_field object=$user field="titres"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$user field="compte"}}</th>
          <td>
              {{mb_field object=$user field="_compte_banque" onkeyup="followUp(this, '_compte_guichet', 5)" }}
              {{mb_field object=$user field="_compte_guichet" onkeyup="followUp(this, '_compte_numero', 5)" }}
              {{mb_field object=$user field="_compte_numero" onkeyup="followUp(this, '_compte_cle', 11)" }}
              {{mb_field object=$user field="_compte_cle"}}
          </td>
        </tr>
        <tr>
          <th>
            {{mb_label object=$user field="banque_id"}}
          </th>
          <td>
          <select name="banque_id">
          <option value="">&mdash; Choix d'une banque</option>
          {{foreach from=$banques item="banque"}}
            <option value="{{$banque->_id}}" {{if $user->banque_id == $banque->_id}}selected = "selected"{{/if}}>{{$banque->_view}}</option>
          {{/foreach}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$user field="_user_email"}}</th>
          <td>{{mb_field object=$user field="_user_email"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$user field="_user_phone"}}</th>
          <td>{{mb_field object=$user field="_user_phone"}}</td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <button type="button" class="modify" onclick="submitForm(this.form)">
              {{tr}}Modify{{/tr}}
            </button>
          </td>
        </tr>
      </table>
      </form>
    </td>
    
    <td class="halfPane">
      <form name="editFct" action="?m={{$m}}" method="post" onSubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_functions_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="function_id" value="{{$fonction->function_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
            {{$fonction->_view}}
          </th>
        </tr>
        <tr>
          <th>{{mb_label object=$fonction field="soustitre"}}</th>
          <td>{{mb_field object=$fonction field="soustitre"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fonction field="adresse"}}</th>
          <td>{{mb_field object=$fonction field="adresse"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fonction field="cp"}}</th>
          <td>
            {{mb_field object=$fonction field="cp"}}
            <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$fonction field="ville"}}</th>
          <td>
            {{mb_field object=$fonction field="ville"}}
            <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$fonction field="tel" defaultFor="_tel1"}}</th>
          <td>
            <input type="text" name="_tel1" size="2" maxlength="2" value="{{$fonction->_tel1}}" class="num length|2" onkeyup="followUp(this, '_tel2', 2)" /> - 
            <input type="text" name="_tel2" size="2" maxlength="2" value="{{$fonction->_tel2}}" class="num length|2" onkeyup="followUp(this, '_tel3', 2)" /> -
            <input type="text" name="_tel3" size="2" maxlength="2" value="{{$fonction->_tel3}}" class="num length|2" onkeyup="followUp(this, '_tel4', 2)" /> -
            <input type="text" name="_tel4" size="2" maxlength="2" value="{{$fonction->_tel4}}" class="num length|2" onkeyup="followUp(this, '_tel5', 2)" /> -
            <input type="text" name="_tel5" size="2" maxlength="2" value="{{$fonction->_tel5}}" class="num length|2" onkeyup="followUp(this, '_fax1', 2)" />
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$fonction field="fax" defaultFor="_fax1"}}</th>
          <td>
            <input type="text" name="_fax1" size="2" maxlength="2" value="{{$fonction->_fax1}}" class="num length|2" onkeyup="followUp(this, '_fax2', 2)" /> - 
            <input type="text" name="_fax2" size="2" maxlength="2" value="{{$fonction->_fax2}}" class="num length|2" onkeyup="followUp(this, '_fax3', 2)" /> -
            <input type="text" name="_fax3" size="2" maxlength="2" value="{{$fonction->_fax3}}" class="num length|2" onkeyup="followUp(this, '_fax4', 2)" /> -
            <input type="text" name="_fax4" size="2" maxlength="2" value="{{$fonction->_fax4}}" class="num length|2" onkeyup="followUp(this, '_fax5', 2)" /> -
            <input type="text" name="_fax5" size="2" maxlength="2" value="{{$fonction->_fax5}}" class="num length|2" />
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <button type="button" class="modify" onclick="submitForm(this.form)">
              {{tr}}Modify{{/tr}}
            </button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>