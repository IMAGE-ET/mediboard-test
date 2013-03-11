{{*
  * Visualise les transmissions d'une cible fermée
  *  
  * @category dPhospi
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

{{assign var=readonly value=true}}

<div style="overflow-x: auto;">
<table class="tbl">
  <tr>
    <th rowspan="2">{{tr}}Type{{/tr}}</th>
    <th rowspan="2" style="width: 10%">{{tr}}User{{/tr}}</th>
    <th rowspan="2">{{tr}}Date{{/tr}}</th>
    <th rowspan="2">{{tr}}Hour{{/tr}}</th>
    <th rowspan="2">{{mb_title class=CTransmissionMedicale field=object_class}}</th>
    <th colspan="3" style="width: 50%">{{mb_title class=CTransmissionMedicale field=text}}</th>
    <th rowspan="2"></th>
  </tr>
  <tr>
    <th class="section" style="width: 17%">{{tr}}CTransmissionMedicale.type.data{{/tr}}</th>
    <th class="section" style="width: 17%">{{tr}}CTransmissionMedicale.type.action{{/tr}}</th>
    <th class="section" style="width: 17%">{{tr}}CTransmissionMedicale.type.result{{/tr}}</th>
  </tr>
  {{foreach from=$transmissions item=_suivi}}
    <tr>
      {{mb_include module=hospi template=inc_line_suivi show_patient=false force_new=true show_target=true}}
    </tr>
  {{/foreach}}
</table>
</div>
<br />

<button class="add" type="button"
  onclick="
    Control.Modal.close();
    {{if $transmission->object_id && $transmission->object_class}}
      addTransmission('{{$transmission->sejour_id}}', '{{$app->user_id}}', null, '{{$transmission->object_id}}', '{{$transmission->object_class}}');
    {{elseif $transmission->libelle_ATC}}
      addTransmission('{{$transmission->sejour_id}}', '{{$transmission->user_id}}', null, null, null, '{{$transmission->libelle_ATC|smarty:nodefaults|JSAttribute}}');
    {{/if}}
  ">
  Réouvrir la cible</button>