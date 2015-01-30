{{*
 * $Id$
 *  
 * @category Modèles
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<div id="lock_area" style="display: none;">
  <table class="form">
    <tr>
      <th class="title">
        <button type="button" class="cancel notext" style="float: right;"
                onclick="$V(getForm('editFrm')._is_locked, 0, false);
                        $V(getForm('editFrm').___is_locked, 0, false);
                        Control.Modal.close();">
          {{tr}}Cancel{{/tr}}
        </button>
        Verrouillage du document
      </th>
    </tr>
  </table>

  {{if !$conf.dPcompteRendu.CCompteRendu.pass_lock}}
    <fieldset>
      <form name="LockDocOwner" method="post" action="?m=system&a=ajax_password_action"
            onsubmit="return onSubmitFormAjax(this, {useFormAction: true})">
        <input type="hidden" name="user_id" class="notNull" value="{{$app->user_id}}" />
        <input type="hidden" name="form_name" value="LockDocOwner" />
        <input type="hidden" name="callback" value="toggleLock" />
        <table class="form">
          <tr>
            <td class="button" {{if $app->_ref_user->_id == $compte_rendu->author_id}}style="display: none;"{{/if}}>
              <label>
                <input type="checkbox" name="change_owner" {{if $app->_ref_user->isPraticien()}}checked{{/if}}/>
                <strong>Devenir propriétaire du document</strong>
              </label>
            </td>
          </tr>
          <tr>
            <td class="text button">
              <strong>Souhaitez-vous réellement verrouiller ce document sous votre nom ?</strong>
            </td>
          </tr>
          <tr>
            <td class="button">
              <button type="button" class="tick" onclick="this.form.onsubmit();">Verrouiller</button>
            </td>
          </tr>
        </table>
      </form>
    </fieldset>
  {{/if}}

  <fieldset>
    <form name="LockDocOther" method="post" action="?m=system&a=ajax_password_action"
          onsubmit="return onSubmitFormAjax(this, {useFormAction: true})">
      <input type="hidden" name="user_id" class="notNull"
             {{if $conf.dPcompteRendu.CCompteRendu.pass_lock}}value="{{$curr_user->_id}}"{{/if}} />
      <input type="hidden" name="form_name" value="LockDocOther" />
      <input type="hidden" name="callback" value="toggleLock" />
      <table class="form">
        <tr>
          <td class="text button" colspan="2">
            {{if $conf.dPcompteRendu.CCompteRendu.pass_lock}}
              <div class="small-info">
                Pour verrouiller ce document sous votre nom, saisissez votre mot de passe ou choisissez un autre nom dans la liste.
              </div>
            {{else}}
              Souhaitez-vous verrouiller ce document pour un autre utilisateur ?
            {{/if}}
          </td>
        </tr>

        <tr>
          <th>Utilisateur</th>
          <td>
            <input type="text" name="_user_view" class="autocomplete"
                   {{if $conf.dPcompteRendu.CCompteRendu.pass_lock}}value="{{$curr_user}}"{{/if}} />
          </td>
        </tr>

        <tr>
          <th>
            <label for="user_password">Mot de passe</label>
          </th>
          <td>
            <input type="password" name="user_password" class="notNull password str" />
          </td>
        </tr>

        <tr class="change-owner-container">
          <td colspan="2" class="button">
            <label>
              <input type="checkbox" name="change_owner" {{if $app->_ref_user->isPraticien()}}checked{{/if}}/>
              L'utilisateur sélectionné devient propriétaire du document
            </label>
          </td>
        </tr>

        <tr>
          <td class="button" colspan="2">
            <button class="tick singleclick" onclick="return this.form.onsubmit();">Verrouiller</button>
          </td>
        </tr>
      </table>
    </form>
  </fieldset>
</div>