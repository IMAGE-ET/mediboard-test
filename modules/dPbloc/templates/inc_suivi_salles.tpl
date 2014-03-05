{{*
 * $Id$
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="main" id="suivi-salles">
  <tr>
    <th colspan="100">
      <h1 class="no-break">{{$date_suivi|date_format:$conf.longdate}}</h1>
    </th>
  </tr>
  <tr class="not-printable">
    <td class="button" colspan="100">
      {{foreach from=$bloc->_ref_salles item=_salle}}
        <label><input type="checkbox" onclick="Effect.toggle('salle-{{$_salle->_id}}', 'appear');" checked="checked" /> {{$_salle->nom}}</label>
      {{/foreach}}
      {{if $non_traitees|@count}}
        <label><input type="checkbox" onclick="Effect.toggle('non-traitees', 'appear');" checked="checked" /> {{tr}}CSejour.type.hors_plage{{/tr}}</label>
      {{/if}}
    </td>
  </tr>
  <tr>
    {{foreach from=$bloc->_ref_salles item=_salle}}
      <td id="salle-{{$_salle->_id}}">
        <table class="tbl">
          <tr>
            <th class="title">{{$_salle->nom}}</th>
          </tr>
        </table>
        {{mb_include module=salleOp template=inc_details_plages salle=$_salle}}
      </td>
    {{foreachelse}}
      <td class="empty">{{tr}}CSalle.none{{/tr}}</td>
    {{/foreach}}
    {{if $non_traitees|@count}}
      {{assign var=salle value=""}}
      <td id="non-traitees">
        <table class="tbl">
          <tr>
            <th class="title" colspan="5">{{tr}}CSejour.type.hors_plage{{/tr}}</th>
          </tr>
          {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=1 operations=$non_traitees}}
        </table>
      </td>
    {{/if}}
  </tr>
</table>