{{*
  * list of astreintes of the curent week
  *  
  * @category Astreintes
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

{{mb_script module=astreintes script=plage}}

<button type="button" onclick="PlageAstreinte.modal()" class="new">Créer</button>

  <form method="get" action="">
    <input type="hidden" name="m" value="{{$m}}"/>
    <input type="hidden" name="tab" value="vw_list_astreinte"/>
    <select name="mode" onchange="this.form.submit();">
      <option value="day" {{if $mode == "day"}}selected="selected" {{/if}}>{{tr}}Day{{/tr}}</option>
      <option value="week" {{if $mode == "week"}}selected="selected" {{/if}}>{{tr}}Week{{/tr}}</option>
      <option value="month" {{if $mode == "month"}}selected="selected" {{/if}}>{{tr}}Month{{/tr}}</option>
      <option value="year" {{if $mode == "year"}}selected="selected" {{/if}}>{{tr}}Year{{/tr}}</option>
    </select>
  </form>

<table class="tbl">
  <tr>
    <th colspan="6" class="category">
      <a class="button notext left" href="?m={{$m}}&amp;mode={{$mode}}&amp;date={{$date_prev}}">Précédent</a>
        {{$today|date_format:$conf.longdate}}
      <a class="button notext right" href="?m={{$m}}&amp;mode={{$mode}}&amp;date={{$date_next}}">Suivant</a>
    </th>
  </tr>
  <tr>
    <th class="narrow"></th>
    <th>Libelle</th>
    <th>Utilisateur</th>
    <th>Dates</th>
    <th>Durée</th>
    <th>Type</th>
  </tr>
  {{foreach from=$astreintes item=_astreinte}}
    <tr>
      <td style="width:40px;"><button type="button" class="edit notext" onclick="PlageAstreinte.modal('{{$_astreinte->_id}}')">{{tr}}Modify{{/tr}}</button></td>
      <td style="background:#{{$_astreinte->_color}}; text-shadow:0 0 4px white;">{{$_astreinte->libelle}}</td>
      <td>{{mb_value object=$_astreinte->_ref_user field=_user_last_name}}<br/>
        <strong>{{mb_value object=$_astreinte field=phone_astreinte}}</strong></td>
      <td>
        {{mb_include module="system" template="inc_interval_datetime" from=$_astreinte->start to=$_astreinte->end}}
      </td>
      <td>{{mb_include module="system" template="inc_vw_duration" duration=$_astreinte->_duree}}</td>
      <td>{{mb_value object=$_astreinte field=type}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="6" class="empty">{{tr}}CPlageAstreinte.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>