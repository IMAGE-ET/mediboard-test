<script>
function showBox(valeur_id){
  var url = new Url("dPurgences", "ajax_inc_vw_box_urgence");
  url.addParam("box_urgences_id", valeur_id);
  url.requestUpdate("box_urgence");
}
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <a href="#" onclick="PlanEtage.edit('0')" class="button new">
        {{tr}}CBoxUrgence-title-create{{/tr}}
      </a>
      
      <!-- Liste des boxs -->
      <table class="tbl">
        <tr>
          <th colspan="3" class="title">
            {{tr}}CBoxUrgence.all{{/tr}}
          </th>
        </tr>
        <tr>
          <th>{{mb_title class=CBoxUrgence field=nom}}</th>
          <th>{{mb_title class=CBoxUrgence field=description}}</th>
          <th>{{mb_title class=CBoxUrgence field=type}}</th>
        </tr>
    
        {{foreach from=$boxs item=_box}}
          <tr {{if $_box->_id == $_box->_id}} class="selected" {{/if}}>
            <td>
              <a href="#" onclick="PlanEtage.edit('{{$_box->_id}}')">
                {{mb_value object=$_box field=nom}}
              </a>
            </td>
            <td class="text">{{mb_value object=$_box field=description}}</td>
            <td class="text">{{mb_value object=$_box field=type}}</td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="2" class="empty">{{tr}}CBoxUrgence.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td> 
  
    <td class="halfPane" id="box_urgence">
      {{mb_include module=urgences template=inc_vw_box_urgence}}
    </td>
  </tr>
</table>
