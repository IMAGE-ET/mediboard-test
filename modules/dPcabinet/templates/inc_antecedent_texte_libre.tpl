{{*
 * $Id$
 *
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=callback_texte_libre value=""}}

<script>
  onSubmitTraitement = function(form) {
    var trait = $(form.traitement);
    if (!trait.present()) {
      return false;
    }

    onSubmitFormAjax(form, {
      onComplete : function() {
        {{if $type_see}}
        DossierMedical.reloadDossierPatient(null, '{{$type_see}}');
        {{elseif $callback_texte_libre}}
          {{$callback_texte_libre}}();
        {{else}}
        DossierMedical.reloadDossiersMedicaux();
        {{/if}}
      }
    } );

    trait.clear().focus();

    return false;
  };
</script>

<form name="editTrmtFrm{{$addform}}" action="?m=cabinet" method="post" onsubmit="return onSubmitTraitement(this);">
  <input type="hidden" name="m" value="patients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_traitement_aed" />
  <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />

  {{if $_is_anesth}}
    <!-- On passe _sejour_id seulement s'il y a un sejour_id -->
    <input type="hidden" name="_sejour_id" value="{{$sejour_id}}" />
  {{/if}}

  <table class="layout">
    <tr>
      {{if $app->user_prefs.showDatesAntecedents}}
        <th style="height: 100%;">{{mb_label object=$traitement field=debut}}</th>
        <td>{{mb_field object=$traitement field=debut form=editTrmtFrm$addform register=true}}</td>
      {{else}}
        <td colspan="2"></td>
      {{/if}}
      <td rowspan="2" style="width: 100%">
        {{mb_field object=$traitement field=traitement rows=4 form=editTrmtFrm$addform
        aidesaisie="validateOnBlur: 0"}}
      </td>
    </tr>
    <tr>
      {{if $app->user_prefs.showDatesAntecedents}}
        <th>{{mb_label object=$traitement field=fin}}</th>
        <td>{{mb_field object=$traitement field=fin form=editTrmtFrm$addform register=true}}</td>
      {{else}}
        <td colspan="2"></td>
      {{/if}}
    </tr>

    <tr>
      <td class="button" colspan="3">
        <button class="tick">
          {{tr}}Add{{/tr}} le traitement
        </button>
      </td>
    </tr>
  </table>
</form>