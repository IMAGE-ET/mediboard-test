{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tab_permissions', true);
});
</script>

<table class="main">
  <tr>
    <th class="title">
      {{if $user->template}}
      Profil utilisateur '{{$user}}' 
      {{else}}
        Utilisateur '{{$user}}' 
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
        {{assign var=usedProfile value=$user->template|ternary:$user:$profile}}
        {{if $usedProfile->_id}}
        <li>
          <a href="#profile">Utilisateurs basés sur le profil '{{$usedProfile->user_username}}' <small>(&ndash;)</small></a>
          <script>Main.add(Control.Tabs.setTabCount.curry('profile', '{{$profilesList|@count}}').bind(Control.Tabs));</script>
        </li>

        {{/if}}
      </ul>
      <hr class="control_tabs" />
      
      <div id="module" style="display: none;">
        {{mb_include template=inc_perms_modules}}
      </div>
      <div id="object" style="display: none;">
        {{mb_include template=inc_perms_objects}}
      </div>
      
      {{if $usedProfile->_id}}
      <table class="tbl main" id="profile" style="display: none;">
        {{foreach from=$profilesList item=_user name="users"}}
        <tr>
          {{if $user->_id == $_user->_id}}
          <td colspan="2"><strong>{{$_user}}</strong></td>
          {{else}}
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_users&amp;user_id={{$_user->_id}}" style="float: left;">
              {{$_user}}
            </a>
          </td>
          <td class="button narrow" style="white-space: nowrap;">
            <button class="search" onclick="window.location='?m={{$m}}&amp;tab=edit_perms&amp;user_id={{$_user->_id}}'">
              Droits
            </button>
            <button class="search" onclick="window.location='?m={{$m}}&amp;tab=edit_prefs&amp;user_id={{$_user->_id}}'">
              Préférences
            </button>
            {{include file="loginas.tpl" loginas_user=$_user}}
          </td>
          {{/if}}
        </tr>
        {{foreachelse}}
        <tr>
          <td class="empty">{{tr}}CUser.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </table>
      {{/if}}
    </td>
  </tr>
</table>