<script type="text/javascript">

var Functions = {
  collapse: function() {
    Element.hide.apply(null, $$("tbody.functionEffect"));
  },
  
  expand: function() {
    Element.show.apply(null, $$("tbody.functionEffect"));
  }
}

function deldate(sField){
  oForm = document.mediuser;
  ElemField = eval("oForm."+sField);
  ElemField.value = "";
  oDateDiv = $("mediuser_"+sField+"_da");
  oDateDiv.innerHTML = "";
}

function pageMain() {
  PairEffect.initGroup("functionEffect", { 
    bStoreInCookie: false,
    idStartVisible: "function{{$mediuserSel->function_id}}",
    sEffect: "appear"
  });
  regFieldCalendar("mediuser", "deb_activite");
  regFieldCalendar("mediuser", "fin_activite");
}

</script>

<table class="main">
  <tr>
    <td class="greedyPane">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;user_id=0" class="buttonnew">
        Créer un utilisateur
      </a>
      <table class="tbl">
        <tr>
          <th style="width: 32px;">
            <img src="images/icons/collapse.gif" onclick="Functions.collapse()" alt="r�duire" />
            <img src="images/icons/expand.gif"  onclick="Functions.expand()" alt="agrandir" />
          </th>
          <th>{{tr}}CMediusers-_user_username{{/tr}}</th>
          <th>{{tr}}CMediusers-_user_last_name{{/tr}}</th>
          <th>{{tr}}CMediusers-_user_first_name{{/tr}}</th>
          <th>{{tr}}CMediusers-_user_type{{/tr}}</th>
        </tr>
        {{foreach from=$groups item=curr_group}}
        <tr>
          <th class="title" colspan="5">
            {{$curr_group->text}}
          </th>
        </tr>
        {{foreach from=$curr_group->_ref_functions item=curr_function}}
        <tr id="function{{$curr_function->_id}}-trigger">
          <td style="background-color: #{{$curr_function->color}}">
          </td>
          <td colspan="4">
            <strong>{{$curr_function->text}}</strong>
            ({{$curr_function->_ref_users|@count}})
          </td>
        </tr>
        <tbody class="functionEffect" id="function{{$curr_function->_id}}" style="display:none;">
        {{foreach from=$curr_function->_ref_users item=curr_user}}
        <tr {{if $curr_user->_id == $mediuserSel->_id}}class="selected"{{/if}}>
          <td style="background-color: #{{$curr_function->color}}"></td>
          {{assign var=user_id value=$curr_user->_id}}
          {{assign var="href" value="?m=$m&tab=$tab&user_id=$user_id"}}
          <td>
            <a href="{{$href}}">
              {{$curr_user->_user_username}}
              {{if $curr_user->_user_last_login}}({{$curr_user->_user_last_login|date_format:"%d/%m/%Y %Hh%M"}}){{/if}}
            </a>
          </td>
          <td><a href="{{$href}}">{{$curr_user->_user_last_name}}</a></td>
          <td><a href="{{$href}}">{{$curr_user->_user_first_name}}</a></td>
          <td><a href="{{$href}}">{{$curr_user->_user_type}}</a></td>
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

      <form name="mediuser" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

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
            
            Modification de l'utilisateur &lsquo;{{$mediuserSel->_user_username}}&rsquo;
          {{else}}
          <th class="title" colspan="2">
            <input type="hidden" name="_user_type" value="0" />
          
            Création d'un nouvel utilisateur
          {{/if}}
          </th>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="_user_username"}}</th>
          <td>{{mb_field object=$mediuserSel field="_user_username"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="_user_password"}}</th>
          <td><input type="password" name="_user_password" class="{{$mediuserSel->_props._user_password}}{{if !$mediuserSel->user_id}} notNull{{/if}}" value="" /></td>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="_user_password2"}}</th>
          <td><input type="password" name="_user_password2" class="str sameAs|_user_password" value="" /></td>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="actif"}}</th>
          <td>{{mb_field object=$mediuserSel field="actif"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$mediuserSel field="deb_activite"}}</th>
          <td class="date">
            {{mb_field object=$mediuserSel field="deb_activite" form="mediuser"}}
          </td>
        </tr>
    
        <tr>
          <th>{{mb_label object=$mediuserSel field="fin_activite"}}</th>
          <td class="date">
            {{mb_field object=$mediuserSel field="fin_activite" form="mediuser"}}
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$mediuserSel field="remote"}}</th>
          <td>{{mb_field object=$mediuserSel field="remote"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="function_id"}}</th>
          <td>
            <select name="function_id" class="{{$mediuserSel->_props.function_id}}">
              <option value="">&mdash; Choisir une fonction &mdash;</option>
              {{foreach from=$groups item=curr_group}}
              <optgroup label="{{$curr_group->text}}">
              {{foreach from=$curr_group->_ref_functions item=curr_function}}
              <option class="mediuser" style="border-color: #{{$curr_function->color}};" value="{{$curr_function->_id}}" {{if $curr_function->_id == $mediuserSel->function_id}} selected="selected" {{/if}}>
                {{$curr_function->text}}
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
            <select name="discipline_id" class="{{$mediuserSel->_props.discipline_id}}">
              <option value="">&mdash; Choisir une spécialité &mdash;</option>
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
            <select name="spec_cpam_id" class="{{$mediuserSel->_props.spec_cpam_id}}">
              <option value="">&mdash; Choisir une spécialité &mdash;</option>
              {{foreach from=$spec_cpam item=curr_spec}}
              <option value="{{$curr_spec->spec_cpam_id}}" {{if $curr_spec->spec_cpam_id == $mediuserSel->spec_cpam_id}} selected="selected" {{/if}}>
                {{$curr_spec->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$mediuserSel field="_profile_id"}}</th>
          <td>
            <select name="_profile_id">
              <option value="">&mdash; Choisir un profil</option>
              {{foreach from=$profiles item=curr_profile}}
              <option value="{{$curr_profile->user_id}}">{{$curr_profile->user_username}}</option>
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
            <button class="modify" type="submit">Valider</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'utilisateur',objName:'{{$mediuserSel->_user_username|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
            {{else}}
            <button class="submit" type="submit">Cr�er</button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>