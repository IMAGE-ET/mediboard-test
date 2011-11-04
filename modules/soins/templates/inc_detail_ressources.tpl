{{foreach from=$list_ressources key=ressource_id item=nb}}
  {{assign var=ressource value=$ressources.$ressource_id}}
  {{if $nb_unites}}
    {{$nb}}
  {{/if}}
  
  {{$ressource->code}}
  {{if $cout_euro}}
    = {{math equation=x*y x=$nb y=$ressource->cout}} &euro;
  {{/if}}
  <br />  
{{/foreach}}