<table class="main">
  <tr>
    <td>
    Type d'affichage
      <form action="?m={{$m}}" method="post">
        <select name="typeVue" onchange="form.submit()">
          <option value="profil" {{if $typeVue == "profil"}}selected = "selected"{{/if}}>Profil</option>
          <option value="user" {{if $typeVue == "user"}}selected = "selected"{{/if}}>Utilisateur</option>
          <option value="resultat" {{if $typeVue == "resultat"}}selected = "selected"{{/if}}>Résultat</option>
        </select>
      </form>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th class="title">
            Utilisateur : {{$user->_view}} 
            {{if $profile->_id}}
              ({{$profile->user_username}})
            {{else}}
              (Aucun profil)  
            {{/if}}
          </th>
        </tr>
      </table>
    </td>
  </tr>
  {{if $typeVue=="user"}}
    {{assign var="listPermsModules" value=$listPermsModulesUser}}
    {{assign var="listPermsObjects" value=$listPermsObjectsUser}}
  {{/if}}
  {{if $typeVue=="profil"}}
    {{assign var="listPermsModules" value=$listPermsModulesProfil}}
    {{assign var="listPermsObjects" value=$listPermsObjectsProfil}}
  {{/if}}
  {{if $typeVue=="resultat"}}
    {{assign var="listPermsModules" value=$listPermsModulesResultat}}
    {{assign var="listPermsObjects" value=$listPermsObjectsResultat}}
  {{/if}}
  <tr>
    <td class="halfPane">
      {{include file="inc_perms_modules.tpl" type=$typeVue}}
    </td>
    <td class="halfPane">
      {{include file="inc_perms_objects.tpl" type=$typeVue}}
    </td>
  </tr>
</table>