{{if $pack->_id}}
<table class="tbl">
  <tr>
    <th class="title" colspan="6">
      <a style="float:right;" href="#nothing" onclick="view_log('{{$pack->_class_name}}', {{$pack->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
      </a>
      {{$pack->_view}}
    </th>
  </tr>
  
  <tr>
    <th>Examen</th>
    <th>Unité</th>
    <th>Références</th>
  </tr>
  
  <!-- Liste des items d'un pack -->
  {{foreach from=$pack->_ref_items_examen_labo item="curr_item"}}
  {{assign var="curr_examen" value=$curr_item->_ref_examen_labo}}
  <tr>
    <td>
      <div class="draggable" id="examen-{{$curr_examen->_id}}-{{$curr_item->_id}}">
      <script type="text/javascript">
        new Draggable('examen-{{$curr_examen->_id}}-{{$curr_item->_id}}', oDragOptions);
      </script>
      {{$curr_examen->_view}}
      </div>
      <form name="delPackItem-{{$curr_item->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPlabo" />
        <input type="hidden" name="dosql" value="do_pack_item_aed" />
        <input type="hidden" name="pack_examens_labo_id" value="{{$pack->_id}}" />
        <input type="hidden" name="pack_item_examen_labo_id" value="{{$curr_item->_id}}" />
        <input type="hidden" name="del" value="1" />
        <button type="button" class="trash notext" onclick="Pack.delExamen(this.form)">{{tr}}Delete{{/tr}}</button>
        <button type="button" class="search notext" onclick="ObjectTooltip.create(this, 'CExamenLabo', {{$curr_examen->_id}}, { popup: true })">
          view
        </button>
      </form>
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