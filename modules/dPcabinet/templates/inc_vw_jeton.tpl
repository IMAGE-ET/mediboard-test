{{mb_default var=nb value=0}}

<div title="{{$nb}}">
  {{assign var=mode_button value=1}}
  {{if $mode_button}}
    {{if $nb}}
      {{math equation="floor(x/10)" x=$nb assign=dizaines}}
      {{if $dizaines > 0}}
        {{foreach from=1|range:$dizaines item=i}}
          {{math equation="x*y" x="1" y=$i assign=margin}}
          <div class="jeton_dizaine"></div>
        {{/foreach}}
      {{/if}}
      {{math equation="x-10*y" x=$nb y=$dizaines assign=reste}}
      {{if $reste > 0}}
        {{foreach from=1|range:$reste item=i}}
          {{math equation="x*y" x="1" y=$i assign=margin}}
          <div class="jeton_unite"></div>
        {{/foreach}}
      {{/if}}
    {{/if}}
  {{else}}
    {{$nb}}
  {{/if}}
</div>