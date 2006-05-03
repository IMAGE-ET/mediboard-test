      <table class="tbl">
        <tr>
          <th colspan="4">
            <a style="display: inline;" href="index.php?m={$m}&amp;tab={$tab}&amp;date={$lastmonth}">&lt;&lt;&lt;</a>
            {$date|date_format:"%B %Y"}
            <a style="display: inline;" href="index.php?m={$m}&amp;tab={$tab}&amp;date={$nextmonth}">&gt;&gt;&gt;</a>
          </th>
        <tr>
          <th class="text">Date</th>
          <th class="text"><a href="index.php?m={$m}&amp;tab={$tab}&amp;selAdmis=0&amp;selSaisis=0">Toutes les admissions</a></th>
          <th class="text"><a href="index.php?m={$m}&amp;tab={$tab}&amp;selAdmis=0&amp;selSaisis=n">Dossiers non préparés</a></th>
          <th class="text"><a href="index.php?m={$m}&amp;tab={$tab}&amp;selAdmis=n&amp;selSaisis=0">Admissions non effectuées</a></th>
        </tr>
        {foreach from=$list1 item=curr_list}
        <tr>
          <td align="right">
            <a href="index.php?m={$m}&amp;tab={$tab}&amp;date={$curr_list.date|date_format:"%Y-%m-%d"}">
            {$curr_list.date|date_format:"%A %d"}
            </a>
          </td>
          <td align="center">
            {$curr_list.num}
          </td>
          <td align="center">
            {$curr_list.num3}
          </td>
          <td align="center">
            {$curr_list.num2}
          </td>
        </tr>
        {/foreach}
      </table>