<!-- $Id$ -->

{foreach from=$listCr item=curr_cr}
  <h1 class="newpage">Nouveau document</h1>
  <p>{$curr_cr->document}</p>
{/foreach}