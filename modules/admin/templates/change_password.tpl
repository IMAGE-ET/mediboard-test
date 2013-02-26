{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  goHome = function(){
    location.replace("?");
  }

  checkPasswd = function(form) {
    if (($V(form.old_pwd) != $V(form.new_pwd1)) || (!$V(form.new_pwd2))) {
      return onSubmitFormAjax(form);
    }
    alert("Le nouveau mot de passe doit être différent du précédent.");
    return false;
  }
</script>

{{if $user->_ldap_linked && !$conf.admin.LDAP.allow_change_password}}
  <div class="small-warning">{{tr}}CUser_associate-ldap-no-password-change{{/tr}}</div>
{{elseif !$user->canChangePassword()}}
  <div class="small-warning">{{tr}}CUser-password_change_forbidden{{/tr}}</div>
{{else}}
  <form name="chpwdFrm" method="post" onsubmit="return checkPasswd(this)">
    <input type="hidden" name="m" value="admin" />
    <input type="hidden" name="dosql" value="do_chpwd_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="callback" value="{{if $forceChange}}goHome{{else}}Control.Modal.close{{/if}}" />
  
    {{if !$forceChange}}
      <input type="hidden" name="dialog" value="1" />
    {{else}}
      <div class="big-warning">
        <strong>Votre mot de passe ne correspond pas aux critères de sécurité de Mediboard</strong>. 
        Vous ne pourrez pas accéder à Mediboard tant que vous ne l'aurez pas changé afin qu'il respecte ces critères.
        La sécurité des informations de vos patients en dépend.<br />
        Pour plus de précisions, veuillez vous référer aux 
        <a href="http://mediboard.org/public/Recommandations+de+la+CNIL" target="_blank"> recommandations de la CNIL</a>.
        {{if $lifeDuration}}
          <br /><br />
          <strong>Votre mot de passe n'a pas été changé depuis au moins {{tr}}config-admin-CUser-password_life_duration-{{$lifetime}}{{/tr}}.</strong>
        {{/if}}
      </div>
    {{/if}}

    <table class="form">
      <tr>
        <th style="width: 50%;">
          <label for="old_pwd" title="{{tr}}CUser-user_password-current{{/tr}}">
            {{tr}}CUser-user_password-current{{/tr}}
          </label>
        </th>
        <td style="width: 50%;">
          <input class="notNull str" type="password" name="old_pwd" />
        </td>
      </tr>
      
      <tr>
        <th>
          <label for="new_pwd1" title="{{tr}}CUser-user_password-new{{/tr}}">
            {{tr}}CUser-user_password-new{{/tr}}
          </label>
        </th>
        <td class="text">
          <input type="hidden" name="user_username" value="{{$user->user_username}}" />
          <input class="{{$user->_props._user_password}}" type="password" name="new_pwd1" onkeyup="checkFormElement(this);" />
        </td>
      </tr>
      
      <tr>
        <th>
          <label for="new_pwd2" title="{{tr}}Repeat New Password{{/tr}}">
            {{tr}}Repeat new password{{/tr}}
          </label>
        </th>
        <td>
          <input class="notNull password sameAs|new_pwd1" type="password" name="new_pwd2" />
        </td>
      </tr>
      
      <tr>
        <td colspan="3" style="height: 3em;">
          <div id="chpwdFrm_new_pwd1_message"></div>
        </td>
      </tr>
      
      <tr>
        <td colspan="2" class="button">
          <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
        </td>
      </tr>
    </table>  
  </form>
{{/if}}