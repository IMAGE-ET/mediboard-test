{{*  
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*}}

<script type="text/javascript">

sendMessage = function(echange_hprim_id, echange_hprim_classname){
  var url = new Url;
  url.setModuleAction("sip", "httpreq_send_message");
  url.addParam("echange_hprim_id", echange_hprim_id);
  url.addParam("echange_hprim_classname", echange_hprim_classname);
	url.requestUpdate("systemMsg", { onComplete:function() { 
		 refreshEchange(echange_hprim_id, echange_hprim_classname) }});
}

refreshEchange = function(echange_hprim_id, echange_hprim_classname){
  var url = new Url;
  url.setModuleAction("sip", "httpreq_refresh_message");
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
        
        {{foreach from=$types key=type item=value}}
          <input type="checkbox" name="types[]" value="{{$type}}" {{if $value}}checked="checked"{{/if}} />{{$type}}
        {{/foreach}}
        <br />
        <button name="submit" class="search">Filtrer</button>
      </form>
    </td>
  </tr>
  
  <tr>
    <td class="halfPane" rowspan="3">
      <table class="tbl">
        <tr>
          <th class="title" colspan="12">ECHANGES HPRIM</th>
        </tr>
        <tr>
          <th></th>
          <th>{{mb_title object=$echange_hprim field="echange_hprim_id"}}</th>
          <th>{{mb_title object=$echange_hprim field="initiateur_id"}}</th>
          <th>Patient</th>
          <th>{{mb_title object=$echange_hprim field="date_production"}}</th>
          <th>{{mb_title object=$echange_hprim field="identifiant_emetteur"}}</th>
          <th>{{mb_title object=$echange_hprim field="destinataire"}}</th>
          <th>{{mb_title object=$echange_hprim field="type"}}</th>
          <th>{{mb_title object=$echange_hprim field="sous_type"}}</th>
          <th>{{mb_title object=$echange_hprim field="date_echange"}}</th>
          <th>{{mb_title object=$echange_hprim field="acquittement"}}</th>
          <th>{{mb_title object=$echange_hprim field="statut_acquittement"}}</th>
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
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{else}}
  <tr>
    <th class="title" style="text-transform: uppercase;">{{mb_title object=$echange_hprim field="message"}}</th>
    <th class="title" style="text-transform: uppercase;">{{mb_title object=$echange_hprim field="acquittement"}}</th>
  </tr>
  <tr>
    <td style="height:730px; width:50%">{{mb_value object=$echange_hprim field="message"}}</td>
    <td style="height:730px;">
      {{if $echange_hprim->acquittement}}
        {{mb_value object=$echange_hprim field="acquittement"}}
        
        <div class="big-{{if ($echange_hprim->statut_acquittement == 'erreur')}}error{{elseif ($echange_hprim->statut_acquittement == 'avertissement')}}warning{{else}}info{{/if}}">
          {{foreach from=$observations item=observation}}
            <strong>Code :</strong> {{$observation.code}} <br />
            <strong>Libelle :</strong> {{$observation.libelle}} <br />
            <strong>Commentaire :</strong> {{$observation.commentaire}} <br />
          {{/foreach}}
        </div>
      {{else}}
        <div class="big-info">Aucun acquittement n'a été reçu.</div>
      {{/if}}
    </td>
  </tr>
  {{/if}}
</table>