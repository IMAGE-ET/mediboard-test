{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

sendMessage = function(echange_hprim_id, echange_hprim_classname){
  var url = new Url;
  url.setModuleAction("sip", "ajax_send_message");
  url.addParam("echange_hprim_id", echange_hprim_id);
  url.addParam("echange_hprim_classname", echange_hprim_classname);
	url.requestUpdate("systemMsg", { onComplete:function() { 
		 refreshEchange(echange_hprim_id, echange_hprim_classname) }});
}

refreshEchange = function(echange_hprim_id, echange_hprim_classname){
  var url = new Url;
  url.setModuleAction("sip", "ajax_refresh_message");
  url.addParam("echange_hprim_id", echange_hprim_id);
  url.addParam("echange_hprim_classname", echange_hprim_classname);
  url.requestUpdate("echange_"+echange_hprim_id , { waitingText: null });
}

function changePage(page) {
  $V(getForm('filterEchange').page,page);
}

</script>

<table class="main">
  {{if !$echange_hprim->_id}}
  
  <!-- Filtres -->
  <tr>
    <td style="text-align: center;">
      <form action="?" name="filterEchange" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="types[]" />
        <input type="hidden" name="page" value="{{$page}}" onchange="this.form.submit()"/>
        
        <table class="form">
	        <tr>
	          <th class="category" colspan="2">Choix de la date d'échange</th>
	        </tr>
	        <tr>
	          <th>{{mb_label object=$echange_hprim field="_date_min"}}</th>
	          <td>{{mb_field object=$echange_hprim field="_date_min" form="filterEchange" register=true}} </td>
	        </tr>
	        <tr>
	           <th>{{mb_label object=$echange_hprim field="_date_max"}}</th>
	           <td>{{mb_field object=$echange_hprim field="_date_max" form="filterEchange" register=true}} </td>
	        </tr>
	        <tr>
	          <th class="category" colspan="2">Critères de filtres</th>
	        </tr>
          <tr>
            <th>Type de message d'événement</th>
            <td>
              <select class="str" name="msg_evenement">
                <option value="">&mdash; Liste des messages </option>
                <option value="patients" {{if $msg_evenement == "patients"}}selected="selected"{{/if}}>
                  Message patients
                </option>
                <option value="pmsi" {{if $msg_evenement == "pmsi"}}selected="selected"{{/if}}>
                  Message PMSI
                </option>
                <option value="serveurActes" {{if $msg_evenement == "serveurActes"}}selected="selected"{{/if}}>
                  Message serveur actes
                </option>
              </select>
            </td>
          </tr> 
	        <tr>
            <th>Types d'événements</th>
            <td>
              <select class="str" name="type_evenement">
                <option value="">&mdash; Liste des événements </option>
                <option value="inconnu" {{if $type_evenement == "inconnu"}}selected="selected"{{/if}}>
                  Inconnu
                </option>
                <option value="enregistrementPatient" {{if $type_evenement == "enregistrementPatient"}}selected="selected"{{/if}}>
                  Enregistrement Patient
                </option>
                <option value="fusionPatient" {{if $type_evenement == "fusionPatient"}}selected="selected"{{/if}}>
                  Fusion Patient
                </option>
                <option value="venuePatient" {{if $type_evenement == "venuePatient"}}selected="selected"{{/if}}>
                  Venue Patient
                </option>
                <option value="fusionVenue" {{if $type_evenement == "fusionVenue"}}selected="selected"{{/if}}>
                  Fusion Venue
                </option>
                <option value="mouvementPatient" {{if $type_evenement == "mouvementPatient"}}selected="selected"{{/if}}>
                  Mouvement Patient
                </option>
              </select>
            </td>
          </tr>
	        <tr>
	          <th>Choix du statut d'acquittement</th>
	          <td>
	            <select class="str" name="statut_acquittement">
			          <option value="">&mdash; Liste des statuts </option>
			          <option value="OK" {{if $statut_acquittement == "OK"}}selected="selected"{{/if}}>Ok</option>
			          <option value="avertissement" {{if $statut_acquittement == "avertissement"}}selected="selected"{{/if}}>Avertissement </option>
			          <option value="erreur" {{if $statut_acquittement == "erreur"}}selected="selected"{{/if}}>Erreur</option>
			        </select>
	          </td>
	        </tr>
          <tr>
            <td colspan="2" style="text-align: center">
              {{foreach from=$types key=type item=value}}
			          <input type="checkbox" name="types[{{$type}}]" {{if array_key_exists($type, $selected_types)}}checked="checked"{{/if}} />{{$type}}
			        {{/foreach}}
            </td>
          </tr>
          <tr>
            <td colspan="2" style="text-align: center">
              <button type="submit" class="search">Filtrer</button>
            </td>
          </tr>
        </table>
          {{if $total_echange_hprim != 0}}
            {{mb_include module=system template=inc_pagination total=$total_echange_hprim current=$page change_page='changePage'}}
	        {{/if}}
      </form>
    </td>
  </tr>
  <tr>
    <td class="halfPane" rowspan="3">
      <table class="tbl">
        <tr>
          <th class="title" colspan="14">ECHANGES HPRIM - {{$msg_evenement}}</th>
        </tr>
        <tr>
          <th></th>
          <th>{{mb_title object=$echange_hprim field="echange_hprim_id"}}</th>
          <th>{{mb_title object=$echange_hprim field="initiateur_id"}}</th>
          <th>Object Class</th>
          <th>Object Id</th>
          <th>{{mb_title object=$echange_hprim field="date_production"}}</th>
          <th>{{mb_title object=$echange_hprim field="identifiant_emetteur"}}</th>
          <th>{{mb_title object=$echange_hprim field="destinataire"}}</th>
          <th>{{mb_title object=$echange_hprim field="sous_type"}}</th>
          <th>{{mb_title object=$echange_hprim field="date_echange"}}</th>
          <th>{{mb_title object=$echange_hprim field="acquittement"}}</th>
          <th>{{mb_title object=$echange_hprim field="statut_acquittement"}}</th>
          <th>{{mb_title object=$echange_hprim field="message_valide"}}</th>
          <th>{{mb_title object=$echange_hprim field="acquittement_valide"}}</th>
        </tr>
        {{foreach from=$listEchangeHprim item=curr_echange_hprim}}
          <tbody id="echange_{{$curr_echange_hprim->_id}}">
            {{include file="inc_echange_hprim.tpl" object=$curr_echange_hprim}}
          </tbody>
          {{foreach from=$curr_echange_hprim->_ref_notifications item=curr_ref_notification}}
            <tbody id="echange_{{$curr_ref_notification->_id}}">
              {{include file="inc_echange_hprim.tpl" object=$curr_ref_notification}}
            </tbody>
          {{/foreach}}
        {{foreachelse}}
          <tr>
            <td colspan="14">
              {{tr}}CEchangeHprim.none{{/tr}}
            </td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{else}}
  <script type="text/javascript">
    Main.add(function () {
      Control.Tabs.create('tabs-contenu', true);
    });
  </script>
  <tr>
    <td>
      <ul id="tabs-contenu" class="control_tabs">
        <li><a href="#message">{{mb_title object=$echange_hprim field="message"}}</a></li>
        <li><a href="#ack">{{mb_title object=$echange_hprim field="acquittement"}}</a></li>
      </ul>
      
      <hr class="control_tabs" />
      
      <div id="message" style="display: none;">
        {{mb_value object=$echange_hprim field="message"}}
      </div>
      
      <div id="ack" style="display: none;">
        {{if $echange_hprim->message_valide == 1 || $echange_hprim->acquittement_valide == 1}}
          {{if $echange_hprim->acquittement}}
            {{mb_value object=$echange_hprim field="acquittement"}}
            
            <div class="big-{{if ($echange_hprim->statut_acquittement == 'erreur')}}error
                            {{elseif ($echange_hprim->statut_acquittement == 'avertissement')}}warning
                            {{else}}info{{/if}}">
              {{foreach from=$observations item=observation}}
                <strong>Code :</strong> {{$observation.code}} <br />
                <strong>Libelle :</strong> {{$observation.libelle}} <br />
                <strong>Commentaire :</strong> {{$observation.commentaire}} <br />
              {{/foreach}}
            </div>
          {{else}}
            <div class="big-info">Aucun acquittement n'a été reçu.</div>
          {{/if}}
        {{else}}
          <div class="big-error">
            {{if $doc_errors_msg}}
              <strong>Erreur validation schéma du message</strong> <br />
              {{$doc_errors_msg}}
            {{/if}}
            {{if $doc_errors_ack}}
              <strong>Erreur validation schéma de l'acquittement</strong> <br />
              {{$doc_errors_ack}}
            {{/if}}
          </div>
        {{/if}}
      </div>
    </td>
  </tr> 
  {{/if}}
</table>