<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tab_permissions', true);
});
</script>

{{if $user->template}}
  {{assign var=usedProfile value=$user}}
{{else}}
  {{assign var=usedProfile value=$profile}}
{{/if}}

<table class="main">
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
  <tr>
    <td>
      <ul id="tab_permissions" class="control_tabs">
        <li><a href="#module">Droits sur les modules</a></li>
        <li><a href="#object">Droits sur les objets</a></li>
        {{if $usedProfile->_id}}
        <li><a href="#profile">Utilisateurs basés sur le profil '{{$usedProfile->user_username}}'</a></li>
        {{/if}}
      </ul>
      <hr class="control_tabs" />
      
      <div id="module" style="display: none;">
        {{include file="inc_perms_modules.tpl" listPermsModules=$listPermsModulesProfil type="profil"}}
      </div>
      <div id="object" style="display: none;">
        {{include file="inc_perms_objects.tpl" listPermsObjects=$listPermsObjectsProfil type="profil"}}
      </div>
      
      {{if $usedProfile->_id}}
      {{assign var=numCols value=1}}
      {{math equation="100/$numCols" assign=width format="%.1f"}}
      <table class="tbl main" id="profile" style="display: none;">
        <tr>
        {{foreach from=$profilesList item=curr_user name="users"}}
          <td style="width: {{$width}}%; text-align: right;">
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;user_id={{$curr_user->_id}}" style="float: left;">
              {{$curr_user->_view}}
            </a>
            <button class="search" onclick="window.location='?m={{$m}}&amp;tab=edit_perms&amp;user_id={{$curr_user->_id}}'">
              Droits
            </button>
            <button class="search" onclick="window.location='?m={{$m}}&amp;tab=edit_prefs&amp;user_id={{$curr_user->_id}}'">
              Préférences
            </button>
            {{include file="loginas.tpl" loginas_user=$curr_user}}
          </td>
          {{if ($smarty.foreach.users.index % $numCols) == ($numCols-1) && !$smarty.foreach.users.last}}</tr><tr>{{/if}}
        {{/foreach}}
        </tr>
      </table>
      {{/if}}
    </td>
  </tr>
</table>