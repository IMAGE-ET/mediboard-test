{{*
 * $Id:$
 * 
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 *}}

{{assign var=dossiers_anesth value=$consult->_refs_dossiers_anesth}}

{{mb_default var=onlycreate value=false}}

<script>
  createDossier = function(consult_id, duplicate) {
    var form = getForm("manageDossierAnesth");
    $V(form.del, 0);
    form.consultation_id.writeAttribute("disabled", null);
    $V(form.consultation_id, consult_id);
    if (duplicate == 1) {
      $V(form.dosql, "do_duplicate_dossier_anesth_aed");
    }
    form.submit();
  };

  delDossier = function(dossier_anesth_id) {
    var form = getForm("manageDossierAnesth");
    form.consultation_id.writeAttribute("disabled", "disabled");
    $V(form.consultation_anesth_id, dossier_anesth_id);
    confirmDeletion(form, {
      typeName: 'le dossier d\'anesthésie'
    });
  };
  reloadDossierAnesth = function(dossier_anesth_id) {
    var url = new Url("cabinet", Preferences.new_consultation == "1" ? "vw_consultation" : "edit_consultation", "tab");
    url.addParam("selConsult", "{{$consult->_id}}");
    url.addParam("dossier_anesth_id", dossier_anesth_id);
    url.redirect();
  }

  GestionDA = {
    url: null,
    edit: function() {
      var url = new Url("cabinet", "vw_gestion_da");
      url.addParam("conusultation_id", '{{$consult->_id}}');
      url.requestModal(800);
      GestionDA.url = url;
    }
  }
</script>
{{if $conf.dPcabinet.CConsultAnesth.use_new_da && $dossiers_anesth|@count}}
  <table>
    {{assign var=operation value=$consult_anesth->_ref_operation}}
    {{if $consult_anesth->operation_id}}
      {{assign var=sejour value=$consult_anesth->_ref_operation->_ref_sejour}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
        <strong>Séjour :</strong>
        Dr {{$sejour->_ref_praticien->_view}} -
        {{if $sejour->type!="ambu" && $sejour->type!="exte"}} {{$sejour->_duree_prevue}} jour(s) -{{/if}}
        {{mb_value object=$sejour field=type}}
      </span><br/>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$operation->_guid}}', null, { view_tarif: true })">
        <strong>Intervention :</strong>
        le <strong>{{$operation->_datetime|date_format:"%a %d %b %Y"}}</strong>
        par le <strong>Dr {{$operation->_ref_chir->_view}}</strong>
          {{if $operation->libelle}}
            <em>[{{$operation->libelle}}]</em>
          {{/if}}
      </span><br/>
      <strong>{{mb_label object=$operation field="depassement"}} :</strong>
      {{mb_value object=$operation field="depassement"}}
    {{else}}
      {{if $consult_anesth->date_interv || $consult_anesth->chir_id || $consult_anesth->libelle_interv}}
        <tr>
          <th>{{mb_label object=$consult_anesth field=date_interv}}</th>
          <td>{{mb_value object=$consult_anesth field=date_interv}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field=chir_id}}</th>
          <td>{{mb_value object=$consult_anesth field=chir_id}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$consult_anesth field=libelle_interv}}</th>
          <td>{{mb_value object=$consult_anesth field=libelle_interv}}</td>
        </tr>
      {{/if}}
      <tr>
        <td colspan="2">L'intervention n'est pas liée</td>
      </tr>
    {{/if}}
    <tr>
      <td colspan="2" class="button">
        <button type="button" class="edit" onclick="GestionDA.edit();">
          Gérer le{{if $dossiers_anesth|@count > 1}}s {{$dossiers_anesth|@count}} dossiers {{else}} dossier{{/if}}
        </button>
      </td>
    </tr>
  </table>
{{else}}
  <!-- Formulaire de création / suppression de dossier d'anesthésie -->
  <form name="manageDossierAnesth" action="?" method="post">
    <input type="hidden" name="m" value="cabinet" />
    <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="consultation_anesth_id" />
    <input type="hidden" name="consultation_id" disabled/>
    {{if $app->user_prefs.new_consultation}}
      <input type="hidden" name="postRedirect" value="m=cabinet&tab=vw_consultation&selConsult={{$consult->_id}}" />
    {{else}}
      <input type="hidden" name="postRedirect" value="m=cabinet&tab=edit_consultation&selConsult={{$consult->_id}}" />
    {{/if}}
    {{if $dossiers_anesth|@count}}
      <select name="_consult_anesth_id" style="width: 20em;" onchange="reloadDossierAnesth(this.value)">
        {{foreach from=$dossiers_anesth item=_dossier}}
          {{assign var=_op value=$_dossier->_ref_operation}}
          <option value="{{$_dossier->_id}}" {{if $consult_anesth->_id == $_dossier->_id}}selected{{/if}}>
            Dossier d'anesthésie du {{$_dossier->_ref_consultation->_ref_plageconsult->date|date_format:$conf.date}}
            {{if $_op->_id}}(Intervention du {{$_op->_datetime|date_format:$conf.date}}){{/if}}
          </option>
        {{/foreach}}
      </select>
      <button type="button" class="trash notext" title="Supprimer le dossier d'anesthésie"
              onclick="delDossier($V(this.form._consult_anesth_id))">{{tr}}Delete{{/tr}}</button>
    {{/if}}
    <button type="button" class="add {{if !$onlycreate}}notext{{/if}}" onclick="createDossier('{{$consult->_id}}')">Ajouter un dossier d'anesthésie</button>
    {{if $dossiers_anesth|@count}}
      <button type="button" class="add" title="Dupliquer le dossier courant" onclick="createDossier('{{$consult->_id}}', 1)">Dupliquer</button>
    {{/if}}
  </form>
{{/if}}