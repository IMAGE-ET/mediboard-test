<table class="main">
  <tr>
    <td class="halfPane">
      <a href="#" onclick="showInfrastructure('service_id', '0', 'infrastructure_service')" class="button new">
        {{tr}}CService-title-create{{/tr}}
      </a>
      
      <!-- Liste des services -->
      <table class="tbl">
        <tr>
          <th colspan="3" class="title">
            {{tr}}CService.all{{/tr}}
          </th>
        </tr>
        <tr>
          <th>{{mb_title class=CService field=nom}}</th>
          <th>{{mb_title class=CService field=description}}</th>
        </tr>
    
        {{foreach from=$services item=_service}}
          <tr {{if $_service->_id == $service->_id}} class="selected" {{/if}}>
            <td {{if $_service->cancelled}} class="cancelled" {{/if}}>
              <a href="#" onclick="showInfrastructure('service_id', '{{$_service->_id}}', 'infrastructure_service')">
                {{mb_value object=$_service field=nom}}
              </a>
            </td>
            <td class="text">{{mb_value object=$_service field=description}}</td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="2" class="empty">{{tr}}CService.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td> 
  
    <td class="halfPane" id="infrastructure_service">
      {{mb_include module=hospi template=inc_vw_service}}
    </td>
  </tr>
</table>
