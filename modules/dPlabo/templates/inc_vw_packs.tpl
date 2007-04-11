<form name="editPackItem" id="newPackItem" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPlabo" />
  <input type="hidden" name="dosql" value="do_pack_item_aed" />
  <input type="hidden" name="pack_item_examen_labo_id" value="" />
  <input type="hidden" name="examen_labo_id" value="" />
  <input type="hidden" name="pack_examens_labo_id" value="" />
  <input type="hidden" name="del" value="0" />
</form>

{{foreach from=$listPacks item="curr_pack"}}
{{assign var="examens" value=$curr_pack->_ref_examens_labo|@count}}
<div class="tree-header" id="drop-pack-{{$curr_pack->_id}}">
  <script>
  Droppables.add('drop-pack-{{$curr_pack->_id}}', { 
                  onDrop:function(element){
                    dragDropExamen(element.id,{{$curr_pack->_id}})
                  }, 
                  hoverclass:'selected'
                });
  </script>
  <div style="float:right;">
    {{$curr_pack->_ref_examens_labo|@count}} Examens
  </div>
  <div class="tree-trigger" id="pack-{{$curr_pack->_id}}-trigger">showHide</div>  
  {{$curr_pack->_view}}
</div>
{{if $examens}}
<div class="tree-content pack-tree" id="pack-{{$curr_pack->_id}}" style="display: block;">
  {{foreach from=$curr_pack->_ref_examens_labo item="curr_examen"}}
  <div class="tree-header">
    {{$curr_examen->_view}}
  </div
  {{/foreach}}
</div>
{{/if}}
{{/foreach}}

<script type="text/javascript">

PairEffect.initGroup('pack-tree', {
  bStoreInCookie: false,
  bStartVisible: true
} );

</script>