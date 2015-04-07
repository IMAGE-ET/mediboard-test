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

  admettre = function() {
    var form = getForm('{{$form_name}}');

    // with idex
    var idex = $('idex_sejour_{{$sejour->_id}}');
    var nb_idex_not_ok = 0;
    if (idex) {
      $(idex).select('form').each(function(elt) {
        if (elt.id400 && !checkForm(elt)) {
          nb_idex_not_ok = 1;
          return false;
        }
        elt.onsubmit();
      });
    }

    // do idex must defined ?
    var must_be_define_idex = {{"dPsante400 CIdSante400 admit_ipp_nda_obligatory"|conf:"CGroups-$g"}};
    if (nb_idex_not_ok && must_be_define_idex) {
      return;
    }

    {{if !$entree_reelle && (($date_actuelle > $sejour->entree_prevue) || ($date_demain < $sejour->entree_prevue))}}
      if (confirm('La date enregistrée d\'admission est différente de la date prévue, souhaitez vous confimer l\'admission du patient ?')) {
        form.onsubmit();
      }
    {{else}}
      form.onsubmit();
    {{/if}}
  };

  showSecondary = function() {
    $$('.togglisable_tr').each(function(elt) {
      elt.hide();
    });

    var form = getForm('{{$form_name}}');
    var val = $V(form.mode_entree);
    if (val == 7) {
      $('empty_entree_id_{{$sejour->_id}}').hide();
      $('etablissement_entree_id_{{$sejour->_id}}').show();
    }
    else {
      if (val == 6) {
        $('empty_entree_id_{{$sejour->_id}}').hide();
        $('service_entree_id_{{$sejour->_id}}').show();
      }
      else {
        $('empty_entree_id_{{$sejour->_id}}').show();
      }
    }
  };

  Main.add(function() {
    showSecondary();
  });
</script>

<h2>Admission</h2>
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

    {{assign var=_mode_entree_prop value=$sejour->_props.mode_entree}}
    {{if "dPplanningOp CSejour required_mode_entree"|conf:"CGroups-$g"}}
      {{assign var=_mode_entree_prop value="$_mode_entree_prop notNull"}}
    {{/if}}

    {{if $conf.dPplanningOp.CSejour.use_custom_mode_entree && $list_mode_entree|@count}}
      <tr>
        <th>
          {{mb_label object=$sejour field=mode_entree prop=$_mode_entree_prop}}
        </th>
        <td>
          {{mb_field object=$sejour field=mode_entree onchange="\$V(this.form._modifier_entree, 0);" hidden=true prop=$_mode_entree_prop}}

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
        <th>
          {{mb_label object=$sejour field=mode_entree prop=$_mode_entree_prop}}
        </th>
        <td>
          {{mb_field object=$sejour field=mode_entree onchange="showSecondary();" typeEnum=radio prop=$_mode_entree_prop}}
        </td>
      </tr>

      <tr id="empty_entree_id_{{$sejour->_id}}">
        <td colspan="2">&nbsp;</td>
      </tr>

      <tr style="display: none" id="etablissement_entree_id_{{$sejour->_id}}" class="togglisable_tr">
        <th>{{mb_label object=$sejour field=etablissement_entree_id}}</th>
        <td>{{mb_field object=$sejour field="etablissement_entree_id" form="$form_name"
          autocomplete="true,1,50,true,true" onchange="changeEtablissementId(this.form)"}}</td>

        <th>{{mb_label object=$sejour field=date_entree_reelle_provenance}}</th>
        <td>{{mb_field object=$sejour field=date_entree_reelle_provenance form=$form_name register=true}}</td>
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
  </table>
</form>

{{if "dPsante400"|module_active && "dPsante400 CIdSante400 add_ipp_nda_manually"|conf:"CGroups-$g"}}
  <hr/>
  <h2>{{tr}}mod-dPsante400-tab-ajax_edit_manually_ipp_nda{{/tr}}</h2>
  {{assign var=ipp value=$sejour->_ref_patient->_ref_IPP}}
  {{assign var=nda value=$sejour->_ref_NDA}}
  {{unique_id var=unique_ipp}}
  {{unique_id var=unique_nda}}

  <div id="idex_sejour_{{$sejour->_id}}">
    {{mb_include module=dPsante400 template=inc_form_ipp_nda idex=$ipp object=$sejour->_ref_patient field=_IPP unique=$unique_ipp}}
    {{mb_include module=dPsante400 template=inc_form_ipp_nda idex=$nda object=$sejour field=_NDA unique=$unique_nda}}
  </div>
{{/if}}

<hr/>
<p style="text-align: center">
  <button class="{{if !$entree_reelle}}tick{{else}}save{{/if}}" type="button" onclick="admettre();">
    {{if !$entree_reelle}}{{tr}}CSejour-admit{{/tr}}{{else}}{{tr}}Save{{/tr}}{{/if}}
  </button>
  {{if $entree_reelle}}
    <button class="cancel" type="button" onclick="emptyFields();">
      {{tr}}CSejour-cancel_admit{{/tr}}
    </button>
  {{/if}}
</p>