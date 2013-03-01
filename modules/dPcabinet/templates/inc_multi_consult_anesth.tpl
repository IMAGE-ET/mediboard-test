{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{assign var=dossiers_anesth value=$consult->_refs_dossiers_anesth}}

<script type="text/javascript">
  createDossier = function(consult_id) {
    var form = getForm("manageDossierAnesth");
    $V(form.del, 0);
    form.consultation_id.writeAttribute("disabled", null);
    $V(form.consultation_id, consult_id);
    form.submit();
  }

  delDossier = function(dossier_anesth_id) {
    var form = getForm("manageDossierAnesth");
    form.consultation_id.writeAttribute("disabled", "disabled");
    $V(form.consultation_anesth_id, dossier_anesth_id);
    confirmDeletion(form, {
      typeName: 'le dossier d\'anesthésie'
    });
  }
  reloadDossierAnesth = function(dossier_anesth_id) {
    var url = new Url("cabinet", "edit_consultation", "tab");
    url.addParam("selConsult", "{{$consult->_id}}");
    url.addParam("dossier_anesth_id", dossier_anesth_id);
    url.redirect();
  }
</script>

<!-- Formulaire de création / suppression de dossier d'anesthésie -->
<form name="manageDossierAnesth" action="?" method="post">
  <input type="hidden" name="m" value="cabinet" />
  <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="consultation_anesth_id" />
  <input type="hidden" name="consultation_id" disabled/>
  <input type="hidden" name="postRedirect" value="m=cabinet&tab=edit_consultation&selConsult={{$consult->_id}}" />
  {{if $dossiers_anesth|@count}}
    <select name="_consult_anesth_id" onchange="reloadDossierAnesth(this.value)">
      {{foreach from=$dossiers_anesth item=_dossier}}
        {{assign var=_op value=$_dossier->_ref_operation}}
        <option value="{{$_dossier->_id}}" {{if $consult_anesth->_id == $_dossier->_id}}selected{{/if}}>

          Dossier d'anesthésie {{$_dossier->_id}} {{if $_op->_id}}(Intervention du {{$_op->_datetime|date_format:$conf.date}}){{/if}}
          {{* Consult. anesth. du {{$_dossier->_ref_consultation->_ref_plageconsult->date|date_format:$conf.date}} à
          {{$_dossier->_ref_consultation->heure|date_format:$conf.time}} *}}
        </option>
      {{/foreach}}
    </select>
    <button type="button" class="trash notext" title="Supprimer le dossier d'anesthésie"
            onclick="delDossier($V(this.form._consult_anesth_id))"></button>
  {{/if}}
  <div>
    <button typd="button" class="add" onclick="createDossier('{{$consult->_id}}')">Ajouter un dossier d'anesthésie</button>
  </div>
</form>