<table class="main">
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
  <tr>
    <td class="halfPane">
      {{include file="inc_perms_modules.tpl" listPermsModules=$listPermsModulesProfil type="profil"}}
    </td>
    <td class="halfPane">
      {{include file="inc_perms_objects.tpl" listPermsObjects=$listPermsObjectsProfil type="profil"}}
    </td>
  </tr>
</table>