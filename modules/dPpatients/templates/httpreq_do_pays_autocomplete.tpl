<ul>
  {{foreach from=$result item=pays}}
    <li>
    <span><strong>{{$pays.nom_fr|lower|capitalize}}</strong></span>
    </li>
  {{/foreach}}
</ul>