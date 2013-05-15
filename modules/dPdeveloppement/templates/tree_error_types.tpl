{{assign var=items     value=$item.items}}
{{assign var=children value=false}}
{{if is_array($items) && !array_key_exists(0, $items)}}
  {{assign var=children value=true}}
{{/if}}

<div id="types-{{$dir}}-header" class="tree-header">
  <div id="types-{{$dir}}-trigger" class="tree-trigger">{{tr}}Toggle{{/tr}}</div>

  {{assign var=count value=$item.count}}
  {{if $count}}
    {{math assign=width equation='100*min(1,(log10(count+1)/4))' count=$count format="%.0f"}}
    {{assign var=color value=lightgreen}}
    {{if $count >=    10}}{{assign var=color value=orange}}{{/if}}
    {{if $count >=   100}}{{assign var=color value=red   }}{{/if}}
    {{if $count >=  1000}}{{assign var=color value=brown }}{{/if}}
    {{if $count >= 10000}}{{assign var=color value=black }}{{/if}}
    
    <div class="sniff-bar" title="{{$count}} warnings">
      <div style="width:{{$width}}%; background: {{$color}};"></div>
    </div>
    
  {{/if}}
  <span style="font-family: monospace; white-space: pre;">[{{$count|pad:4}}]</span>
  <span class="typename"  {{if $children}} style="font-weight: bold;" {{/if}} >
    {{$type}}
  </span>
</div>

{{assign var=items value=$item.items}}
{{if $children}}
<div class="tree-content" id="types-{{$dir}}" style="display: block;">
  {{foreach from=$items key=_type item=_item}}
    {{mb_include template=tree_error_types dir="$dir.$_type" type=$_type item=$_item}}
  {{/foreach}}
</div>
{{/if}}