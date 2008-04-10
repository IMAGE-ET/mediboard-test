<ul>
{{foreach from=$allergies item=allergie}}
<li>
{{if $allergie->date}}
  {{$allergie->date|date_format:"%d/%m/%Y"}}:
{{/if}}
{{$allergie->rques}}
</li>
{{/foreach}}
</ul>