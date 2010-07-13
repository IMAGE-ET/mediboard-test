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
	console.debug(selected);
  dest.update();
  $H(evenements[selected]).each(function(pair){
	  var v = pair.key;
    dest.insert(new Element('option', {value: v}).update(v));
  });
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
	          <th class="category" colspan="4">Choix de la date d'échange</th>
	        </tr>
	        <tr>
	          <th style="width:50%">{{mb_label object=$echange_hprim field="_date_min"}}</th>
	          <td style="width:0.1%">{{mb_field object=$echange_hprim field="_date_min" form="filterEchange" register=true onchange="\$V(this.form.page, 0)"}} </td>
	          <th style="width:0.1%">{{mb_label object=$echange_hprim field="_date_max"}}</th>
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
          {{if $total_echange_hprim != 0}}
            {{mb_include module=system template=inc_pagination total=$total_echange_hprim current=$page change_page='changePage' jumper='10'}}
	        {{/if}}
      </form>
    </td>
  </tr>
  <tr>
    <td class="halfPane" rowspan="3">
      <table class="tbl">
        <tr>
          <th class="title" colspan="17">ECHANGES HPRIM - {{$msg_evenement}}</th>
        </tr>
        <tr>
          <th></th>
          <th>{{mb_title object=$echange_hprim field="echange_hprim_id"}}</th>
          {{if $dPconfig.sip.server}}
          <th>{{mb_title object=$echange_hprim field="initiateur_id"}}</th>
          {{/if}}
          <th>{{mb_title object=$echange_hprim field="object_class"}}</th>
          <th>{{mb_title object=$echange_hprim field="object_id"}}</th>
          <th>{{mb_title object=$echange_hprim field="id_permanent"}}</th>
          <th>{{mb_title object=$echange_hprim field="date_production"}}</th>
          <th>{{mb_title object=$echange_hprim field="identifiant_emetteur"}}</th>
          <th>{{mb_title object=$echange_hprim field="destinataire"}}</th>
          <th>{{mb_title object=$echange_hprim field="sous_type"}}</th>
          <th>{{mb_title object=$echange_hprim field="date_echange"}}</th>
          <th>Retraitement</th>
          <th>{{mb_title object=$echange_hprim field="_acquittement"}}</th>
          <th>{{mb_title object=$echange_hprim field="statut_acquittement"}}</th>
          <th>{{mb_title object=$echange_hprim field="_observations"}}</th>
          <th>{{mb_title object=$echange_hprim field="message_valide"}}</th>
          <th>{{mb_title object=$echange_hprim field="acquittement_valide"}}</th>
        </tr>
        {{foreach from=$echangesHprim item=_echange}}
          <tbody id="echange_{{$_echange->_id}}">
            {{include file="inc_echange_hprim.tpl" object=$_echange}}
          </tbody>
          {{foreach from=$_echange->_ref_notifications item=_ref_notification}}
            <tbody id="echange_{{$_ref_notification->_id}}">
              {{include file="inc_echange_hprim.tpl" object=$_ref_notification}}
            </tbody>
          {{/foreach}}
        {{foreachelse}}
          <tr>
            <td colspan="17">
              {{tr}}CEchangeHprim.none{{/tr}}
            </td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{else}}
	  {{if $echange_hprim->_message === null || $echange_hprim->_acquittement === null}}
	    <div class="small-info">{{tr}}CEchangeHprim-purge-desc{{/tr}}</div>
	  {{else}}
	  <script type="text/javascript">
	    Main.add(function () {
	      Control.Tabs.create('tabs-contenu', true);
	    });
	  </script>
	  <tr>
	    <td>
	      <ul id="tabs-contenu" class="control_tabs">
	        <li><a href="#message">{{mb_title object=$echange_hprim field="_message"}}</a></li>
	        <li><a href="#ack">{{mb_title object=$echange_hprim field="_acquittement"}}</a></li>
	      </ul>
	      
	      <hr class="control_tabs" />
	      
	      <div id="message" style="display: none;">
	        {{mb_value object=$echange_hprim field="_message"}}
	      </div>
	      
	      <div id="ack" style="display: none;">
	        {{if $echange_hprim->message_valide == 1 || $echange_hprim->acquittement_valide == 1}}
	          {{if $echange_hprim->_acquittement}}
	            {{mb_value object=$echange_hprim field="_acquittement"}}
	            
	            <div class="big-{{if ($echange_hprim->statut_acquittement == 'erreur') || 
							                     ($echange_hprim->statut_acquittement == 'err')}}error
	                            {{elseif ($echange_hprim->statut_acquittement == 'avertissement') || 
															         ($echange_hprim->statut_acquittement == 'avt')
															}}warning
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
	{{/if}}
</table>