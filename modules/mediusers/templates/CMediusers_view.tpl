{{assign var=mediuser value=$object}}

<table class="tbl tooltip">
  <tr>
    <th class="title text" colspan="2">
      {{mb_include module=system template=inc_object_idsante400 object=$mediuser}}
      {{mb_include module=system template=inc_object_history    object=$mediuser}}
      {{mb_include module=system template=inc_object_notes      object=$mediuser}}
      {{$mediuser}}
    </th>
  </tr>
  <tr>
    <td class="text" style="width: 1px;" rowspan="4">
      {{assign var=border value=$mediuser->_ref_function->color}}
      {{mb_include module=files template=inc_named_file name=identite.jpg default=identity_user.png size=50}}
    </td>
    <td class="text">
      <strong>{{mb_value object=$mediuser->_ref_function}}</strong>
    </td>
  </tr>
  {{if $mediuser->_can->read}}
    <tr>
      <td>
        {{mb_label object=$mediuser field=_user_phone}} :
        {{mb_value object=$mediuser field=_user_phone}}
      </td>
    </tr>
    <tr>
      <td>
        {{mb_label object=$mediuser field=_user_astreinte}} :
        {{mb_value object=$mediuser field=_user_astreinte}}
      </td>
    </tr>
    <tr>
      <td>
        {{mb_label object=$mediuser field=_user_email}} :
        {{mb_value object=$mediuser field=_user_email}}
      </td>
    </tr>
    {{if $mediuser->_is_praticien}}
    <tr>
      <th colspan="2">Praticien</th>
    </tr>
    {{if $mediuser->discipline_id}}
    <tr>
      <td colspan="2">
        {{mb_label object=$mediuser field=discipline_id}} :
        {{mb_value object=$mediuser field=discipline_id}}
      </td>
    </tr>
    {{/if}}
    {{if $mediuser->spec_cpam_id}}
    <tr>
      <td colspan="2">
        {{mb_label object=$mediuser field=spec_cpam_id}} :
        {{mb_value object=$mediuser field=spec_cpam_id}}
      </td>
    </tr>
    {{/if}}
    {{if $mediuser->titres}}
    <tr>
      <td colspan="2">
        {{mb_value object=$mediuser field=titres}}
      </td>
    </tr>
    {{/if}}
    <tr>
      <td colspan="2">
        {{mb_label object=$mediuser field=rpps}} :
        {{mb_value object=$mediuser field=rpps}}
      </td>
    </tr>
    <tr>
      <td colspan="2">
        {{mb_label object=$mediuser field=adeli}} :
        {{mb_value object=$mediuser field=adeli}}
      </td>
    </tr>
    {{/if}}
    <tr>
      <td colspan="2" class="button">
        {{mb_script module=personnel script=plage ajax=true}}

        {{if "dPpersonnel"|module_active && $modules.dPpersonnel->_can->edit}}
          <button type="button" class="search" onclick="PlageConge.showForUser('{{$mediuser->_id}}');">
            Congés
          </button>
        {{/if}}
        {{if "messagerie"|module_active && $modules.messagerie->_can->edit}}
          <a class="action" href="#nothing" onclick="UserMessage.createWithSubject('{{$mediuser->_id}}', null)">
            <button type="button">
              <img src="images/icons/usermessage.png" title="Envoyer un message" /> Message
            </button>
          </a>
        {{/if}}
        {{if "astreintes"|module_active && $modules.astreintes->_can->edit}}
          {{mb_script module=astreintes script=plage ajax=true}}
          <button type="button" class="search" onclick="PlageAstreinte.showForUser('{{$mediuser->_id}}');">
            {{tr}}CPlageAstreinte.plural{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
    {{assign var=modPerm value="admin"|module_active}}
    {{if $modPerm && $modPerm->_can->edit}}
      <tr>
        <th colspan="2">Administration</th>
      </tr>
      <tr>
        <td colspan="2" {{if !$mediuser->actif}}class="cancelled"{{/if}}>
          {{mb_label object=$mediuser field=_user_username}} :
          {{mb_value object=$mediuser field=_user_username}}
        </td>
      </tr>
      <tr>
        <td colspan="3">
          {{mb_label object=$mediuser field=_user_type_view}} :
          {{mb_value object=$mediuser field=_user_type_view}} [{{mb_value object=$mediuser field=_user_type}}]
        </td>
      </tr>
      <tr>
        <td colspan="3">
          {{mb_label object=$mediuser field=_profile_id}} :
          {{mb_value object=$mediuser field=_profile_id}}
        </td>
      </tr>
      <tr>
        <td colspan="3">
          {{mb_label object=$mediuser field=_user_last_login}} :
          {{mb_value object=$mediuser field=_user_last_login}}
        </td>
      </tr>
      <tr>
        <td colspan="3">
          {{mb_label object=$mediuser field=remote}} :
          {{mb_value object=$mediuser field=remote}}
        </td>
      </tr>
      <tr>
        <td colspan="3" class="button">
          <button class="search" onclick="location.href='?m=admin&amp;tab=view_edit_users&amp;user_username={{$mediuser->_user_username}}&amp;user_id={{$mediuser->_id}}'">
            {{tr}}CMediusers_administer{{/tr}}
          </button>
          <button class="search" onclick="location.href='?m=admin&amp;tab=edit_perms&amp;user_id={{$mediuser->_id}}'">
            Droits
          </button>
          <button class="search" onclick="location.href='?m=admin&amp;tab=edit_prefs&amp;user_id={{$mediuser->_id}}'">
            Préférences
          </button>
          {{assign var="loginas_user" value=$mediuser->_ref_user}}
          {{mb_include module=admin template=loginas}}
          {{assign var="_user" value=$mediuser->_ref_user}}
          {{mb_include module=admin template=unlock}}
        </td>
      </tr>
    {{/if}}
  {{/if}}
</table>

