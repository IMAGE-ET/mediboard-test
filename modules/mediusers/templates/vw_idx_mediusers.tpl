<script type="text/javascript">

function loadProfil(type){
  var tabProfil = {{$tabProfil|@json}};

  // Liste des profils dispo pour le type selectionné
  var listProfil = tabProfil[type] || [];
  
  $A(document.mediuser._profile_id).each( function(input) {
    input.disabled = !listProfil.include(input.value) && input.value;
    input.selected = input.selected && !input.disabled;
  });  
}

var Functions = {
  collapse: function() {
  	$$("tbody.functionEffect").each(Element.hide);
  },
  
  expand: function() {
  	$$("tbody.functionEffect").each(Element.show);
  },
  
  initEffect: function(function_id) {
    new PairEffect("CFunctions-" + function_id, { 
      bStoreInCookie: false,
      bStartVisible: function_id == "{{$mediuserSel->function_id}}"
    } );
  }
}

{{if $mediuserSel->_id}}
Main.add(function () {
  loadProfil("{{$mediuserSel->_user_type}}");
});
{{/if}}

function changeRemote(o) {
  var oPassword = $(o.form._user_password);
  
  // can the user connect remotely ?
  var canRemote = $V(o)==0;
  
  // we change the form element's spec 
  oPassword.className = canRemote?
  '{{$mediuserSel->_props._user_password_strong}}':
  '{{$mediuserSel->_props._user_password_weak}}';
  
  {{if !$mediuserSel->user_id}}oPassword.addClassName('notNull');{{/if}}
  
  // we check the field
  checkFormElement(oPassword);
}

</script>

<table class="main">
  <tr>
    <td class="greedyPane">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;user_id=0" class="buttonnew">
        {{tr}}CMediusers-title-create{{/tr}}
      </a>
      
      <table class="tbl">
        <tr>
          <th style="width: 32px;">
            <img src="images/icons/collapse.gif" onclick="Functions.collapse()" alt="réduire" />
            <img src="images/icons/expand.gif"  onclick="Functions.expand()" alt="agrandir" />
          </th>
          <th>{{mb_title class=CUser field=user_username}}</th>
          <th>{{mb_title class=CUser field=user_last_name}}</th>
          <th>{{mb_title class=CUser field=user_first_name}}</th>
          <th>{{mb_title class=CUser field=user_type}}</th>
          <th>{{mb_title class=CUser field=profile_id}}</th>
          <th>{{mb_title class=CUser field=user_last_login}}</th>
        </tr>
        {{foreach from=$groups item=curr_group}}
        <tr>
          <th class="title" colspan="10">
            {{$curr_group->text}}
          </th>
        </tr>
        {{foreach from=$curr_group->_ref_functions item=_function}}
        <tr id="{{$_function->_guid}}-trigger">
          <td style="background-color: #{{$_function->color}}" />
          <td colspan="10">
            <strong>{{$_function->text}}</strong>
            ({{$_function->_ref_users|@count}})
          </td>
        </tr>
        
        <tbody class="functionEffect" id="{{$_function->_guid}}">
        
        <tr class="script">
        	<td>
        		<script type="text/javascript">Functions.initEffect({{$_function->_id}});</script>
        	</td>
        </tr>
        
        {{foreach from=$_function->_ref_users item=curr_user}}
        <tr {{if $curr_user->_id == $mediuserSel->_id}}class="selected"{{/if}}>
          <td style="background-color: #{{$_function->color}}" />

          {{assign var=user_id value=$curr_user->_id}}
          {{assign var="href" value="?m=$m&tab=$tab&user_id=$user_id"}}
        	{{if $curr_user->_ref_user->_id}}
	
	          <td><a href="{{$href}}">{{$curr_user->_user_username}}</a></td>
	          <td><a href="{{$href}}">{{$curr_user->_user_last_name}}</a></td>
	          <td><a href="{{$href}}">{{$curr_user->_user_first_name}}</a></td>
	
	          <td>
	          	{{assign var=type value=$curr_user->_user_type}}
	          	{{if array_key_exists($type, $utypes)}}{{$utypes.$type}}{{/if}}
	          </td>
	          
	          <td>
	          	{{$curr_user->_ref_profile->user_username}}
	          </td>
	          
	          <td>
	            {{if $curr_user->_user_last_login}}
	            <label title="{{mb_value object=$curr_user field=_user_last_login}}">
	              {{mb_value object=$curr_user field=_user_last_login format=relative}}
	            </label>
	          	{{/if}}
	          </td>

					{{else}}
					  <td colspan="10">
					  	<div class="little-warning">
					  	  Pas d'utilisateur <em>core</em> pour 
					  	  <a class="action" href="{{$href}}">ce Mediuser</a>.
					  	</div>
					  </td>
					{{/if}}
          
        </tr>
        {{/foreach}}
        </tbody>
        {{/foreach}}
        {{/foreach}}
      </table>
    </td>
    <td class="pane">
    
      {{if $mediuserSel->_id}}
      <a class="buttonsearch" style="" href="?m=admin&amp;tab=view_edit_users&amp;user_username={{$mediuserSel->_user_username}}&amp;user_id={{$mediuserSel->_id}}">
        Administrer cet utilisateur
      </a>
      {{/if}}
      <form name="mediuser" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">

      <input type="hidden" name="dosql" value="do_mediusers_aed" />
      <input type="hidden" name="user_id" value="{{$mediuserSel->_id}}" />
      <input type="hidden" name="del" value="0" />

      <table class="form">
        <tr>
          {{if $mediuserSel->_id}}
          <th class="title modify" colspan="2">
            <div class="idsante400" id="CMediusers-{{$mediuserSel->_id}}"></div>
            
            <a style="float:right;" href="#" onclick="view_log('CMediusers',{{$mediuserSel->user_id}})">
              <img src="images/icons/history.gif" alt="historique" title="historique"/>
            </a>
            
						{{tr}}CMediusers-title-modify{{/tr}} 
						'{{$mediuserSel->_user_username}}'
          </th>
						
          {{else}}
          <th class="title" colspan="2">
            <input type="hidden" name="_user_type" value="0" />
            {{tr}}CMediusers-title-create{{/tr}}
          </th>
          {{/if}}
        </tr>

        <tr>
          <th>{{mb_label object=$mediuserSel field="_user_username"}}</th>
          <td>{{mb_field object=$mediuserSel field="_user_username"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="_user_password"}}</th>
          <td>
	          <input type="password" name="_user_password" class="{{$mediuserSel->_props._user_password}}{{if !$mediuserSel->user_id}} notNull{{/if}}" onkeyup="checkFormElement(this);" value="" />
	          <span id="mediuser__user_password_message"></span>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="_user_password2"}}</th>
          <td><input type="password" name="_user_password2" class="password sameAs|_user_password" value="" /></td>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="actif"}}</th>
          <td>{{mb_field object=$mediuserSel field="actif"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$mediuserSel field="deb_activite"}}</th>
          <td class="date">
            {{mb_field object=$mediuserSel field="deb_activite" form="mediuser" register=true}}
          </td>
        </tr>
    
        <tr>
          <th>{{mb_label object=$mediuserSel field="fin_activite"}}</th>
          <td class="date">
            {{mb_field object=$mediuserSel field="fin_activite" form="mediuser" register=true}}
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$mediuserSel field="remote"}}</th>
          <td>{{mb_field object=$mediuserSel field="remote" onchange="changeRemote(this)"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="function_id"}}</th>
          <td>
            <select name="function_id" style="width: 150px;" class="{{$mediuserSel->_props.function_id}}">
              <option value="">&mdash; Choisir une fonction</option>
              {{foreach from=$groups item=curr_group}}
              <optgroup label="{{$curr_group->text}}">
              {{foreach from=$curr_group->_ref_functions item=_function}}
              <option class="mediuser" style="border-color: #{{$_function->color}};" value="{{$_function->_id}}"
              	 {{if $_function->_id == $mediuserSel->function_id}} selected="selected" {{/if}}
              >
                {{$_function->text}}
              </option>
              {{foreachelse}}
              <option value="" disabled="disabled">
              	{{tr}}CFunctions.none{{/tr}}
              </option>
              
              {{/foreach}}
              </optgroup>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="discipline_id"}}</th>
          <td>
            <select name="discipline_id" style="width: 150px;" class="{{$mediuserSel->_props.discipline_id}}">
              <option value="">&mdash; Choisir une spécialité</option>
              {{foreach from=$disciplines item=curr_discipline}}
              <option value="{{$curr_discipline->discipline_id}}" {{if $curr_discipline->discipline_id == $mediuserSel->discipline_id}} selected="selected" {{/if}}>
                {{$curr_discipline->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="spec_cpam_id"}}</th>
          <td>
            <select name="spec_cpam_id" style="width: 150px;" class="{{$mediuserSel->_props.spec_cpam_id}}">
              <option value="">&mdash; Choisir une spécialité</option>
              {{foreach from=$spec_cpam item=curr_spec}}
              <option value="{{$curr_spec->spec_cpam_id}}" {{if $curr_spec->spec_cpam_id == $mediuserSel->spec_cpam_id}} selected="selected" {{/if}}>
                {{$curr_spec->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
        <th>{{mb_label object=$mediuserSel field="_user_type"}}</th>
          <td>
            <select name="_user_type"  style="width: 150px;" class="{{$mediuserSel->_props._user_type}}" onchange="loadProfil(this.value)">
            {{foreach from=$utypes key=curr_key item=type}}
              <option value="{{if $curr_key != 0}}{{$curr_key}}{{/if}}" {{if $curr_key == $mediuserSel->_user_type}}selected="selected"{{/if}}>{{$type}}</option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="_profile_id"}}</th>
          <td>
            <select name="_profile_id" style="width: 150px;">
              <option value="">&mdash; Choisir un profil</option>
              {{foreach from=$profiles item=curr_profile}}
              <option value="{{$curr_profile->user_id}}" {{if $curr_profile->user_id == $mediuserSel->_profile_id}} selected="selected" {{/if}}>{{$curr_profile->user_username}}</option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="_user_last_name"}}</th>
          <td>{{mb_field object=$mediuserSel field="_user_last_name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="_user_first_name"}}</th>
          <td>{{mb_field object=$mediuserSel field="_user_first_name"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="compte"}}</th>
          <td>{{mb_field object=$mediuserSel field="compte"}}</td>
        </tr>
        
        {{if is_array($banques)}}
        <!-- Choix de la banque quand disponible -->
        <tr>
          <th>{{mb_label object=$mediuserSel field="banque_id"}}</th>
          <td>
	          <select name="banque_id" style="width: 150px;">
		          <option value="">&mdash; Choix d'une banque</option>
		          {{foreach from=$banques item="banque"}}
	            <option value="{{$banque->_id}}" {{if $mediuserSel->banque_id == $banque->_id}}selected = "selected"{{/if}}>
	            	{{$banque->_view}}
	            </option>
		          {{/foreach}}
	          </select>
          </td>
        </tr>
        {{/if}}

        <tr>
          <th>{{mb_label object=$mediuserSel field="adeli"}}</th>
          <td>{{mb_field object=$mediuserSel field="adeli"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$mediuserSel field="titres"}}</th>
          <td>{{mb_field object=$mediuserSel field="titres"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$mediuserSel field="_user_email"}}</th>
          <td>{{mb_field object=$mediuserSel field="_user_email"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$mediuserSel field="_user_phone"}}</th>
          <td>{{mb_field object=$mediuserSel field="_user_phone"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$mediuserSel field="commentaires"}}</th>
          <td>{{mb_field object=$mediuserSel field="commentaires"}}</td>
        </tr>
        
        <tr>
          <td class="button" colspan="2">
            {{if $mediuserSel->user_id}}
            <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'utilisateur',objName:'{{$mediuserSel->_user_username|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
            {{else}}
            <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>