<form name="editPackItem" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPlabo" />
  <input type="hidden" name="dosql" value="do_pack_item_aed" />
  <input type="hidden" name="pack_item_examen_labo_id" value="" />
  <input type="hidden" name="examen_labo_id" value="" />
  <input type="hidden" name="pack_examens_labo_id" value="" />
  <input type="hidden" name="del" value="0" />
</form>

{{foreach from=$listPacks item="curr_pack"}}
<div class="tree-header {{if $curr_pack->_id == $pack->_id}}selected{{/if}}" id="drop-pack-{{$curr_pack->_id}}">
  {{if $dragPacks}}
  <script type="text/javascript">
    new Draggable('pack-{{$curr_pack->_id}}', oDragOptions);
  </script>
  {{else}}
  <script type="text/javascript">
  Droppables.add('drop-pack-{{$curr_pack->_id}}', {
    onDrop: function(element) {
      Pack.dropExamen(element.id, {{$curr_pack->_id}})
    }, 
    hoverclass:'selected'
  } );
  </script>
  {{/if}}
  <div style="float:right;">
    {{$curr_pack->_ref_items_examen_labo|@count}} Examens
  </div>
  <div {{if $dragPacks}}class="draggable"{{/if}} id="pack-{{$curr_pack->_id}}">
    <a href="#nothing" onclick="Pack.select({{$curr_pack->_id}})">
      {{$curr_pack->_view}}
    </a>
  </div>
</div>
{{/foreach}}