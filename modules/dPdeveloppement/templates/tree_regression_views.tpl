<div id="files-{{$dir}}-header" class="tree-header">
  <div id="files-{{$dir}}-trigger" class="tree-trigger">{{tr}}Toggle{{/tr}}</div>
  <div class="regression">
    {{if !is_array($views)}}
      <button type="button" class="change notext singleclick" onclick="RegressionChecker.run(this);">
        {{tr}}Run{{/tr}}
      </button>
    {{else}}
      <button type="button" class="search notext singleclick" onclick="RegressionChecker.show(this);">
        {{tr}}Show{{/tr}}
      </button>
    {{/if}}
  </div>

  {{assign var=fullpath value=$dir|replace:":":"/"}}
  {{assign var=fullpath value=$fullpath|substr:9}}
  {{assign var=fullpath value=$fullpath|default:"-root-"}}

  <span class="basename" {{if is_array($views)}}style="font-weight: bold;"{{/if}}>
    {{$basename}}
  </span>
</div>

{{if is_array($views)}}
<div class="tree-content" id="files-{{$dir}}" style="display: block;">
  {{foreach from=$views key=_dir item=_views}}
  {{mb_include template=tree_regression_views dir="$dir:$_dir" basename=$_dir views=$_views}}
  {{/foreach}}
</div>
{{/if}}