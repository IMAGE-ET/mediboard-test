{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

        <tr>
          <th>
            <a href="?m=dPbloc&amp;tab=vw_edit_planning&amp;date={{$curr_day}}">
              <strong>{{$curr_day|date_format:"%a %d %b"}}</strong>
            </a>
          </th>
          {{foreach from=$listHours|smarty:nodefaults item=curr_hours}}
          <th colspan="4" class="heure">{{$curr_hours}}:00</th>
          {{/foreach}}
        </tr>
        {{foreach from=$listSalles item=curr_salle key=keySalle}}
        <tr>
          <td class="salle">{{$curr_salle->nom}}</td>
          {{foreach from=$listHours|smarty:nodefaults item=curr_hour}}
          {{foreach from=$listMins|smarty:nodefaults item=curr_min key=keymin}}
            {{assign var="keyAff" value="$curr_day-s$keySalle-$curr_hour:$curr_min:00"}}
            {{assign var=affichage value=$affichages.$keyAff}}
            
            {{if $affichage === "empty"}}
              <td class="empty{{if !$keymin}} firsthour{{/if}}"></td>
            {{elseif $affichage === "full"}}
           
            {{else}}
            {{assign var=_listPlages value=$listPlages.$curr_day}}
            {{assign var=plage value=$_listPlages.$affichage}}
         
              {{mb_ternary var=colorCell test=$plage->chir_id value=$plage->_ref_chir->_ref_function->color other=$plage->_ref_spec->color}}
             
              {{assign var="pct" value=$plage->_fill_rate}}
              {{if $pct gt 100}}
              {{assign var="pct" value=100}}
              {{/if}}
              {{if $pct lt 50}}{{assign var="backgroundClass" value="empty"}}
              {{elseif $pct lt 90}}{{assign var="backgroundClass" value="normal"}}
              {{elseif $pct lt 100}}{{assign var="backgroundClass" value="booked"}}
              {{else}}{{assign var="backgroundClass" value="full"}}
              {{/if}}
              <td nowrap="nowrap" style="vertical-align: top; text-align: center; white-space: normal; background-color:#{{$colorCell}};" colspan="{{$plage->_nbQuartHeure}}">
                <div class="progressBar" style="height: 3px;" title="{{$plage->_fill_rate}} % du temps occupé">
                  <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;height: 3px;border-right: 2px solid #000;">
                  </div>
                </div>
                {{if $can->edit}}
                <strong title="{{$plage->_fill_rate}} % du temps occupé">
                <a href="?m=dPbloc&amp;tab=vw_edit_interventions&amp;plageop_id={{$plage->plageop_id}}" title="Agencer les interventions">
                  {{$plage->_view}}
                </a>
                ({{$plage->_nb_operations_placees}}/{{$plage->_nb_operations}})
                </strong>
                <a href="?m=dPbloc&amp;tab=vw_edit_planning&amp;plageop_id={{$plage->plageop_id}}&amp;date={{$curr_day}}">
                  <img src="images/icons/edit.png" alt="Editer la plage" title="Editer la plage" border="0" height="16" width="16" />
                </a>
                {{if ($plage->_ref_affectations_personnel.op|@count) || ($plage->_ref_affectations_personnel.op_panseuse|@count)}}
                  <a href="?m=dPbloc&amp;tab=vw_edit_interventions&amp;plageop_id={{$plage->plageop_id}}">
                  <img src="images/icons/personnel.png" border="0" height="16" width="16"
                    alt="personnel"
                    onmouseover="$('plage-{{$plage->_id}}').show();"
                    onmouseout="$('plage-{{$plage->_id}}').hide();" />
                  </a>
                  <div id="plage-{{$plage->_id}}" class="tooltip" style="display: none; width: 200px;">
                    <table class="tbl">
                      <tr>
                        <th>{{tr}}CPersonnel.emplacement.op{{/tr}}</th>
                      </tr>
                      {{foreach from=$plage->_ref_affectations_personnel.op item=curr_aff}}
                      <tr>
                        <td class="text">
                          {{$curr_aff->_ref_personnel->_ref_user->_view}}
                        </td>
                      </tr>
                      {{foreachelse}}
                      <tr>
                        <td><em>{{tr}}None{{/tr}}</em></td>
                      </tr>
                      {{/foreach}}
                      <tr>
                        <th>{{tr}}CPersonnel.emplacement.op_panseuse{{/tr}}</th>
                      </tr>
                      {{foreach from=$plage->_ref_affectations_personnel.op_panseuse item=curr_aff}}
                      <tr>
                        <td class="text">
                          {{$curr_aff->_ref_personnel->_ref_user->_view}}
                        </td>
                      </tr>
                      {{foreachelse}}
                      <tr>
                        <td><em>{{tr}}None{{/tr}}</em></td>
                      </tr>
                      {{/foreach}}
                    </table>
                  </div>
                {{/if}}
                {{else}}
                <strong title="{{$plage->_fill_rate}} % du temps occupé">
                  {{$plage->_view}}
                  ({{$plage->_nb_operations_placees}}/{{$plage->_nb_operations}})
                </strong>
                {{/if}}
              </td>
            {{/if}}
           {{/foreach}}
          {{/foreach}}
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="100">{{tr}}CSalle.none{{/tr}}</td>
        </tr> 
        {{/foreach}} 