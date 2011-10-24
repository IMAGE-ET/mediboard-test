{{* $Id: edit_plage_consultation.tpl$  *}}

{{*
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: 11962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="form" id="maj_plage">
      <tr>
        {{if !$plageSel->_id}}
        <th class="title" colspan="4">Créer une plage</th>

        {{else}}
        <th class="title modify" colspan="4">
          {{mb_include module=system template=inc_object_idsante400 object=$plageSel}}
          {{mb_include module=system template=inc_object_history    object=$plageSel}}
          Modifier cette plage
        </th>
        {{/if}}
      </tr>
      <tr>
        <td>
          <form name='editFrm' action='?m=dPcabinet' method='post' onsubmit='return checkPlage()'>
          <input type='hidden' name='dosql' value='do_plageconsult_multi_aed' />
          <input type='hidden' name='del' value='0' />
          {{mb_key object=$plageSel}}
          
          <input type='hidden' name='nbaffected' value='{{$plageSel->_affected}}' />
          <input type='hidden' name='_firstconsult_time' value='{{$_firstconsult_time}}' />
          <input type='hidden' name='_lastconsult_time' value='{{$_lastconsult_time}}' />
          <table class="form">
            <tr>
              <th>{{mb_label object=$plageSel field="chir_id"}}</th>
              <td>
                <select name="chir_id" class="{{$plageSel->_props.chir_id}}" style="width: 15em;">
                  <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                  {{mb_include module=mediusers template=inc_options_mediuser list=$listChirs selected=$chirSel}}
                </select>
              </td>
              <th>{{mb_label object=$plageSel field="libelle"}}</th>
              <td>{{mb_field object=$plageSel field="libelle" style="width: 15em;"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$plageSel field="_hour_deb"}}</th>
              <td><select name="_hour_deb" class="notNull num">
                {{foreach from=$listHours item=curr_hour}}
                  <option value="{{$curr_hour|string_format:"%02d"}}" {{if $curr_hour == $plageSel->_hour_deb}} selected="selected" {{/if}}>
                    {{$curr_hour|string_format:"%02d"}}
                  </option>
                {{/foreach}}
                </select> h
                <select name="_min_deb">
                  {{foreach from=$listMins item=curr_min}}
                    <option value="{{$curr_min|string_format:"%02d"}}" {{if $curr_min == $plageSel->_min_deb}} selected="selected" {{/if}}>
                      {{$curr_min|string_format:"%02d"}}
                    </option>
                  {{/foreach}}
                  {{if !in_array($plageSel->_min_deb, $listMins)}}
                    <option value="{{$plageSel->_min_deb|string_format:"%02d"}}" selected="selected">
                      {{$plageSel->_min_deb|string_format:"%02d"}}
                    </option>
                  {{/if}}
                </select> min
              </td>
              <th>{{mb_label object=$plageSel field="date"}}</th>
              <td>
                <select name="date" class="{{$plageSel->_props.date}}" style="width: 15em;">
                  <option value="">&mdash; Choisir le jour</option>
                  {{foreach from=$listDaysSelect item=curr_day}}
                  <option value="{{$curr_day}}" {{if $curr_day == $plageSel->date}} selected="selected" {{/if}}>
                    {{$curr_day|date_format:"%A"}}
                  </option>
                  {{/foreach}}
                </select>
              </td>
            </tr>     
            <tr>
              <th>{{mb_label object=$plageSel field="_hour_fin"}}</th>
              <td>
                <select name="_hour_fin" class="notNull num moreEquals|_hour_deb">
                  {{foreach from=$listHours item=curr_hour}}
                    <option value="{{$curr_hour|string_format:"%02d"}}" {{if $curr_hour == $plageSel->_hour_fin}} selected="selected" {{/if}}>
                      {{$curr_hour|string_format:"%02d"}}
                    </option>
                  {{/foreach}}
                </select> h
                <select name="_min_fin">
                  {{foreach from=$listMins item=curr_min}}
                    <option value="{{$curr_min|string_format:"%02d"}}" {{if $curr_min == $plageSel->_min_fin}} selected="selected" {{/if}}>
                      {{$curr_min|string_format:"%02d"}}
                    </option>
                  {{/foreach}}
                  {{if !in_array($plageSel->_min_fin, $listMins)}}
                    <option value="{{$plageSel->_min_fin|string_format:"%02d"}}" selected="selected">
                      {{$plageSel->_min_fin|string_format:"%02d"}}
                    </option>
                  {{/if}}
                </select> min
              </td>
              <th><label for="_repeat" title="Nombre de plages à créer">Nombre de plages</label></th>
              <td><input type="text" size="2" name="_repeat" value="1" /></td>
            </tr>      
            <tr>
              <th>{{mb_label object=$plageSel field="_freq"}}</th>
              <td>
                <select name="_freq">
                  <option value="05" {{if ($plageSel->_freq == "05")}} selected="selected" {{/if}}>05</option>
                  <option value="10" {{if ($plageSel->_freq == "10")}} selected="selected" {{/if}}>10</option>
                  <option value="15" {{if ($plageSel->_freq == "15") || (!$plageSel->_id)}} selected="selected" {{/if}}>15</option>
                  <option value="20" {{if ($plageSel->_freq == "20")}} selected="selected" {{/if}}>20</option>
                  <option value="30" {{if ($plageSel->_freq == "30")}} selected="selected" {{/if}}>30</option>
                  <option value="45" {{if ($plageSel->_freq == "45")}} selected="selected" {{/if}}>45</option>
               </select> min
              </td>
              <th>
                <label for="_type_repeat" title="Espacement des plages">Type de répétition</label>
              </th>
              <td>
                <select name="_type_repeat" style="width: 15em;">
                  <option value="1">Toutes les semaines</option>
                  <option value="2">Une semaine sur 2</option>
                  <option value="3">Une semaine sur 3</option>
                  <option value="4">Une semaine sur 4</option>
                  <option value="5">Une semaine sur 5</option>
                  <option value="6">Une semaine sur 6</option>
                  <option value="7">Une semaine sur 7</option>
                  <option value="8">Une semaine sur 8</option>
                  <option value="9">Une semaine sur 9</option>
                </select>
              </td>
            </tr>
            <tr>
              <td colspan="2"></td>
              <th>{{mb_label object=$plageSel field="locked"}}</th>
              <td>{{mb_field object=$plageSel field="locked" typeEnum="checkbox"}}</td>
            </tr>
            <tr>
              <td colspan="4" class="text">
                <div class="small-info">
                  Pour modifier plusieurs plages (nombre de plages > 1),
                  veuillez ne pas changer les champs début et fin en même temps
                </div>
              </td>
            </tr>
            <tr>
              {{if !$plageSel->_id}}
              <td class="button" colspan="4"><button type="submit" class="submit">{{tr}}Create{{/tr}}</button></td>
              {{else}}
              <td class="button" colspan="4"><button type="submit" class="modify">{{tr}}Modify{{/tr}}</button></td>
              {{/if}}
            </tr>
          </table>
          </form>
      
          {{if $plageSel->_id}}
        <form name='removeFrm' action='?m=dPcabinet' method='post'>
          <input type='hidden' name='dosql' value='do_plageconsult_multi_aed' />
          <input type='hidden' name='del' value='1' />
          {{mb_key object=$plageSel}}
          
          <table class="form">
          <tr>
            <th class="title modify" colspan="2">Supprimer cette plage</th>
          </tr>
          <tr>
            <th>Supprimer cette plage pendant</th>
            <td><input type='text' name='_repeat' size="1" value='1' /> semaine(s)</td>
          </tr>
          <tr>
            <td class="button" colspan="2">
              <button class="trash" type='button' onclick="confirmDeletion(this.form,{typeName:'la plage de consultations du',objName:'{{$plageSel->date|date_format:$conf.longdate}}'})">
                {{tr}}Delete{{/tr}}
              </button>
            </td>
          </tr>
        </table>
      </form>
        {{/if}}        
        </td>
      </tr>
    </table>