{{if $catalogue->_id}}
<table class="tbl">
  <tr>
    <th class="title" colspan="6">
      <a style="float:right;" href="#nothing" onclick="view_log('{{$catalogue->_class_name}}', {{$catalogue->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
      </a>
      {{$catalogue->_view}}
    </th>
  </tr>

  <tr>
    <th>Analyse</th>
    <th>Unité</th>
    <th>Références</th>
  </tr>

  {{foreach from=$catalogue->_ref_examens_labo item="curr_examen"}}
  <tr>
    <td>
      <div class="draggable" id="examenCat-{{$curr_examen->_id}}">
      <script type="text/javascript">
        new Draggable('examenCat-{{$curr_examen->_id}}', oDragOptions);
      </script>
      {{$curr_examen->_view}}
      </div>
      <button type="button" class="search notext" onclick="ObjectTooltip.create(this, 'CExamenLabo', {{$curr_examen->_id}}, { popup: true })">
        view
      </button>
    </td>
    {{if $curr_examen->type == "num"}}
    <td>{{$curr_examen->unite}}</td>
    <td>{{$curr_examen->min}} &ndash; {{$curr_examen->max}}</td>
    {{else}}
    <td colspan="2">{{mb_value object=$curr_examen field="type"}}</td>
    {{/if}}
  </tr>
  {{/foreach}}
</table>
{{/if}}