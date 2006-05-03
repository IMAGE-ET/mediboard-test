{literal}
<script type="text/javascript">

function pageMain() {
  initEffectClass("groupallocated", "triggerallocated");
  initEffectClass("groupnotallocated", "triggernotallocated");
}

</script>
{/literal}

<table class="tbl">
  <tr>
    <th />
    {foreach from=$listDays item=curr_day}
    {assign var="myMonth" value=$curr_day|date_format:"%m"}
    <th>
      <a href="index.php?m={$m}&amp;tab=vw_affectations&amp;date={$curr_day}">
    {$curr_day|date_format:"%a %d %b %y"}
      </a>
    </th>
    {/foreach}
  </tr>

  <!-- Allocated -->
  {foreach from=$mainTab.allocated.functions item=curr_function}
  {if $curr_function.class != "allocated"}
  <tr class="triggerShow" id="triggerallocated" onclick="flipEffectElement('groupallocated', 'SlideDown', 'SlideUp', 'triggerallocated')">
    <td>{$curr_function.text}</td>
    {foreach from=$curr_function.days item=curr_day}
      <td>{$curr_day.nombre}</td>
    {/foreach}
  </tr>
  {/if}
  {/foreach}
  <tbody id="groupallocated" style="display:none">
  {foreach from=$mainTab.allocated.functions item=curr_function}
    {if $curr_function.class == "allocated"}
    <tr>
      <td style="background:#{$curr_function.color}">{$curr_function.text}</td>
      {foreach from=$curr_function.days item=curr_day}
        <td>{$curr_day.nombre}</td>
      {/foreach}
    </tr>
    {/if}
  {/foreach}
  </tbody>

  <!-- Not Allocated -->
  {foreach from=$mainTab.notallocated.functions item=curr_function}
  {if $curr_function.class != "notallocated"}
  <tr class="triggerShow" id="triggernotallocated" onclick="flipEffectElement('groupnotallocated', 'SlideDown', 'SlideUp', 'triggernotallocated')">
    <td>{$curr_function.text}</td>
    {foreach from=$curr_function.days item=curr_day}
      <td>{$curr_day.nombre}</td>
    {/foreach}
  </tr>
  {/if}
  {/foreach}
  <tbody id="groupnotallocated" style="display:none">
  {foreach from=$mainTab.notallocated.functions item=curr_function}
    {if $curr_function.class == "notallocated"}
    <tr>
      <td style="background:#{$curr_function.color}">{$curr_function.text}</td>
      {foreach from=$curr_function.days item=curr_day}
        <td>{$curr_day.nombre}</td>
      {/foreach}
    </tr>
    {/if}
  {/foreach}
  </tbody>
</table>