<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.create("ufs_type");
  });
</script>

<table class="main">
  
<tr>
  <td class="halfPane">
    <a href="#" onclick="showInfrastructure('uf_id', '0', 'infrastructure_uf')" class="button new">
      {{tr}}CUniteFonctionnelle-title-create{{/tr}}
    </a>
    
    <ul id="ufs_type" class="control_tabs">
      {{foreach from=$ufs item=_ufs key=type}}
        <li>
          <a href="#{{$type}}" {{if $_ufs|@count == 0}}class="empty"{{/if}}>
            {{tr}}CUniteFonctionnelle.type.{{$type}}{{/tr}}
            <small>({{$_ufs|@count}})</small>
          </a>
        </li>
      {{/foreach}}
    </ul>
    
    <hr class="control_tabs" />
    
    {{foreach from=$ufs item=_ufs key=type}}
      <div id="{{$type}}" style="display: none;">
        <table class="tbl">
          <tr>
            <th>{{mb_title class=CUniteFonctionnelle field=code}}</th>
            <th>{{mb_title class=CUniteFonctionnelle field=libelle}}</th>
            <th>{{mb_title class=CUniteFonctionnelle field=description}}</th>
          </tr>
      
          {{foreach from=$_ufs item=_uf}}
          <tr {{if $_uf->_id == $uf->_id}}class="selected"{{/if}}>
            <td>
              <a href="#" onclick="showInfrastructure('uf_id', '{{$_uf->_id}}', 'infrastructure_uf')">
                {{mb_value object=$_uf field=code}}
              </a>
            </td>
            <td class="text">{{mb_value object=$_uf field=libelle}}</td>
            <td class="text">{{mb_value object=$_uf field=description}}</td>
          </tr>
          {{foreachelse}}
            <tr>
              <td colspan="3" class="empty">
                {{tr}}CUniteFonctionnelle.none{{/tr}}
              </td>
            </tr>
          {{/foreach}}
        </table>
      </div>
    {{/foreach}}
  </td> 

  <td class="halfPane" id="infrastructure_uf">
    {{mb_include module=hospi template=inc_vw_uf}}
  </td>
</tr>

</table>
