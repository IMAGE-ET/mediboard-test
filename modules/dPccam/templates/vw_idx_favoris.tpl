<script type="text/javascript">

function pageMain() {
  PairEffect.initGroup("ChapEffect", { 
    bStoreInCookie: false,
    sEffect: "appear"
  });
}

</script>

<table class="bookCode">
  <tr>
    <th style="text-align: center;" colspan="4">
      Codes favoris
    </th>
  </tr>
  {{foreach from=$codesByChap item=curr_chap key=key_chap}}
  <tr id="chap{{$key_chap}}-trigger">
    <th colspan="4">
      {{$curr_chap.nom}} ({{$curr_chap.codes|@count}})
    </th>
  </tr>
  <tbody id="chap{{$key_chap}}" class="ChapEffect" style="display: none;">
  {{foreach from=$curr_chap.codes item=curr_code key=key_code}}
  {{if $key_code is div by 4}}
  <tr>
  {{/if}}
    <td>
      <strong>
        <a href="index.php?m={{$m}}&amp;tab=vw_full_code&amp;codeacte={{$curr_code->code}}">{{$curr_code->code}}</a>
      </strong>
      <br />

      {{$curr_code->libelleLong}}
      {{if $can->edit}}
      <br />
      <form name="delFavoris" action="index.php?m={{$m}}" method="post">
      <input type="hidden" name="dosql" value="do_favoris_aed" />
      <input type="hidden" name="del" value="1" />
      <input type="hidden" name="favoris_id" value="{{$curr_code->favoris_id}}" />
      <button class="trash" type="submit" name="btnFuseAction">
        Retirer de mes favoris
      </button>
	  </form>
	  {{/if}}
    </td>
  {{if ($key_code+1) is div by 4 or ($key_code+1) == $curr_chap.codes|@count}}
  </tr>
  {{/if}}
  {{/foreach}}
  </tbody>
  {{/foreach}}
</table>
