{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

sendMessage = function(echange_hprim_id, echange_hprim_classname){
  var url = new Url("hprimxml", "ajax_send_message");
  url.addParam("echange_hprim_id", echange_hprim_id);
  url.addParam("echange_hprim_classname", echange_hprim_classname);
  url.requestUpdate("systemMsg", { onComplete:function() { 
     refreshEchange(echange_hprim_id, echange_hprim_classname) }});
}

reprocessing = function(echange_hprim_id, echange_hprim_classname){
  var url = new Url("hprimxml", "ajax_reprocessing_message");
  url.addParam("echange_hprim_id", echange_hprim_id);
  url.addParam("echange_hprim_classname", echange_hprim_classname);
  url.requestUpdate("systemMsg", { onComplete:function() { 
     refreshEchange(echange_hprim_id, echange_hprim_classname) }});
}

refreshEchange = function(echange_hprim_id, echange_hprim_classname){
  var url = new Url("hprimxml", "ajax_refresh_message");
  url.addParam("echange_hprim_id", echange_hprim_id);
  url.addParam("echange_hprim_classname", echange_hprim_classname);
  url.requestUpdate("echange_"+echange_hprim_id);
}

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
  
function changePage(page) {
  $V(getForm('filterEchange').page,page);
}

function refreshEchanges(form) {
	var url = new Url("hprimxml", "ajax_refresh_echanges_hprim");
  url.addFormData(form);
  url.requestUpdate("listEchangesHprim");
  return false;
}

</script>

<table class="main">
  {{if !$echange_hprim->_id}}
  
  <!-- Filtres -->
  <tr>
    <td style="text-align: center;">
      <form action="?" name="filterEchange" method="get" onsubmit="return refreshEchanges(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="types[]" />
        <input type="hidden" name="page" value="{{$page}}" onchange="this.form.onsubmit()"/>
        
        <table class="form">
          <tr>
            <th class="category" colspan="4">Choix de la date d'échange</th>
          </tr>
          <tr>
            <th style="width:50%">{{mb_label object=$echange_hprim field="_date_min"}}</th>
            <td class="narrow">{{mb_field object=$echange_hprim field="_date_min" form="filterEchange" register=true onchange="\$V(this.form.page, 0)"}} </td>
            <th class="narrow">{{mb_label object=$echange_hprim field="_date_max"}}</th>
            <td style="width:50%">{{mb_field object=$echange_hprim field="_date_max" form="filterEchange" register=true onchange="\$V(this.form.page, 0)"}} </td>
          </tr>
          <tr>
            <th class="category" colspan="4">Critères de filtres</th>
          </tr>
          <tr>
            <th colspan="2">{{mb_label object=$echange_hprim field="echange_hprim_id"}}</th>
            <td colspan="2">{{mb_field object=$echange_hprim field="echange_hprim_id"}}</td>
          </tr>
          <tr>
            <th colspan="2">{{mb_label object=$echange_hprim field="id_permanent"}}</th>
            <td colspan="2">{{mb_field object=$echange_hprim field="id_permanent"}}</td>
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
          <tr>
            <td colspan="4" style="text-align: center">
              {{foreach from=$types key=type item=value}}
                <input onclick="$V(this.form.page, 0)" type="checkbox" name="types[{{$type}}]" {{if array_key_exists($type, $selected_types)}}checked="checked"{{/if}} />{{tr}}CEchangeHprim-type-{{$type}}{{/tr}}
              {{/foreach}}
            </td>
          </tr>
          <tr>
            <td colspan="4" style="text-align: center">
              <button type="submit" class="search">Filtrer</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  
  <tr>
    <td class="halfPane" rowspan="3" id="listEchangesHprim">
    </td>
  </tr>
  
  {{else}}
    {{mb_include template=inc_echange_hprim_details}}
  {{/if}}
</table>