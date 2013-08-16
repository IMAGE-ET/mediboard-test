{{mb_default var=small value=false}}

<span style="{{if !$small}} font-size: 16px; width: 16px; {{else}} width: 12px; {{/if}} line-height: 12px; display: inline-block;
text-align: center; font-family: 'Lucida Sans Unicode', 'Arial Unicode MS', sans-serif;">{{$axis->getSymbolChar()|smarty:nodefaults}}</span>
