{{mb_include_script module="dPpatients" script="autocomplete"}}
{{mb_include_script module="mediusers" script="color_selector"}}
{{mb_include_script module="system" script="object_selector"}}

<script type="text/javascript">
Main.add(function () {
  initInseeFields("editFrm", "cp", "ville");
});

Main.add(function () {
  Control.Tabs.create('tab_user', true);
});

ColorSelector.init = function(){
  this.sForm  = "editFrm";
  this.sColor = "color";
  this.pop();
}
</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="2">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;function_id=0" class="button new">
        Créer une fonction
      </a>
      <table class="tbl">
      {{foreach from=$listGroups item=curr_group}}
        <tr>
          <th>Etablissement {{$curr_group->text}} &mdash; {{$curr_group->_ref_functions|@count}} fonction(s)</th>
          <th>Type</th>
          <th>Utilisateurs</th>
        </tr>
        {{foreach from=$curr_group->_ref_functions item=curr_function}}
        <tr {{if $curr_function->_id == $userfunction->_id}}class="selected"{{/if}}>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;function_id={{$curr_function->_id}}">
              {{$curr_function->text}}
            </a>
          </td>
          <td>
            {{tr}}CFunctions.type.{{$curr_function->type}}{{/tr}}
          </td>
          <td style="background: #{{$curr_function->color}}">
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;function_id={{$curr_function->_id}}">
              {{$curr_function->_ref_users|@count}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      {{/foreach}}
      </table>
    </td>
    <td class="halfPane" style="height: 1%">
    <form name="editFrm" action="?m={{$m}}" method="post" onSubmit="return checkForm(this)">
      <input type="hidden" name="m" value="mediusers" />
      <input type="hidden" name="dosql" value="do_functions_aed" />
      <input type="hidden" name="function_id" value="{{$userfunction->function_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $userfunction->function_id}}
          <th class="title modify" colspan="2">
            <a style="float:right;" href="#" onclick="view_log('CFunctions',{{$userfunction->function_id}})">
              <img src="images/icons/history.gif" alt="historique" />
            </a>
            Modification de la fonction &lsquo;{{$userfunction->text}}&rsquo;
          </th>
          {{else}}
          <th class="title" colspan="2">
            <a style="float:right;" href="#" onclick="view_log('CFunctions',{{$userfunction->function_id}})">
              <img src="images/icons/history.gif" alt="historique" />
            </a>
            {{tr}}Création d'une fonction{{/tr}}
          </th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="text"}}</th>
          <td>{{mb_field object=$userfunction field="text"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="soustitre"}}</th>
          <td>{{mb_field object=$userfunction field="soustitre"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="compta_partagee"}}</th>
          <td>{{mb_field object=$userfunction field="compta_partagee"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="group_id"}}</th>
          <td>
            <select name="group_id" class="{{$userfunction->_props.group_id}}">
              <option value="">&mdash; {{tr}}CGroups.select{{/tr}}</option>
              {{foreach from=$listGroups item=curr_group}}
              <option value="{{$curr_group->group_id}}" {{if $curr_group->group_id == $userfunction->group_id}} selected="selected" {{/if}}>
                {{$curr_group->text}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="type"}}</th>
          <td>{{mb_field object=$userfunction field="type"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="color"}}</th>
          <td>
            <a href="#1" id="select_color" style="background: #{{$userfunction->color}}; padding: 0 3px; border: 1px solid #aaa;" onclick="ColorSelector.init()">Cliquer pour changer</a>
            {{mb_field object=$userfunction field="color" hidden=1}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="adresse"}}</th>
          <td>{{mb_field object=$userfunction field="adresse"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="cp"}}</th>
          <td>
            {{mb_field object=$userfunction field="cp"}}
            <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="ville"}}</th>
          <td>
            {{mb_field object=$userfunction field="ville"}}
            <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="tel"}}</th>
          <td>{{mb_field object=$userfunction field="tel"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$userfunction field="fax"}}</th>
          <td>{{mb_field object=$userfunction field="fax"}}</td>
        </tr>
        
        <tr>
          <td class="button" colspan="2">
          {{if $userfunction->function_id}}
            <button class="modify" type="submit">Valider</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la fonction',objName:'{{$userfunction->text|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
          {{else}}
            <button class="submit" name="btnFuseAction" type="submit">Créer</button>
          {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  {{if $userfunction->function_id}}
  <tr>
    <td>
      <ul id="tab_user" class="control_tabs">
        <li>
          <a href="#list-primary-users" id="list-primary-users-title">
            Utilisateurs principaux <small>({{$userfunction->_back.users|@count}})</small>
          </a>
        </li>
        <li>
          <a href="#list-secondary-users" id="list-secondary-users-title">
            Utilisateurs secondaires <small>({{$userfunction->_back.secondary_functions|@count}})</small>
          </a>
        </li>
      </ul>
      <hr class="control_tabs" />
      <div id="list-primary-users" style="display: none;">
        <table class="tbl">
          <tr>
            <th>{{mb_title class=CUser field=user_username}}</th>
            <th>{{mb_title class=CUser field=user_last_name}}</th>
            <th>{{mb_title class=CUser field=user_first_name}}</th>
            <th>{{mb_title class=CUser field=user_type}}</th>
            <th>{{mb_title class=CUser field=profile_id}}</th>
            <th>{{mb_title class=CUser field=user_last_login}}</th>
          </tr>
          {{foreach from=$userfunction->_back.users item=curr_user}}
          <tr>
            {{assign var=user_id value=$curr_user->_id}}
            {{assign var="href" value="?m=mediusers&tab=vw_idx_mediusers&user_id=$user_id"}}
	          <td><a href="{{$href}}">{{$curr_user->_user_username}}</a></td>
	          <td><a href="{{$href}}">{{$curr_user->_user_last_name}}</a></td>
	          <td><a href="{{$href}}">{{$curr_user->_user_first_name}}</a></td>
            <td>
	          	{{assign var=type value=$curr_user->_user_type}}
	          	{{if array_key_exists($type, $utypes)}}{{$utypes.$type}}{{/if}}
	          </td>
	          <td>{{$curr_user->_ref_profile->user_username}}</td>
	          <td>
	            {{if $curr_user->_user_last_login}}
	            <label title="{{mb_value object=$curr_user field=_user_last_login}}">
	              {{mb_value object=$curr_user field=_user_last_login format=relative}}
	            </label>
	          	{{/if}}
	          </td>
          </tr>
          {{foreachelse}}
          <tr>
            <td colspan="6">Aucun utilisateur principal</td>
          </tr>
          {{/foreach}}
        </table>
      </div>
      <div id="list-secondary-users" style="display: none;">
        
	      <form name="addSecUser" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_secondary_function_aed" />
	    	<input type="hidden" name="secondary_function_id" value="" />
	    	<input type="hidden" name="function_id" value="{{$userfunction->_id}}" />
		    <input type="hidden" name="del" value="0" />
    		<table class="form">
		      <tr>
		        <th class="title" colspan="2">
		          Ajout d'un utilisateur
		        </th>
		      </tr>
		  
	    	  <tr>
		        <th>{{mb_label object=$secondary_function field="user_id"}}</th>
            <td>
              <input type="text" name="user_id" class="notNull" value=""/>
              <input type="hidden" name="object_class" value="CMediusers" />
              <button class="search" type="button" onclick="ObjectSelector.initEdit()">Chercher</button>
              <script type="text/javascript">
               ObjectSelector.initEdit = function(){
                  this.sForm     = "addSecUser";
                  this.sId       = "user_id";
                  this.sClass    = "object_class";  
                  this.onlyclass = "true";
                  this.pop();
                }
              </script>
            </td>
		      </tr>
    		  <tr>
		        <td class="button" colspan="2">
		          <button class="submit" name="btnFuseAction" type="submit">Créer</button>
		        </td>
		      </tr>
		    </table>
        </form>
        
        <table class="tbl">
          <tr>
            <th>{{mb_title class=CUser field=user_username}}</th>
            <th>{{mb_title class=CUser field=user_last_name}}</th>
            <th>{{mb_title class=CUser field=user_first_name}}</th>
            <th>{{mb_title class=CUser field=user_type}}</th>
            <th>{{mb_title class=CUser field=profile_id}}</th>
            <th>{{mb_title class=CUser field=user_last_login}}</th>
          </tr>
          {{foreach from=$userfunction->_back.secondary_functions item=curr_function}}
          <tr>
            {{assign var=user_id value=$curr_function->_ref_user->_id}}
            {{assign var="href" value="?m=mediusers&tab=vw_idx_mediusers&user_id=$user_id"}}
	          <td><a href="{{$href}}">{{$curr_function->_ref_user->_user_username}}</a></td>
	          <td><a href="{{$href}}">{{$curr_function->_ref_user->_user_last_name}}</a></td>
	          <td><a href="{{$href}}">{{$curr_function->_ref_user->_user_first_name}}</a></td>
            <td>
	          	{{assign var=type value=$curr_function->_ref_user->_user_type}}
	          	{{if array_key_exists($type, $utypes)}}{{$utypes.$type}}{{/if}}
	          </td>
	          <td>{{$curr_function->_ref_user->_ref_profile->user_username}}</td>
	          <td>
	            {{if $curr_function->_ref_user->_user_last_login}}
	            <label title="{{mb_value object=$curr_function->_ref_user field=_user_last_login}}">
	              {{mb_value object=$curr_function->_ref_user field=_user_last_login format=relative}}
	            </label>
	          	{{/if}}
	          </td>
          </tr>
          {{foreachelse}}
          <tr>
            <td colspan="6">Aucun utilisateur secondaire</td>
          </tr>
          {{/foreach}}
        </table>
      </div>
    </td>
  </tr>
  {{/if}}
</table>