{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage messagerie
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{mb_script module=messagerie script=UserEmail}}

<script type="text/javascript">
  selectInternalMessagerie = function(user_id) {
    var url = new Url('messagerie', 'vw_list_internalMessages');
    url.addParam('user_id', user_id);
    url.requestUpdate('account');
  }

  Main.add(function() {
    selectInternalMessagerie('{{$selected_user->_id}}');
  });
</script>

<div id="accounts" style="text-align: center;">
  <span style="float: left;">
    <form onsubmit="return checkForm(this.form)" method="get" name="selectUser">
      <input type="hidden" name="m" value="{{$m}}"/>
      <input type="hidden" name="tab" value="{{$tab}}"/>
      <label>Utilisateurs disponibles :
      <select name="selected_user" onchange="this.form.submit()">
        <option value="">{{tr}}Select{{/tr}} {{tr}}CMediusers{{/tr}}</option>
        {{foreach from=$users_list item=_user}}
          <option value="{{$_user->_id}}" {{if $selected_user->_id == $_user->_id}}selected="selected" {{/if}}>{{$_user}}</option>
        {{/foreach}}
      </select>
      </label>
    </form>
  </span>

  <span style="float: right;">
    <button type="button" onclick="messagerie.manageAccounts();">
      <i class="msgicon fa fa-gear"></i>
      Gestion des comptes
    </button>
  </span>

  <span>
    <span class="circled">
      <input type="radio" name="selected_account" onclick="selectInternalMessagerie('{{$selected_user->_id}}');" value="internal" checked="checked"/>
      Messagerie interne
    </span>

    {{foreach from=$pop_accounts item=_account}}
      <span class="circled">
        <label>
          <input type="radio" name="selected_account" onclick="messagerie.refreshAccount('{{$_account->_id}}')" value="{{$_account->_guid}}"/>
          {{$_account->libelle}}
        </label>
      </span>
    {{/foreach}}

    {{if $mssante_account}}
      {{mb_script module=mssante script=Folder ajax=1}}
      {{mb_script module=mssante script=Message ajax=1}}
      {{mb_script module=mssante script=Account ajax=1}}
      <span class="circled">
        <label>
          <input type="radio" name="selected_account" onclick="Account.select('{{$mssante_account->_id}}');" value="{{$mssante_account->_guid}}"/>
          MSSanté
        </label>
      </span>
    {{/if}}
  </span>
</div>

<div id="account">

</div>