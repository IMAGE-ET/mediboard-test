{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $total_echange_xml != 0}}
  {{mb_include module=system template=inc_pagination total=$total_echange_xml current=$page change_page='changePage' jumper='10'}}
{{/if}}
<table class="tbl">
  <tr>
    <th class="title" colspan="20">{{tr}}{{$echange_xml->_class_name}}{{/tr}} - {{$msg_evenement}}</th>
  </tr>
  <tr>
    <th></th>
    <th>{{tr}}Purge{{/tr}}</th>
    <th>Identifiant</th>
    {{if $conf.sip.server}}
    <th>{{mb_title object=$echange_xml field="initiateur_id"}}</th>
    {{/if}}
    <th>{{mb_title object=$echange_xml field="object_class"}}</th>
    <th>{{mb_title object=$echange_xml field="object_id"}}</th>
    <th>{{mb_title object=$echange_xml field="id_permanent"}}</th>
    <th>{{mb_title object=$echange_xml field="date_production"}}</th>
    <th>{{mb_title object=$echange_xml field="identifiant_emetteur"}}</th>
    <th>{{mb_title object=$echange_xml field="destinataire_id"}}</th>
    <th>{{mb_title object=$echange_xml field="sous_type"}}</th>
    <th>{{mb_title object=$echange_xml field="date_echange"}}</th>
    <th>Retraitement</th>
    <th>{{mb_title object=$echange_xml field="statut_acquittement"}}</th>
    <th>{{mb_title object=$echange_xml field="_observations"}}</th>
    <th colspan="2">{{mb_title object=$echange_xml field="message_valide"}}</th>
    <th colspan="2">{{mb_title object=$echange_xml field="acquittement_valide"}}</th>
  </tr>
  {{foreach from=$echangesXML item=_echange}}
    <tbody id="echange_{{$_echange->_id}}">
      {{mb_include template=inc_echange_xml object=$_echange}}
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="17">
        {{tr}}{{$echange_xml->_class_name}}.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>