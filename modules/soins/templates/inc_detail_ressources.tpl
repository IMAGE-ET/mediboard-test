{{if count($list_ressources)}} 
<table class="layout">
  {{foreach from=$list_ressources key=ressource_id item=nb}}
    <tr>
    {{assign var=ressource value=$ressources.$ressource_id}}
      <td style="text-align: right">
        {{if $nb_unites}}
          {{$nb}}
        {{/if}}
      </td>
      <td>{{$ressource->code}}</td>
    
      {{if $show_cost && $total}}
      <td>=</td>
      <td style="text-align: right">
        {{math equation=x*y x=$nb y=$ressource->cout}} 
        {{$conf.currency_symbol}}
      </td>
      {{/if}}
  </tr>
  {{/foreach}}
</table> 
{{/if}}
