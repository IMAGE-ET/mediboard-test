<script type="text/javascript" src="modules/dPpatients/javascript/autocomplete.js?build={{$mb_version_build}}"></script>
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
     {{tr}}My Info{{/tr}}
   </th>
  </tr>
  
  <tr>
    <td class="halfPane">
      <form name="editUser" action="?m={{$m}}&amp;a=edit_infos" method="post" onsubmit="return checkForm(this);">
      <input type="hidden" name="dosql" value="do_mediusers_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="user_id" value="{{$user->user_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
            {{$user->_view}}
          </th>
        </tr>
        <tr>
          <th><label for="_user_last_name" title="Nom de famille de l'utilisateur. Obligatoire">Nom</label></th>
          <td><input type="text" name="_user_last_name" title="{{$user->_user_props._user_last_name}}" value="{{$user->_user_last_name}}" /></td>
        </tr>
        <tr>
          <th><label for="_user_first_name" title="Prénom de l'utilisateur">Prénom</label></th>
          <td><input type="text" name="_user_first_name"  title="{{$user->_user_props._user_first_name}}" value="{{$user->_user_first_name}}" /></td>
        </tr>
        <tr>
          <th><label for="discipline_id" title="Spécialité de l'utilisateur. Optionnel">Spécialité</label></th>
          <td>
            <select name="discipline_id" title="{{$user->_props.discipline_id}}">
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
          <th><label for="spec_cpam_id" title="Spécialité CPAM de l'utilisateur. Optionnel">Spéc CPAM</label></th>
          <td>
            <select name="spec_cpam_id" title="{{$user->_props.spec_cpam_id}}">
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
          <th><label for="adeli" title="Numero Adeli de l'utilisateur">Code Adeli</label></th>
          <td><input type="text" name="adeli" size="9" maxlength="9" title="{{$user->_props.adeli}}" value="{{$user->adeli}}" /></td>
        </tr>
        <tr>
          <th><label for="titres" title="Titres du médecin">Titres</label></th>
          <td><textarea name="titres" title="{{$user->_props.titres}}">{{$user->titres}}</textarea>
        </tr>
        <tr>
          <th><label for="_user_email" title="Email de l'utilisateur">Email</label></th>
          <td><input type="text" name="_user_email" title="{{$user->_user_props._user_email}}" value="{{$user->_user_email}}" /></td>
        </tr>
        <tr>
          <th><label for="_user_phone" title="Numéro de téléphone de l'utilisateur">Tél</label></th>
          <td><input type="text" name="_user_phone" title="{{$user->_user_props._user_phone}}" value="{{$user->_user_phone}}" /></td>
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
      <form name="editFct" action="./index.php?m={{$m}}" method="post" onSubmit="return checkForm(this)">
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
          <th>
            <label for="soustitre" title="Sous-titre de la fonction">Sous-titre</label>
          </th>
          <td>
            <textarea title="{{$fonction->_props.soustitre}}" name="soustitre">{{$fonction->soustitre}}</textarea>
          </td>
        </tr>
        <tr>
          <th>
            <label for="adresse" title="Veuillez saisir l'adresse du cabinet">Adresse</label>
          </th>
          <td>
            <textarea title="{{$fonction->_props.adresse}}" name="adresse">{{$fonction->adresse}}</textarea>
          </td>
        </tr>
        <tr>
          <th><label for="cp" title="Code postal">Code Postal</label></th>
          <td>
            <input size="31" maxlength="5" type="text" name="cp" value="{{$fonction->cp}}" title="{{$fonction->_props.cp}}" />
            <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th><label for="ville" title="Ville du cabinet">Ville</label></th>
          <td>
            <input size="31" type="text" name="ville" value="{{$fonction->ville}}" title="{{$fonction->_props.ville}}" />
            <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th><label for="_tel1" title="Numéro de téléphone filaire">Téléphone</label></th>
          <td>
            <input type="text" name="_tel1" size="2" maxlength="2" value="{{$fonction->_tel1}}" title="num|length|2" onkeyup="followUp(this, '_tel2', 2)" /> - 
            <input type="text" name="_tel2" size="2" maxlength="2" value="{{$fonction->_tel2}}" title="num|length|2" onkeyup="followUp(this, '_tel3', 2)" /> -
            <input type="text" name="_tel3" size="2" maxlength="2" value="{{$fonction->_tel3}}" title="num|length|2" onkeyup="followUp(this, '_tel4', 2)" /> -
            <input type="text" name="_tel4" size="2" maxlength="2" value="{{$fonction->_tel4}}" title="num|length|2" onkeyup="followUp(this, '_tel5', 2)" /> -
            <input type="text" name="_tel5" size="2" maxlength="2" value="{{$fonction->_tel5}}" title="num|length|2" onkeyup="followUp(this, '_fax1', 2)" />
          </td>
        </tr>
        <tr>
          <th><label for="_fax1" title="Numéro de fax">Télécopie</label></th>
          <td>
            <input type="text" name="_fax1" size="2" maxlength="2" value="{{$fonction->_fax1}}" title="num|length|2" onkeyup="followUp(this, '_fax2', 2)" /> - 
            <input type="text" name="_fax2" size="2" maxlength="2" value="{{$fonction->_fax2}}" title="num|length|2" onkeyup="followUp(this, '_fax3', 2)" /> -
            <input type="text" name="_fax3" size="2" maxlength="2" value="{{$fonction->_fax3}}" title="num|length|2" onkeyup="followUp(this, '_fax4', 2)" /> -
            <input type="text" name="_fax4" size="2" maxlength="2" value="{{$fonction->_fax4}}" title="num|length|2" onkeyup="followUp(this, '_fax5', 2)" /> -
            <input type="text" name="_fax5" size="2" maxlength="2" value="{{$fonction->_fax5}}" title="num|length|2" />
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