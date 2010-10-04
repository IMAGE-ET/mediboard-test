{{* $Id: vw_idx_echange_hprim.tpl 10195 2010-09-28 15:58:38Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 10195 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


{{if $total_echange_hprim != 0}}
  {{mb_include module=system template=inc_pagination total=$total_echange_hprim current=$page change_page='changePage' jumper='10'}}
{{/if}}
<table class="tbl">
  <tr>
    <th class="title" colspan="20">ECHANGES HPRIM - {{$msg_evenement}}</th>
  </tr>
  <tr>
    <th></th>
    <th>{{tr}}Purge{{/tr}}</th>
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
    <th>{{mb_title object=$echange_hprim field="statut_acquittement"}}</th>
    <th>{{mb_title object=$echange_hprim field="_observations"}}</th>
    <th colspan="2">{{mb_title object=$echange_hprim field="message_valide"}}</th>
    <th colspan="2">{{mb_title object=$echange_hprim field="acquittement_valide"}}</th>
  </tr>
  {{foreach from=$echangesHprim item=_echange}}
    <tbody id="echange_{{$_echange->_id}}">
      {{mb_include template=inc_echange_hprim object=$_echange}}
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="17">
        {{tr}}CEchangeHprim.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>