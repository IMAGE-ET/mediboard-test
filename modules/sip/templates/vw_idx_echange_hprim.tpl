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
	          <td class="date">{{mb_field object=$echange_hprim field="_date_min" form="filterEchange" register=true}} </td>
	        </tr>
	        <tr>
	           <th>{{mb_label object=$echange_hprim field="_date_max"}}</th>
	           <td class="date">{{mb_field object=$echange_hprim field="_date_max" form="filterEchange" register=true}} </td>
	        </tr>
	        <tr>
	          <th class="category" colspan="2">Critères de filtres</th>
	        </tr>
	        <tr>
            <th>Types d'événements patients</th>
            <td>
              <select class="str" name="type_evenement">
                <option value="">&mdash; Liste des événements </option>
                <option value="enregistrementPatient" {{if $type_evenement == "enregistrementPatient"}}selected="selected"{{/if}}>
                  Enregistrement Patient
                </option>
                <option value="fusionPatient" {{if $type_evenement == "fusionPatient"}}selected="selected"{{/if}}>
                  Fusion Patient
                </option>
                <option value="venuePatient" {{if $type_evenement == "venuePatient"}}selected="selected"{{/if}}>
                  Venue Patient
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
		        <div style="font-weight:bold;padding-top:10px"> 
		          {{$total_echange_hprim}} {{tr}}results{{/tr}}
		        </div>
		        <div class="pagination">
		          {{if ($page == 1)}}
		            {{$page}}
				      {{else}}
				        <a href="#1" onclick="$V(document.forms.filterEchange.elements.page, {{$page-1}})"> < Précédent </a> |
		            <a href="#1" onclick="$V(document.forms.filterEchange.elements.page, 1)"> 1 </a> | 
		            {{$page}} 
				      {{/if}}
				      {{if $page != $total_pages}}
				        | <a href="#1" onclick="$V(document.forms.filterEchange.elements.page, {{$total_pages}})"> {{$total_pages}} </a> | 
				        <a href="#1" onclick="$V(document.forms.filterEchange.elements.page, {{$page+1}})"> Suivant > </a>
		          {{/if}}
		        </div>
		        <div>
			        <select name="listpageechangehprim" onchange="$V(this.form.elements.page, $V(this))">
			          <option value="">&mdash; Page</option>
			          {{if $total_pages < 4}}
			            {{assign var="step" value=1}}
			          {{else}}
			            {{assign var="step" value=4}}
			          {{/if}}
		            {{foreach from=1|range:$total_pages:$step item=curr_page}}
		              <option value="{{$curr_page}}" {{if $curr_page == $page}}selected="selected"{{/if}}>{{$curr_page}}</option>
		            {{/foreach}}
			        </select>
		        </div>
	        {{/if}}
      </form>
    </td>
  </tr>
  <tr>
    <td class="halfPane" rowspan="3">
      <table class="tbl">
        <tr>
          <th class="title" colspan="14">ECHANGES HPRIM</th>
        </tr>
        <tr>
          <th></th>
          <th>{{mb_title object=$echange_hprim field="echange_hprim_id"}}</th>
          <th>{{mb_title object=$echange_hprim field="initiateur_id"}}</th>
          <th>Patient</th>
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
  <tr>
    <th class="title" style="text-transform: uppercase;">{{mb_title object=$echange_hprim field="message"}}</th>
    {{if $echange_hprim->message_valide == 1 || $echange_hprim->acquittement_valide == 1}}
      <th class="title" style="text-transform: uppercase;">{{mb_title object=$echange_hprim field="acquittement"}}</th>
    {{/if}}
  </tr>
  <tr>
    <td style="height:730px; width:50%">{{mb_value object=$echange_hprim field="message"}}</td>
    <td style="height:730px;">
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
    </td>
  </tr>
  {{/if}}
</table>