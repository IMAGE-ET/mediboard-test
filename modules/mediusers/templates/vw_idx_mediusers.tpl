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
    idStartVisible: "function{{$mediuserSel->function_id}}",
    sEffect: "appear"
  });
  regFieldCalendar("mediuser", "deb_activite");
  regFieldCalendar("mediuser", "fin_activite");
}

function deldate(sField){
  oForm = document.mediuser;
  ElemField = eval("oForm."+sField);
  ElemField.value = "";
  oDateDiv = $("mediuser_"+sField+"_da");
  oDateDiv.innerHTML = "";
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
        {{foreach from=$groups item=curr_group}}
        <tr>
          <th class="title" colspan="5">
            {{$curr_group->text}}
          </th>
        </tr>
        {{foreach from=$curr_group->_ref_functions item=curr_function}}
        <tr id="function{{$curr_function->function_id}}-trigger">
          <td style="background-color: #{{$curr_function->color}}">
          </td>
          <td colspan="3">
            <strong>{{$curr_function->text}}</strong>
          </td>
          <td>
            {{$curr_function->_ref_users|@count}} utilisateur(s)
          </td>
        </tr>
        <tbody class="functionEffect" id="function{{$curr_function->function_id}}" style="display:none;">
        {{foreach from=$curr_function->_ref_users item=curr_user}}
        <tr>
          <td style="background-color: #{{$curr_function->color}}"></td>
          {{assign var=user_id value=$curr_user->user_id}}
          {{assign var="href" value="index.php?m=$m&tab=$tab&user_id=$user_id"}}
          <td><a href="{{$href}}">{{$curr_user->_user_username}}</a></td>
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
      <form name="mediuser" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_mediusers_aed" />
      <input type="hidden" name="user_id" value="{{$mediuserSel->user_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
            {{if $mediuserSel->user_id}}
            {{if $canReadSante400}}
            <a style="float:right;" href="#" onclick="view_idsante400('CMediusers',{{$mediuserSel->user_id}})">
              <img src="images/sante400.gif" alt="Sante400" title="Identifiant sante 400"/>
            </a>
            {{/if}}
            <a style="float:right;" href="#" onclick="view_log('CMediusers',{{$mediuserSel->user_id}})">
              <img src="images/history.gif" alt="historique" title="historique"/>
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
          <td><input type="password" name="_user_password" title="{{$mediuserSel->_user_props._user_password}}{{if !$mediuserSel->user_id}}|notNull{{/if}}" value="" /></td>
        </tr>
        <tr>
          <th><label for="_user_password2" title="Re-saisir le mot de passe pour confimer. Obligatoire">Mot de passe (vérif.)</label></th>
          <td><input type="password" name="_user_password2" title="str|sameAs|_user_password" value="" /></td>
        </tr>
        <tr>
          <th><label for="actif_1" title="Permet ou non à d'activer le compte utilisateur">Compte actif</label></th>
          <td>
            <input type="radio" name="actif" value="1" {{if $mediuserSel->actif == "1" || !$mediuserSel->actif}} checked="checked" {{/if}} />
            <label for="actif_1" title="Compte activé">oui</label>
            <input type="radio" name="actif" value="0" {{if $mediuserSel->actif == "0"}} checked="checked" {{/if}} />
            <label for="actif_0" title="Compte désactivé">non</label>
          </td>
        </tr>
        
        <tr>
          <th><label for="deb_activite" title="Date de début d'activité">Début d'activité</label></th>
		  <td class="date">
		    <div id="mediuser_deb_activite_da">{{$mediuserSel->deb_activite|date_format:"%d/%m/%Y"}}</div>
		    <input type="hidden" name="deb_activite" title="date" value="{{$mediuserSel->deb_activite}}" />
		    <img id="mediuser_deb_activite_trigger" src="./images/calendar.gif" alt="Date de début d'activité"/>
		    <button class="cancel notext" type="button" onclick="deldate('deb_activite')"></button>
		  </td>
		</tr>
		
		<tr>
          <th><label for="fin_activite" title="Date de fin d'activité">Fin d'activité</label></th>
		  <td class="date">
		    <div id="mediuser_fin_activite_da">{{$mediuserSel->fin_activite|date_format:"%d/%m/%Y"}}</div>
		    <input type="hidden" name="fin_activite" title="date" value="{{$mediuserSel->fin_activite}}" />
		    <img id="mediuser_fin_activite_trigger" src="./images/calendar.gif" alt="Date de fin d'activité"/>
		    <button class="cancel notext" type="button" onclick="deldate('fin_activite')"></button>
		  </td>
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
              {{foreach from=$groups item=curr_group}}
              <optgroup label="{{$curr_group->text}}">
              {{foreach from=$curr_group->_ref_functions item=curr_function}}
              <option class="mediuser" style="border-color: #{{$curr_function->color}};" value="{{$curr_function->function_id}}" {{if $curr_function->function_id == $mediuserSel->function_id}} selected="selected" {{/if}}>
                {{$curr_function->text}}
              </option>
              {{/foreach}}
              </optgroup>
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
          <th><label for="spec_cpam_id" title="Spécialité CPAM de l'utilisateur. Optionnel">Spéc CPAM</label></th>
          <td>
            <select name="spec_cpam_id" title="{{$mediuserSel->_props.spec_cpam_id}}">
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
          <th><label for="_profile_id" title="Profil de droits utilisateur. Obligatoire">Profil</label></th>
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
          <th><label for="commentaires" title="Veuillez saisir un commentaire">Commentaires</label></th>
          <td><textarea name="commentaires" title="{{$mediuserSel->_props.commentaires}}">{{$mediuserSel->commentaires}}</textarea>
        </tr>
        <tr>
          <td class="button" colspan="2">
            {{if $mediuserSel->user_id}}
            <button class="modify" type="submit">Valider</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'utilisateur',objName:'{{$mediuserSel->_user_username|smarty:nodefaults|JSAttribute}}'})">
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