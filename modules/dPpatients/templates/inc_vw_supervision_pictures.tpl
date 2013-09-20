{{foreach from=$tree key=_name item=_subtree}}
  {{mb_include module=dPpatients template=inc_supervision_picture_tree tree=$_subtree name=$_name depth=0}}
{{/foreach}}