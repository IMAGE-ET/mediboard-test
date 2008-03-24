<table class="main">
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th class="title">
            {{if $user->template}}
            Profil utilisateur '{{$user->_view}}' 
            {{else}}
              Utilisateur '{{$user->_view}}' 
              &mdash; basé sur
              {{if $profile->_id}} 
              le profil 
              <a class="action" href="?m={{$m}}&amp;tab={{$tab}}&user_id={{$profile->_id}}">
	              '{{$profile->user_username}}'
	            </a>
              {{else}}
              aucun profil  
              {{/if}}
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