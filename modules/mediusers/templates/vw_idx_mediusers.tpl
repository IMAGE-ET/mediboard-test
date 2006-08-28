<script type="text/javascript">

function expandFunctions() {
  document.getElementsByClassName("functionEffect").each( function(oElement) {
    Element.show(oElement);
  });
}

function collapseFunctions() {
  document.getElementsByClassName("functionEffect").each( function(oElement) {
    Element.hide(oElement);
  });
}

function pageMain() {
  PairEffect.initGroup("functionEffect", { 
    bStoreInCookie: false,
    idStartVisible: "function{{$mediuserSel->function_id}}"
  });
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
            <img src="modules/{{$m}}/images/collapse.gif" onclick="collapseFunctions()" alt="réduire" />
            <img src="modules/{{$m}}/images/expand.gif"  onclick="expandFunctions()" alt="agrandir" />
          </th>
          <th>Utilisateur</th>
          <th>Nom</th>
          <th>Prénom</th>
          <th>Type</th>
        </tr>
        {{foreach from=$functions item=curr_function}}
        <tr id="function{{$curr_function->function_id}}-trigger">
          <td style="background-color: #{{$curr_function->color}}">
          </td>
          <td colspan="4" style="background: #{{$curr_function->color}}" >
            <strong>{{$curr_function->text}}</strong> -
            {{$curr_function->_ref_users|@count}} utilisateur(s) -
            Etablissement {{$curr_function->_ref_group->text}}
          </td>
        </tr>
        <tbody class="functionEffect" id="function{{$curr_function->function_id}}">
        {{foreach from=$curr_function->_ref_users item=curr_user}}
        <tr>
          <td style="background-color: #{{$curr_function->color}}"></td>
          {{eval var=$curr_user->user_id assign=user_id}}
          {{assign var="href" value="index.php?m=$m&amp;tab=$tab&amp;user_id=$user_id"}}
          <td><a href="{{$href}}">{{$curr_user->_user_username}}</a></td>
          <td><a href="{{$href}}">{{$curr_user->_user_last_name}}</a></td>
          <td><a href="{{$href}}">{{$curr_user->_user_first_name}}</a></td>
          <td><a href="{{$href}}">{{$curr_user->_user_type}}</a></td>
        </tr>
        {{/foreach}}
        </tbody>
        {{/foreach}}
      </table>
    </td>
    <td class="pane">
      <form name="mediuser" action="./index.php?m={{$m}}" method="post" onSubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_mediusers_aed" />
      <input type="hidden" name="user_id" value="{{$mediuserSel->user_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
            {{if $mediuserSel->user_id}}
            <a style="float:right;" href="javascript:view_log('CMediusers',{{$mediuserSel->user_id}})">
              <img src="images/history.gif" alt="historique" />
            </a>
            Modification de l'utilisateur &lsquo;{{$mediuserSel->_user_username}}&rsquo;
            {{else}}
            Création d'un nouvel utilisateur
            {{/if}}
          </th>
        </tr>
        <tr>
          <th><label for="_user_username" title="Nom du compte pour se connecter à Mediboard. Obligatoire">Login</label></th>
          <td><input type="text" name="_user_username" title="{{$mediuserSel->_user_props._user_username}}" value="{{$mediuserSel->_user_username}}" /></td>
        </tr>
        <tr>
          <th><label for="_user_password" title="Mot de passe pour se connecter à Mediboard. Obligatoire">Mot de passe</label></th>
          <td><input type="password" name="_user_password" title="notNull|str" value="{{$mediuserSel->_user_password}}" /></td>
        </tr>
        <tr>
          <th><label for="_user_password2" title="Re-saisir le mot de passe pour confimer. Obligatoire">Mot de passe (vérif.)</label></th>
          <td><input type="password" name="_user_password2" title="notNull|str|sameAs|_user_password" value="{{$mediuserSel->_user_password}}" /></td>
        </tr>
        <tr>
          <th><label for="remote_0" title="Permet ou non à l'utilisateur de se connecter à distance">Accès distant</label></th>
          <td>
            <input type="radio" name="remote" value="0" {{if $mediuserSel->remote == "0"}} checked="checked" {{/if}} />
            <label for="remote_0" title="Accès distant authorisé">oui</label>
            <input type="radio" name="remote" value="1" {{if $mediuserSel->remote == "1"}} checked="checked" {{/if}} />
            <label for="remote_1" title="Accès distant interdit">non</label>
          </td>
        </tr>
        <tr>
          <th><label for="function_id" title="Fonction de l'utilisateur au sein de l'établissement. Obligatoire">Fonction</label></th>
          <td>
            <select name="function_id" title="{{$mediuserSel->_props.function_id}}">
              <option value="">&mdash; Choisir une fonction &mdash;</option>
              {{foreach from=$functions item=curr_function}}
              <option value="{{$curr_function->function_id}}" {{if $curr_function->function_id == $mediuserSel->function_id}} selected="selected" {{/if}}>
                {{$curr_function->text}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="discipline_id" title="Spécialité de l'utilisateur. Optionnel">Spécialité</label></th>
          <td>
            <select name="discipline_id" title="{{$mediuserSel->_props.discipline_id}}">
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
          <th><label for="_profile_id" title="Profil de droits utilisateur. Obligatoire">Profil</label></th>
          <td>
            <select name="_profile_id">
              <option value="0">&mdash; Choisir un profil</option>	
              {{foreach from=$profiles item=curr_profile}}
              <option value="{{$curr_profile->user_id}}">{{$curr_profile->user_username}}</option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="_user_last_name" title="Nom de famille de l'utilisateur. Obligatoire">Nom</label></th>
          <td><input type="text" name="_user_last_name" title="{{$mediuserSel->_user_props._user_last_name}}" value="{{$mediuserSel->_user_last_name}}" /></td>
        </tr>
        <tr>
          <th><label for="_user_first_name" title="Prénom de l'utilisateur">Prénom</label></th>
          <td><input type="text" name="_user_first_name"  title="{{$mediuserSel->_user_props._user_first_name}}" value="{{$mediuserSel->_user_first_name}}" /></td>
        </tr>
        <tr>
          <th><label for="adeli" title="Numero Adeli de l'utilisateur">Code Adeli</label></th>
          <td><input type="text" name="adeli" size="9" maxlength="9" title="{{$mediuserSel->_props.adeli}}" value="{{$mediuserSel->adeli}}" /></td>
        </tr>
        <tr>
          <th><label for="_user_email" title="Email de l'utilisateur">Email</label></th>
          <td><input type="text" name="_user_email" title="{{$mediuserSel->_user_props._user_email}}" value="{{$mediuserSel->_user_email}}" /></td>
        </tr>
        <tr>
          <th><label for="_user_phone" title="Numéro de téléphone de l'utilisateur">Tél</label></th>
          <td><input type="text" name="_user_phone" title="{{$mediuserSel->_user_props._user_phone}}" value="{{$mediuserSel->_user_phone}}" /></td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            {{if $mediuserSel->user_id}}
            <button class="modify" type="submit">Valider</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'utilisateur',objName:'{{$mediuserSel->_user_username|escape:javascript}}'})">
              Supprimer
            </button>
            {{else}}
            <button class="submit" type="submit">Créer</button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>