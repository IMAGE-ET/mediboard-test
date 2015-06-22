{{*
 * $Id$
 *  
 * @category Hospitalisation
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{assign var=alerte value=$obs->_ref_alerte}}

{{if !$alerte->_id}}
  {{mb_return}}
{{/if}}

{{assign var=img value=ampoule_grey}}
{{if $alerte->handled == 0}}
  {{assign var=img value=ampoule}}
{{/if}}

<div id="alert_obs_{{$obs->_id}}">
  <img src="images/icons/{{$img}}.png"
       onmouseover="
         {{if $img == "ampoule"}}
           Alert.showAlerts('{{$obs->_guid}}', 'observation', 'medium', refreshAlertObs.curry('{{$obs->_id}}'), this);
         {{else}}
           ObjectTooltip.createDOM(this, 'tracabilite_obs_{{$obs->_id}}');
         {{/if}}"/>

  <div id="tooltip-alerts-medium-{{$obs->_guid}}" style="display: none; height: 400px; width: 400px; overflow-x:auto;"></div>

  <div id="tracabilite_obs_{{$obs->_id}}" style="display: none;">
    <table class="tbl">
      <tr>
        <th class="title" colspan="3">Traçabilité des alertes</th>
      </tr>
      <tr>
        <th>Traité par</th>
        <th class="narrow">Date de création</th>
        <th class="narrow">Date de traitement</th>
      </tr>
      <tr>
        <td>{{$alerte->_ref_handled_user}}</td>
        <td>{{mb_value object=$alerte field=creation_date}}</td>
        <td>{{mb_include module=system template=inc_object_history object=$alerte}} {{mb_value object=$alerte field=handled_date}}</td>
      </tr>
    </table>
  </div>
</div>