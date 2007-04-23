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
    <th class="category">Examen</th>
    <th class="category">Type</th>
    <th class="category">Unité</th>
    <th class="category">Min</th>
    <th class="category">Max</th>
  </tr>
  {{foreach from=$pack->_ref_items_examen_labo item="curr_item"}}
  {{assign var="curr_examen" value=$curr_item->_ref_examen_labo}}
  <tr>
    <td>
      <form name="delPackItem-{{$curr_item->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPlabo" />
        <input type="hidden" name="dosql" value="do_pack_item_aed" />
        <input type="hidden" name="pack_examens_labo_id" value="{{$pack->_id}}" />
        <input type="hidden" name="pack_item_examen_labo_id" value="{{$curr_item->_id}}" />
        <input type="hidden" name="del" value="1" />
        <button type="button" class="trash notext" style="float: right;" onclick="Pack.delExamen(this.form)">{{tr}}Delete{{/tr}}</button>
      </form>
      <div class="draggable" id="examen-{{$curr_examen->_id}}-{{$curr_item->_id}}">
      <script type="text/javascript">
        new Draggable('examen-{{$curr_examen->_id}}-{{$curr_item->_id}}', oDragOptions);
      </script>
      {{$curr_examen->_view}}
      </div>
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->type}}
      </a>
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->unite}}
      </a>
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->min}} {{$curr_examen->unite}}
      </a>
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->max}} {{$curr_examen->unite}}
      </a>
    </td>
  </tr>
  {{/foreach}}
</table>
{{/if}}