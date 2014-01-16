<script>
  Main.add(function () {
    PairEffect.initGroup("serviceEffect");
  });
</script>
<table class="main">
  <tr>
    <td class="halfPane">
      <a class="button new" href="#" onclick="showLit('chambre_id', '0','lit_id', '0','infrastructure_chambre')">
        {{tr}}CChambre-title-create{{/tr}}
      </a>
    
      <table class="tbl">
        <tr>
          <th colspan="3" class="title">
            {{tr}}CChambre.all{{/tr}}
          </th>
        </tr>
        <tr>
          <th>{{mb_title class=CChambre field=nom}}</th>
          <th>{{mb_title class=CChambre field=caracteristiques}}</th>
          <th>{{tr}}CChambre-back-lits{{/tr}}</th>
        </tr>
    
        {{foreach from=$services item=_service}}
          <tr id="{{$_service->_guid}}-trigger">
            <td colspan="4">{{$_service}}</td>
          </tr>
          <tbody class="serviceEffect" id="{{$_service->_guid}}">
          
            {{foreach from=$_service->_ref_chambres item=_chambre}}
              <tr {{if $_chambre->_id == $chambre->_id}} class="selected" {{/if}}>
                <td>
                  <a href="#" onclick="showLit('chambre_id', '{{$_chambre->_id}}','lit_id', '0', 'infrastructure_chambre')">
                    {{$_chambre}}
                  </a>
                </td>
          
                <td class="text">
                  {{mb_value object=$_chambre field=caracteristiques}}
                </td>
        
                {{if $_chambre->annule}} 
                  <td class="cancelled">
                    {{mb_title object=$_chambre field=annule}}
                  </td>
                {{else}}
                  <td>
                    {{foreach from=$_chambre->_ref_lits item=_lit}}
                      <a href="#" onclick="showLit('chambre_id', '{{$_lit->chambre_id}}','lit_id', '{{$_lit->_id}}', 'infrastructure_chambre')">
                        {{$_lit->nom}}
                        {{if $_lit->nom_complet}}
                          ({{$_lit->nom_complet}})
                        {{/if}}
                      </a>
                    {{/foreach}}
                  </td>
                {{/if}}
              </tr>
            {{foreachelse}}
              <tr>
                <td colspan="3" class="empty">{{tr}}CChambre.none{{/tr}}</td>
              </tr>
            {{/foreach}}
          </tbody>
        {{foreachelse}}
          <tr>
            <td colspan="3" class="empty">{{tr}}CChambre.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  
    <td class="halfPane" id="infrastructure_chambre">
      {{mb_include module=hospi template=inc_vw_chambre}}
    </td>
  </tr>
</table>