{{mb_include_script module="dPpatients" script="autocomplete"}}

<script type="text/javascript">
Main.add(function () {
  initInseeFields("editFct", "cp", "ville");
  var tabs = Control.Tabs.create('tab_edit_mediuser', true);
  
  var url = new Url("admin", "edit_prefs");
  url.addParam("user_id", "{{$user->user_id}}");
  url.requestUpdate("edit-preferences");
});
</script>

<ul id="tab_edit_mediuser" class="control_tabs">
  <li><a href="#edit-mediuser">{{tr}}Account{{/tr}}</a></li>
  <li><a href="#edit-preferences">{{tr}}Preferences{{/tr}}</a></li>
</ul>
<hr class="control_tabs" />

<div id="edit-mediuser" style="display: none;">
<table class="main">
  <tr>
    <td class="halfPane">
      <form name="editUser" action="?m={{$m}}&amp;a=edit_infos" method="post" onsubmit="return onSubmitFormAjax(this);">
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
          <td>{{mb_field object=$user field="compte"}}</td>
        </tr>
        
        {{if $banques}}
        <!-- Choix de la banque quand disponible -->
        <tr>
          <th>{{mb_label object=$user field="banque_id"}}</th>
          <td>
	          <select name="banque_id">
		          <option value="">&mdash; Choix d'une banque</option>
		          {{foreach from=$banques item="banque"}}
	            <option value="{{$banque->_id}}" {{if $user->banque_id == $banque->_id}}selected = "selected"{{/if}}>{{$banque->_view}}</option>
	  	        {{/foreach}}
	          </select>
          </td>
        </tr>
        {{/if}}
                
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
            <button type="submit" class="modify">{{tr}}Modify{{/tr}}</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
    
    <td class="halfPane">
      <form name="editFct" action="?m={{$m}}" method="post" onSubmit="return onSubmitFormAjax(this)">
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
          <th>{{mb_label object=$fonction field="tel"}}</th>
          <td>{{mb_field object=$fonction field="tel"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fonction field="fax"}}</th>
          <td>{{mb_field object=$fonction field="fax"}}</td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <button type="submit" class="modify">{{tr}}Modify{{/tr}}</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>
</div>

<div id="edit-preferences" style="display: none;"></div>
