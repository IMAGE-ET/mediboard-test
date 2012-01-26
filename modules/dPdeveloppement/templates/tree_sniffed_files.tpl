<div id="{{$dir}}-header" class="tree-header">
	<div id="{{$dir}}-trigger" class="tree-trigger">{{tr}}Toggle{{/tr}}</div>  
  <div class="sniffer">
    {{if is_array($files)}}
      <button type="button" class="down notext" onclick="CodeSniffer.run(this);">{{tr}}Run{{/tr}}</button>
    {{else}}
      <button type="button" class="search notext" onclick="CodeSniffer.show(this);">{{tr}}Show{{/tr}}</button>
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
    <span>{{*$stat.count*}}</span>
  {{/if}}
  <span class="basename" {{if is_array($files)}}style="font-weight: bold;"{{/if}}>
    {{$basename}}
  </span>
</div>
{{if is_array($files)}}
<div class="tree-content" id="{{$dir}}" style="display: block;">
  {{foreach from=$files key=_dir item=_files}}
  {{mb_include template=tree_sniffed_files basename=$_dir dir="$dir:$_dir" files=$_files}}
  {{/foreach}}
</div>
{{/if}}