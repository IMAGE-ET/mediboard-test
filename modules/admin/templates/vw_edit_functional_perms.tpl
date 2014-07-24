{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage admin
 * @author     SARL OpenXtrem
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_script module=admin script=preferences ajax=true}}

<script>
  Main.add(Control.Tabs.create.curry('tab-modules', true));
</script>

<table class="main">
  <tr>
    <td class="narrow">
      <ul id="tab-modules" class="control_tabs_vertical" style="width: 20em;">
        {{foreach from=$prefs key=module item=_prefs}}
          {{if $_prefs && ($module == "common" || $module|module_active)}}
            <li>
              <a href="#module-{{$module}}" style="line-height: 24px;">
                {{if $module != "common"}}
                  <img src="modules/{{$module}}/images/icon.png" width="24" style="float: left;" />
                {{/if}}
                {{tr}}module-{{$module}}-court{{/tr}}
                <small>({{$_prefs|@count}})</small>
              </a>
            </li>
          {{/if}}
        {{/foreach}}
      </ul>
    </td>

    <td>
      <form name="form-edit-preferences" action="?m=admin{{if !$ajax}}&amp;{{$actionType}}={{$action}}{{/if}}" method="post" onsubmit="return Preferences.onSubmitAll(this)">
        <input type="hidden" name="dosql" value="do_preference_aed" />
        <input type="hidden" name="m" value="admin" />
        <input type="hidden" name="user_id" value="{{$user->_id}}" />
        <input type="hidden" name="restricted" value="1" />

        <table class="form">
          <col style="width: 40%;" />
          {{if $user_id != "default"}}
            <col style="width: 15%;" />
            {{if !$user->template}}
            <col style="width: 15%;" />
            {{/if}}
            <col style="width: 30%;" />
          {{else}}
            <col style="width: 40%;" />
          {{/if}}

          <tr>
            <th class="title" {{if $can->admin}} colspan="2" {{/if}} >
              {{tr}}FunctionalPerms{{/tr}}
            </th>
            <th class="title">
              {{if $can->admin && $user_id != "default"}}
                <a href="?m={{$m}}&amp;tab=vw_functional_perms&amp;user_id=default" class="button edit">{{tr}}Default{{/tr}}</a>
              {{else}}
                {{tr}}Default{{/tr}}
              {{/if}}
            </th>

            {{if $user_id != "default"}}
              {{if !$user->template}}
                <th class="title">
                  {{tr}}User template{{/tr}} :
                  <br />
                  {{if $can->edit && $prof->_id}}
                    <a href="?m={{$m}}&amp;tab=vw_functional_perms&amp;user_id={{$prof->_id}}" class="button edit">{{$prof}}</a>
                  {{else}}
                    {{if $prof->_id}}{{$prof}}{{else}}{{tr}}None{{/tr}}{{/if}}
                  {{/if}}
                </th>
              {{/if}}
              <th class="title">
                {{tr}}{{$user->template|ternary:"User template":"CUser"}}{{/tr}} :
                <br/>{{if $user->_id}}{{$user}}{{else}}{{tr}}None{{/tr}}{{/if}}
              </th>
            {{/if}}
          </tr>

          <!-- Tous modules confondus -->
          {{assign var="module" value="common"}}
          <tbody style="display: none" id="module-{{$module}}"></tbody>

          {{foreach from=$prefs key=module item=_prefs}}
            {{if $module != "common" && $module|module_active}}
            <tbody style="display: none;" id="module-{{$module}}">
              {{mb_include module=$module template=functional_perms}}
            </tbody>
            {{/if}}
          {{/foreach}}

          <tr>
            <td class="button" colspan="5">
              <button type="submit" class="submit singleclick">{{tr}}Save{{/tr}}</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>
