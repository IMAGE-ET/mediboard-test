<div id="files-{{$dir}}-header" class="tree-header">
  <div id="files-{{$dir}}-trigger" class="tree-trigger">{{tr}}Toggle{{/tr}}</div>
  <div class="sniffer">
    {{if is_array($files)}}
      <button type="button" class="change notext singleclick" onclick="CodeSniffer.run(this);">{{tr}}Run{{/tr}}</button>
    {{else}}
      <button type="button" class="search notext singleclick" onclick="CodeSniffer.show(this);">{{tr}}Show{{/tr}}</button>
    {{/if}}
  </div>
  {{assign var=fullpath value=$dir|replace:":":"/"}}
  {{assign var=fullpath value=$fullpath|substr:9}}

  {{if !is_array($files)}}
    {{assign var=report value=$reports.$fullpath}}
    <div class="sniffed {{$report}}" title="{{tr}}tree-sniffed-report-{{$report}}{{/tr}}"></div>
  {{/if}}
  
  {{assign var=fullpath value=$fullpath|default:"-root-"}}
  {{assign var=stat value=$stats[$fullpath]}}
  {{if $stat}}
    {{assign var=count value=$stat.count}}
    {{math assign=width equation='100*min(1,(log10(count+1)/4))' count=$count format="%.0f"}}
    {{assign var=color value=lightgreen}}
    {{if $count >=    10}}{{assign var=color value=orange}}{{/if}}
    {{if $count >=   100}}{{assign var=color value=red   }}{{/if}}
    {{if $count >=  1000}}{{assign var=color value=brown }}{{/if}}
    {{if $count >= 10000}}{{assign var=color value=black }}{{/if}}
    
    <div class="sniff-bar" title="{{$count}} warnings">
      <div style="width:{{$width}}%; background: {{$color}};"></div>
    </div>
    <span style="font-family: monospace; white-space: pre;">[{{$count|pad:4}}]</span>
  {{/if}}

  <span class="basename" {{if is_array($files)}}style="font-weight: bold;"{{/if}}>
    {{$basename}}
  </span>
</div>

{{if is_array($files)}}
<div class="tree-content" id="files-{{$dir}}" style="display: block;">
  {{foreach from=$files key=_dir item=_files}}
  {{mb_include template=tree_sniffed_files dir="$dir:$_dir" basename=$_dir files=$_files}}
  {{/foreach}}
</div>
{{/if}}