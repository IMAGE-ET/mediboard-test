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
{{assign var=first_dossier value=$dossiers_anesth|@reset}}

<script type="text/javascript">
  duplicateDossier = function(consult_anesth_id) {
    var form = getForm("duplicateDossierAnesth");
    $V(form.consult_anesth_id, consult_anesth_id);
    form.submit();
  }
  
  reloadDossierAnesth = function(dossier_anesth_id) {
    var url = new Url("cabinet", "edit_consultation", "tab");
    url.addParam("selConsult", "{{$first_dossier->consultation_id}}");
    url.addParam("dossier_anesth_id", dossier_anesth_id);
    url.redirect();
  }
</script>

<form name="duplicateDossierAnesth" method="post">
  <input type="hidden" name="m" value="cabinet" />
  <input type="hidden" name="dosql" value="do_copy_dossier_anesth_aed" />
  <input type="hidden" name="consult_anesth_id" />
  <input type="hidden" name="postRedirect" value="m=cabinet&tab=edit_consultation&selConsult={{$first_dossier->consultation_id}}" />
</form>


{{if $dossiers_anesth|@count == 1}}
  {{assign var=dossier_anesth value=dossiers_anesth|@reset}}
  <button typd="button" class="new" onclick="duplicateDossier('{{$first_dossier->_id}}')">Dupliquer</button>
{{else}}
  <select name="consult_anesth_id" onchange="reloadDossierAnesth(this.value)">
    {{* <option value="0">&mdash; Choisissez un dossier</option> *}}
    {{foreach from=$dossiers_anesth item=_dossier}}
      {{assign var=_op value=$_dossier->_ref_operation}}
      <option value="{{$_dossier->_id}}" {{if $consult_anesth->_id == $_dossier->_id}}selected{{/if}}>
        
        Dossier d'anesthésie {{if $_op->_id}}(Intervention du {{$_op->_datetime|date_format:$conf.date}}){{/if}}
        {{* Consult. anesth. du {{$_dossier->_ref_consultation->_ref_plageconsult->date|date_format:$conf.date}} à
        {{$_dossier->_ref_consultation->heure|date_format:$conf.time}} *}}
      </option>
    {{/foreach}}
  </select>
{{/if}}
