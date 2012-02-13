{{* $Id: inc_edit_user.tpl 12741 2011-07-23 12:32:03Z mytto $ *}}

{{*
  * @package Mediboard
  * @subpackage admin
  * @version $Revision: 12741 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

<table class="form">
  <tr>
    <th>{{mb_label object=$user field="user_username"}}</th>
    <td>
      {{if !$readOnlyLDAP}}
        {{mb_field object=$user field="user_username"}}
      {{else}}
        {{mb_value object=$user field="user_username"}}
        {{mb_field object=$user field="user_username" hidden=true}}
      {{/if}}
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$user field="user_type"}}</th>
    <td>
      <select name="user_type" class="{{$user->_props.user_type}}">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$utypes key=_key item=type}}
        <option value="{{$_key}}" {{if $_key == $user->user_type}}selected="selected"{{/if}}>{{$type}}</option>
        {{/foreach}}
      </select>
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$user field="template"}}</th>
    <td>{{mb_field object=$user field="template"}}</td>
  </tr>
  
  {{if !$readOnlyLDAP}}
  <tr>
    <th><label for="_user_password" title="Saisir le mot de passe. Obligatoire">Mot de passe</label></th>
    <td><input  type="password" name="_user_password" class="{{$specs._user_password}}{{if !$user->user_id}} notNull{{/if}}" value="" onkeyup="checkFormElement(this)" />
    <span id="editFrm__user_password_message"></span>
    </td>
  </tr>
  <tr>
    <th><label for="_user_password2" title="Re-saisir le mot de passe pour confimer. Obligatoire">Mot de passe (bis)</label></th>
    <td><input type="password" name="_user_password2" class="password sameAs|_user_password" value="" /></td>
  </tr>
  {{/if}}

  <tr>
    <th>{{mb_label object=$user field="user_last_name"}}</th>
    <td>
      {{if !$readOnlyLDAP}}
        {{mb_field object=$user field="user_last_name"}}
      {{else}}
        {{mb_value object=$user field="user_last_name"}}
        {{mb_field object=$user field="user_last_name" hidden=true}}
      {{/if}}
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$user field="user_first_name"}}</th>
    <td>
      {{if !$readOnlyLDAP}}
        {{mb_field object=$user field="user_first_name"}}
      {{else}}
        {{mb_value object=$user field="user_first_name"}}
        {{mb_field object=$user field="user_first_name" hidden=true}}
      {{/if}}
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$user field="dont_log_connection"}}</th>
    <td>{{mb_field object=$user field="dont_log_connection"}}</td>
  </tr>
  
  {{if $user->dont_log_connection}}
  <tr>
    <th>{{mb_label object=$user field="_count_connections"}}</th>
    <td>
      {{mb_value object=$user field="_count_connections"}}
      {{if $can->admin && $user->_count_connections}}
      <label><input type="checkbox" name="_purge_connections" value="1"/>{{tr}}Purge{{/tr}}</label>
      {{/if}}
    </td>
  </tr>
  {{/if}}
  
  <tr>
    <td class="button" colspan="2">
      {{if $user->_id}}
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form, {
          typeName:'l\'utilisateur',
          objName:'{{$user->user_username|smarty:nodefaults|JSAttribute}}'
        })">
        {{tr}}Delete{{/tr}}
      </button>
      {{else}}
      <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
      {{/if}}
    </td>
  </tr>
    
  <!-- Link to CMediusers -->
  <tr>
    <td colspan="2" class="button">
      {{if $user->_ref_mediuser && $user->_ref_mediuser->_id}}
        <div class="small-success">
          Cet utilisateur est bien intégré à l'organigramme.
          <br />
          <a class="button edit" href="?m=mediusers&tab=vw_idx_mediusers&user_id={{$user->_id}}">
            Gérer cet utilisateur dans l'organigramme
          </a>
        </div>
      {{else}}
        {{if $user->template}}
        <div class="small-info">
          Cet utilisateur n'est pas dans l'organigramme.
          <br />
          C'est <strong>normal pour un Profil</strong>.
        </div>
        {{else}}
        <div class="small-warning">
          Cet utilisateur n'est pas dans l'organigramme, 
          <br />
          C'est <strong>anormal pour un utilisateur réel</strong>.
          <br />
          <a class="button new" href="?m=mediusers&tab=vw_idx_mediusers&user_id={{$user->_id}}&no_association=1">
            Associer cet utilisateur à l'organigramme
          </a>
        </div>
        {{/if}}
      {{/if}}
    </td>
  </tr>
  
</table>