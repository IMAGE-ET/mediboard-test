{{foreach from=$ex_class_categories item=_category}}
  {{if $_category->ex_class_category_id}}
    <h2 style="margin: 0.3em 0.5em; padding-left: 0.5em; border-left: 1em solid #{{$_category->color}};">{{$_category}}</h2>
  {{/if}}

  {{foreach from=$_category->_ref_ex_classes item=_ex_class}}
    {{assign var=_ex_class_id value=$_ex_class->_id}}

    {{if array_key_exists($_ex_class_id,$ex_objects)}}
      {{assign var=_ex_objects value=$ex_objects.$_ex_class_id}}

      <h3 style="margin: 0.3em 1em;">{{$_ex_class->name}}</h3>

      {{foreach from=$_ex_objects item=_ex_object name=_ex_object}}
        <h4 style="margin: 0.3em 0.5em;">
          {{mb_value object=$_ex_object field=datetime_create}} -
          {{mb_value object=$_ex_object field=owner_id}}
        </h4>
        {{mb_include module=forms template=inc_vw_ex_object ex_object=$_ex_object hide_empty_groups=true}}
        <hr />
      {{/foreach}}
    {{/if}}
  {{foreachelse}}
    <span class="empty">Aucun formulaire</span>
  {{/foreach}}
{{/foreach}}