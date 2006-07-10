<ul>
  {foreach from=$result item=insee}
    <li>
    <span><strong>{$insee.code_postal}</strong></span>
    <span> - </span>
    <span>{$insee.commune|lower|capitalize}</span>
    </li>
  {/foreach}
</ul>