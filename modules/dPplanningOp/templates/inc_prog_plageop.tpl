<script type="text/javascript">
  ObjectTooltip.modes.allergies = {  
    module: "patients",
    action: "ajax_vw_allergies",
    sClass: "tooltip"
  };
</script>

<table class="tbl">
  <col style="width: 25px;" />
  
  <tr>
    <th class="category" colspan="4">
      Programme du {{mb_value object=$plageop field=date}}
      <br />
      {{$plageop->debut|date_format:$conf.time}} -
      {{$plageop->fin|date_format:$conf.time}}
      &mdash; {{$plageop->_ref_salle->_view}}
    </th>
  </tr>
  
  {{if $plageop->_ref_operations|@count == 0}}
    <tr>
      <td colspan="3">{{tr}}COperation.none{{/tr}}</td>
    </tr>
  {{else}}
    
    {{assign var=rank_desired value="-1"}}
    <tr>
      <td colspan="4">
        <button style="float: right;" class="tick" type="button" onclick="setClose('', '')">{{tr}}OK{{/tr}}</button>
        <button style="float: right;" class="cancel" type="button" onclick="window._close()">{{tr}}Cancel{{/tr}}</button>
        <label class="insert">
          <input type="radio" name="_place_after_interv_id" value="0" checked="checked" />
          Sans préférence pour le placement
        </label>
      </td>
    </tr>
    
    {{assign var=place_after_interv_id value=-1}}
    {{assign var=is_placed value=true}}
    
    {{foreach from=$plageop->_ref_operations item=_operation name=operations}}
      {{if !$_operation->rank && $is_placed}}
        <tr>
          <td colspan="4" style="background: #ddd;">
            <label class="insert" title="Placer l'intervention de préférence ici">
              <input type="radio" name="_place_after_interv_id" value="{{$place_after_interv_id}}" /><div></div>
              {{mb_value object=$_operation field=_horaire_voulu}}
            </label>
          </td>
        </tr>
      {{else}}
        <tr>
          <td colspan="4" style="background: #ddd; padding: 1px;"></td>
        </tr>
      {{/if}}
      
      {{if $_operation->_id}}
        {{mb_include module=planningOp template=inc_prog_plageop_line operation=$_operation}}
        {{assign var=place_after_interv_id value=$_operation->_id}}
      {{/if}}
      
      {{if !($_operation->rank || $_operation->horaire_voulu)}}
        {{assign var=is_placed value=false}}
      {{/if}}
      
    {{/foreach}}
  {{/if}}
</table>
