{{*
 * $Id$
 *  
 * @category Adminissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}


{{assign var="form_name" value="editAdmFrm`$sejour->_id`"}}
{{assign var="entree_reelle" value=$sejour->entree_reelle}}


<script>
  emptyFields = function() {
    var form = getForm('{{$form_name}}');
    $V(form.entree_reelle, '');
    $V(form.service_entree_id, '');
    $V(form._modifier_entree, '0');
    form.onsubmit();
  };

  showSecondary = function() {
    $$('.togglisable_tr').each(function(elt) {
      elt.hide();
    });

    var form = getForm('{{$form_name}}');
    var val = $V(form.mode_entree);
    if (val == 7) {
      $('etablissement_entree_id_{{$sejour->_id}}').show();
    }
    if (val == 6) {
      $('service_entree_id_{{$sejour->_id}}').show();
    }
  };

  Main.add(function() {
    showSecondary();
  });
</script>

<form name="{{$form_name}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: Control.Modal.close})">
  <input type="hidden" name="m" value="planningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  {{mb_key object=$sejour}}
  <input type="hidden" name="patient_id" value="{{$sejour->patient_id}}" />

  <table class="form">

    <tr>
      <th>{{mb_label object=$sejour field=entree_reelle}}</th>
      <td>
        {{if !$entree_reelle}}
          {{mb_field object=$sejour field=entree_reelle form=$form_name register=true class="notNull" value="now"}}
        {{else}}
          {{mb_field object=$sejour field=entree_reelle form=$form_name register=true}}
        {{/if}}
      </td>
      <th>{{mb_label object=$sejour field=entree_prevue}}</th>
      <td>{{mb_field object=$sejour field=entree_prevue}}</td>
    </tr>

    <input type="hidden" name="_modifier_entree" value="1" />


    {{if $conf.dPplanningOp.CSejour.use_custom_mode_entree && $list_mode_entree|@count}}
      <tr>
        <th>{{mb_label object=$sejour field=mode_entree}}</th>
        <td>
            {{mb_field object=$sejour field=mode_entree onchange="\$V(this.form._modifier_entree, 0);" hidden=true}}
            <select name="mode_entree_id" class="{{$sejour->_props.mode_entree_id}}" style="width: 15em;" onchange="updateModeEntree(this)">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
              {{foreach from=$list_mode_entree item=_mode}}
                <option value="{{$_mode->_id}}" data-mode="{{$_mode->mode}}" {{if $sejour->mode_entree_id == $_mode->_id}}selected{{/if}}>
                  {{$_mode}}
                </option>
              {{/foreach}}
            </select>
        </td>
      </tr>
    {{else}}
      <tr>
        <th>{{mb_label object=$sejour field=mode_entree}}</th>
        <td>{{mb_field object=$sejour field=mode_entree onchange="\$V(this.form._modifier_entree, 0); showSecondary();" typeEnum=radio}}</td>
      </tr>


      <tr style="display: none" id="etablissement_entree_id_{{$sejour->_id}}" class="togglisable_tr">
        <th>{{mb_label object=$sejour field=etablissement_entree_id}}</th>
        <td colspan="3">{{mb_field object=$sejour field="etablissement_entree_id" form="$form_name"
          autocomplete="true,1,50,true,true" onchange="changeEtablissementId(this.form)"}}</td>
      </tr>

      <tr class="togglisable_tr" id="service_entree_id_{{$sejour->_id}}">
        <th>{{mb_label object=$sejour field=service_entree_id}}</th>
        <td colspan="3">
          <select name="service_entree_id">
            <option value="">{{tr}}Choose{{/tr}}</option>
            {{foreach from=$services item=_service}}
              <option value="{{$_service->_id}}" {{if $_service->_id == $sejour->service_entree_id}}selected="selected" {{/if}}>{{$_service}}</option>
            {{/foreach}}
          </select>
        </td>
      </tr>
    {{/if}}

    <tr>
      <td colspan="4" class="button">
        <button class="{{if !$entree_reelle}}tick{{else}}save{{/if}}" type="button" onclick="{{if !$entree_reelle && (($date_actuelle > $sejour->entree_prevue) || ($date_demain < $sejour->entree_prevue))}}confirmation(this.form); Control.Modal.close();{{else}}this.form.onsubmit();{{/if}}">
          {{if !$entree_reelle}}{{tr}}CSejour-admit{{/tr}}{{else}}{{tr}}Save{{/tr}}{{/if}}
        </button>
        {{if $entree_reelle}}
          <button class="cancel" type="button" onclick="emptyFields();">
            {{tr}}CSejour-cancel_admit{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>

  </table>
</form>