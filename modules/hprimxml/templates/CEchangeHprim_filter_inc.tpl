{{* $Id: vw_idx_echange_hprim.tpl 10195 2010-09-28 15:58:38Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 10195 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

var evenements = {{$evenements|@json}};
function fillSelect(source, dest) {
  var selected = $V(source);
  dest.update();
  dest.insert(new Element('option', {value: ''}).update('&mdash; Liste des messages'));
  dest.insert(new Element('option', {value: 'inconnu'}).update($T('hprimxml-evt-none')));
  $H(evenements[selected]).each(function(pair){
    var v = pair.key;
    dest.insert(new Element('option', {value: v}).update($T('hprimxml-evt_'+selected+'-'+v)));
  });
}
 
</script>

<tr>
  <th colspan="2">{{mb_label object=$echange_xml field="id_permanent"}}</th>
  <td colspan="2">{{mb_field object=$echange_xml field="id_permanent"}}</td>
</tr>
<tr>
  <th colspan="2">Type de message d'événement</th>
  <td colspan="2">
    <select class="str" name="msg_evenement" onchange="fillSelect(this, this.form.elements.type_evenement)">
      <option value="">&mdash; Liste des messages </option>
      <option value="patients" {{if $msg_evenement == "patients"}}selected="selected"{{/if}}>
        Message patients
      </option>
      <option value="pmsi" {{if $msg_evenement == "pmsi"}}selected="selected"{{/if}}>
        Message PMSI
      </option>
    </select>
  </td>
</tr> 
<tr>
  <th colspan="2">Types d'événements</th>
  <td colspan="2">
    <select class="str" name="type_evenement">
      <option value="">&mdash; Liste des événements </option>
      <option value="inconnu" {{if $type_evenement == "inconnu"}}selected="selected"{{/if}}>
        {{tr}}hprimxml-evt-none{{/tr}}
      </option>
      {{foreach from=$evenements.$msg_evenement key=_type_evenement item=_class_evenement}}
        <option value="{{$_type_evenement}}" {{if $type_evenement == $_type_evenement}}selected="selected"{{/if}}>
          {{tr}}hprimxml-evt_{{$msg_evenement}}-{{$_type_evenement}}{{/tr}}
        </option>
      {{/foreach}}
    </select>
  </td>
</tr>
<tr>
  <th colspan="2">Choix du statut d'acquittement</th>
  <td colspan="2">
    <select class="str" name="statut_acquittement" onchange="$V(this.form.page, 0)">
      <option value="">&mdash; Liste des statuts </option>
      <option value="OK" {{if $statut_acquittement == "OK"}}selected="selected"{{/if}}>Ok</option>
      <option value="avertissement" {{if $statut_acquittement == "avertissement"}}selected="selected"{{/if}}>Avertissement </option>
      <option value="erreur" {{if $statut_acquittement == "erreur"}}selected="selected"{{/if}}>Erreur</option>
    </select>
  </td>
</tr>