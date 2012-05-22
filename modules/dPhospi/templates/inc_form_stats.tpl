<script type="text/javascript">
  Main.add(function(){
    var form = getForm("filter_{{$type}}");
    Calendar.regField(form.date_min);
    Calendar.regField(form.date_max);
  });
</script>

<form name="filter_{{$type}}" method="get" action="?"
  onsubmit="refreshStats('{{$type}}', $V(this.date_min), $V(this.date_max), $V(this.service_id)); return false;">
  <table class="form">
    <tr>
      <th colspan="3" class="category">Critères de filtre</th>
    </tr>
    <tr>
      <td>
        A partir du <input type="hidden" name="date_min" class="date notNull" value="{{$date_min}}" "/>
      </td>
      <td>
        Jusqu'au <input type="hidden" name="date_max" class="date notNull" value="{{$date_max}}" "/>
      </td>
      <td>
        {{if $type == "occupation"}}
          <span style="float: right;">
            <button type="button" class="search" onclick="viewLegend()">Légende</button>
          </span>
        {{/if}}
        Service
        <select name="service_id" onchange="this.form.onsubmit();">
          <option value="">&mdash; Tous les services</option>
          {{foreach from=$services item=_service}}
            <option value="{{$_service->_id}}" {{if $_service->_id == $service_id}}selected="selected"{{/if}}>{{$_service}}</option>
          {{/foreach}}
        </select>
        <button type="button" onclick="this.form.onsubmit();" class="tick">Envoyer</button>
        {{if $type == "occupation"}}
          <label>
            <input type="checkbox" name="display_stat[ouvert]" value="1" onclick="filtreOccupation()"
              {{if isset($display_stat.ouvert|smarty:nodefaults)}}checked="checked"{{/if}} /> Ouvert
          </label>
          <label>
            <input type="checkbox" name="display_stat[prevu]" value="1" onclick="filtreOccupation()"
              {{if isset($display_stat.prevu|smarty:nodefaults)}}checked="checked"{{/if}} /> Prévu
          </label>
          <label>
            <input type="checkbox" name="display_stat[reel]" value="1" onclick="filtreOccupation()"
              {{if isset($display_stat.reel|smarty:nodefaults)}}checked="checked"{{/if}} /> Réel
          </label>
          <label>
            <input type="checkbox" name="display_stat[entree]" value="1" onclick="filtreOccupation()"
              {{if isset($display_stat.entree|smarty:nodefaults)}}checked="checked"{{/if}} /> Entrées
          </label>
        {{/if}}
      </td>
    </tr>
  </table>
</form>