<ul>
  {{foreach from=$result item=pays}}
    <li>
	    <span><strong>{{$pays.nom_fr|lower|capitalize}}</strong></span>
	    <span style="display:none"> - </span>
	    <span style="display:none">{{$pays.numerique}}</span>
    </li>
  {{/foreach}}
</ul>