<table class="bookCode">
  <tr>
    <th colspan="4">
      Codes favoris
    </th>
  </tr>
  {{foreach from=$codes item=curr_code key=curr_key}}
  {{if $curr_key is div by 4}}
  <tr>
  {{/if}}
    <td>
      <strong>
        <a href="index.php?m={{$m}}&amp;tab=vw_full_code&amp;codeacte={{$curr_code->code}}">{{$curr_code->code}}</a>
      </strong>
      <br />

      {{$curr_code->libelleLong}}
      <br />
      <form name="delFavoris" action="./index.php?m={{$m}}" method="post">
      <input type="hidden" name="dosql" value="do_favoris_aed" />
      <input type="hidden" name="del" value="1" />
      <input type="hidden" name="favoris_id" value="{{$curr_code->favoris_id}}" />
      <button class="trash" type="submit" name="btnFuseAction">
        Retirer de mes favoris
      </button>
	  </form>
    </td>
  {{if ($curr_key+1) is div by 4 or ($curr_key+1) == $codes|@count}}
  </tr>
  {{/if}}
  {{/foreach}}
</table>
