{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage eai
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  toggleAutoRefresh = function(){
    if (!window.autoRefresh) {
      window.autoRefresh = setInterval(function(){
        getForm("filterExchange").onsubmit();
      }, 5000);
      $("auto-refresh-toggler").style.borderColor = "red";
    }
    else {
      clearTimeout(window.autoRefresh);
          window.autoRefresh = null;
      $("auto-refresh-toggler").style.borderColor = "";
    }
  }
</script>

<table class="main">
  <tr>
    <th class="title">
      <button onclick="ExchangeDataFormat.toggle();" style="float: left;" class="hslip notext" type="button" title="{{tr}}CExchangeDataFormat{{/tr}}">
        {{tr}}CExchangeDataFormat{{/tr}}
      </button>
      <button onclick="toggleAutoRefresh()" id="auto-refresh-toggler" style="float: right;" class="change notext" type="button">
        Auto-refresh (5s)
      </button>
      {{tr}}CExchangeDataFormat{{/tr}}
    </th>
  </tr>
  <!-- Filtres -->
  <tr>
    <td style="text-align: center;">
      <form action="?" name="filterExchange" method="get" onsubmit="return ExchangeDataFormat.viewAll(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        
        <table class="form">
          <tr>
            <th class="category" colspan="4">Choix de la date d'échange</th>
          </tr>
          <tr>
            <th style="width:50%">{{mb_label object=$exchange_df field="_date_min"}}</th>
            <td class="narrow">{{mb_field object=$exchange_df field="_date_min" form="filterExchange" register=true }} </td>
            <th class="narrow">{{mb_label object=$exchange_df field="_date_max"}}</th>
            <td style="width:50%">{{mb_field object=$exchange_df field="_date_max" form="filterExchange" register=true }} </td>
          </tr>
          <tr>
            <th class="category" colspan="4">{{tr}}filter-criteria{{/tr}}</th>
          </tr>
          <tr>
            <th colspan="2">{{mb_label object=$exchange_df field="group_id"}}</th>
            <td colspan="2">{{mb_field object=$exchange_df field="group_id" canNull=true form="filterExchange" autocomplete="true,1,50,true,true"}}</td>
          </tr>
          <tr>
            <th colspan="2">{{mb_label object=$exchange_df field="object_id"}}</th>
            <td colspan="2">{{mb_field object=$exchange_df field="object_id"}}</td>
          </tr>
          <tr>
            <th colspan="2">{{mb_label object=$exchange_df field="id_permanent"}}</th>
            <td colspan="2">{{mb_field object=$exchange_df field="id_permanent"}}</td>
          </tr>
          
          <tr>
            <td colspan="4" style="text-align: center">
              <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>

<table class="tbl" id="vw_all_exchanges">
  <tr>
    <th class="title" colspan="13">        
      {{tr}}CExchangeDataFormat{{/tr}}
    </th>
  </tr>
  <tr>
    <th>Identifiant</th>
    <th>{{mb_title class=CExchangeDataFormat field="object_class"}}</th>
    <th>{{mb_title class=CExchangeDataFormat field="object_id"}}</th>
    <th>{{mb_title class=CExchangeDataFormat field="id_permanent"}}</th>
    <th>{{mb_title class=CExchangeDataFormat field="date_production"}}</th>
    <th>{{mb_title class=CExchangeDataFormat field="sender_id"}}</th>
    <th>{{mb_title class=CExchangeDataFormat field="receiver_id"}}</th>
    <th>{{mb_title class=CExchangeDataFormat field="type"}}</th>
    <th>{{mb_title class=CExchangeDataFormat field="sous_type"}}</th>
    <th>{{mb_title class=CExchangeDataFormat field="date_echange"}}</th>
    <th>{{mb_title class=CExchangeDataFormat field="statut_acquittement"}}</th>
    <th>{{mb_title class=CExchangeDataFormat field="message_valide"}}</th>
    <th>{{mb_title class=CExchangeDataFormat field="acquittement_valide"}}</th>
  </tr>
  {{foreach from=$exchanges key=_exchange_classname item=_exchanges}}
    <tr>
      <th class="category" colspan="13">
        {{tr}}{{$_exchange_classname}}{{/tr}}
      </th>
    </tr>
    {{foreach from=$_exchanges item=_exchange}}
    <tr>
      <td class="narrow">
        <button type="button" onclick="ExchangeDataFormat.viewExchange('{{$_exchange->_guid}}')" class="search">
         {{$_exchange->_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
        </button>
      </td>
      <td class="narrow">
        {{$_exchange->object_class}}
      </td>
      <td class="narrow">
        {{if $_exchange->object_id}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_exchange->object_class}}-{{$_exchange->object_id}}');">
            {{$_exchange->object_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
          </span>
        {{/if}}
      </td>
      <td class="narrow">
        {{if $_exchange->id_permanent}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_exchange->object_class}}-{{$_exchange->object_id}}', 'identifiers');">
            {{$_exchange->id_permanent|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
          </span>
        {{/if}}
      </td>
      <td class="narrow">
        <label title='{{mb_value object=$_exchange field="date_production"}}'>
          {{mb_value object=$_exchange field="date_production" format=relative}}
        </label>
      </td>
      {{assign var=emetteur value=$_exchange->_ref_sender}}
      <td class="{{if $_exchange->sender_id == '0'}}error{{/if}} narrow">
         {{if $_exchange->_self_sender}}
         <label title='[SELF]' style="font-weight:bold">
           [SELF]
         </label>
         {{else}}
           <a href="?m=eai&tab=vw_idx_interop_actors#interop_actor_guid={{$emetteur->_guid}}">
             {{$emetteur->_view}}
           </a>
         {{/if}}
      </td>
      {{assign var=destinataire value=$_exchange->_ref_receiver}}
      <td class="narrow">
        {{if $_exchange->_self_receiver}}
         <label title='[SELF]' style="font-weight:bold">
           [SELF]
         </label>
         {{else}}
           <a href="?m=eai&tab=vw_idx_interop_actors#interop_actor_guid={{$destinataire->_guid}}">
             <span onmouseover="ObjectTooltip.createEx(this, '{{$destinataire->_guid}}');">
               {{$destinataire->_view}}
             </span>
           </a>
         {{/if}}
      </td>
      <td class="{{if $_exchange->type == 'inconnu'}}error{{/if}} narrow">{{mb_value object=$_exchange field="type"}}</td>
      <td class="{{if $_exchange->sous_type == 'inconnu'}}error{{/if}} narrow">{{mb_value object=$_exchange field="sous_type"}}</td>
      <td class="{{if $_exchange->date_echange}}ok{{else}}warning{{/if}} narrow">
        <label title='{{mb_value object=$_exchange field="date_echange"}}'>
          {{mb_value object=$_exchange field="date_echange" format=relative}}
        </label>
      </td>
      {{assign var=statut_acq value=$_exchange->statut_acquittement}}
      <td class="{{if !$statut_acq && $_exchange->_self_sender}}
                   hatching
                 {{elseif !$statut_acq || 
                          ($statut_acq == 'erreur') || 
                          ($statut_acq == 'AR') || 
                          ($statut_acq == 'err')}}
                   error 
                 {{elseif ($statut_acq == 'avertissement') || 
                          ($statut_acq == 'avt') || 
                          ($statut_acq == 'AE')}}
                   warning
                 {{/if}} 
                 narrow">
        {{mb_value object=$_exchange field="statut_acquittement"}}
      </td>
      <td class="{{if !$_exchange->message_valide}}error{{/if}} narrow">
        <a target="_blank" href="?m=eai&a=download_exchange&exchange_guid={{$_exchange->_guid}}&dialog=1&suppressHeaders=1&message=1" 
          class="button modify notext"></a>
      </td>
      <td class="{{if !$statut_acq && $_exchange->_self_sender}}hatching{{elseif !$_exchange->acquittement_valide}}error{{/if}} narrow">
        {{if $_exchange->_acquittement}}
          <a target="_blank" href="?m=eai&a=download_exchange&exchange_guid={{$_exchange->_guid}}&dialog=1&suppressHeaders=1&ack=1" 
            class="button modify notext"></a>
        {{/if}}
      </td>
    </tr>
    {{foreachelse}}
      <tr>
        <td colspan="13" class="empty">
          {{tr}}{{$_exchange_classname}}.none{{/tr}}
        </td>
      </tr>
    {{/foreach}}
  {{/foreach}}
</table>