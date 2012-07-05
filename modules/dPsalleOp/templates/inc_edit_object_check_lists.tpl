<script type="text/javascript">
printCheckList = function(id){
  var url = new Url('dPsalleOp', 'print_check_list_pose_disp_vasc');
  url.addParam('pose_disp_vasc_id', id);
  url.popup(800, 600, 'check_list');
}
</script>

<button class="print" onclick="printCheckList('{{$object->_id}}')" style="float: right;">{{tr}}Print{{/tr}}</button>

<h1>{{tr}}{{$object->_class}}{{/tr}}</h1>

{{if $object instanceof CPoseDispositifVasculaire}}
  <table class="main tbl">
    <tr>
      <td>
        <strong>{{mb_label object=$object field=date}}</strong>: {{mb_value object=$object field=date}}<br />
        <strong>{{mb_label object=$object field=lieu}}</strong>: {{mb_value object=$object field=lieu}}<br />
        <strong>{{mb_label object=$object field=urgence}}</strong>: {{mb_value object=$object field=urgence}}<br />
      </td>
      <td>
        <strong>{{mb_label object=$object field=operateur_id}}</strong>: {{mb_value object=$object field=operateur_id}}<br />
        <strong>{{mb_label object=$object field=encadrant_id}}</strong>: {{mb_value object=$object field=encadrant_id}}<br />
      </td>
      <td>
        <strong>{{mb_label object=$object field=type_materiel}}</strong>: {{mb_value object=$object field=type_materiel}}<br />
        <strong>{{mb_label object=$object field=voie_abord_vasc}}</strong>: {{mb_value object=$object field=voie_abord_vasc}}<br />
      </td>
    </tr>
  </table>
{{/if}}

<table class="main form" id="checkList-container">
  {{foreach from=$check_item_categories item=_cat key=_key}}
    <col style="width: 33%" />
  {{/foreach}}
  
  <tr class="{{$type_group}}">
    {{foreach from=$check_item_categories item=_cat key=_key}}
    <td class="button" id="{{$_key}}-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$check_lists.$_key->_readonly|ternary:"tick":"cross"}}.png" />
        {{tr}}CDailyCheckItemCategory.type.{{$_key}}{{/tr}}
      </h3>
    </td>
    {{/foreach}}
  </tr>
  
  <tr class="{{$type_group}}">
    {{foreach from=$check_item_categories item=_cat key=_key}}
      <td style="padding:0;">
        <div id="{{$_key}}">
        {{assign var=check_list value=$check_lists.$_key}}
        {{mb_include module=salleOp template=inc_edit_check_list 
             check_item_categories=$_cat
             personnel=$validateurs_list}}
        </div>
      </td>
    {{/foreach}}
  </tr>
  
  <tr>
    <td colspan="3" class="button text">
      <hr />
      Le rôle du coordonnateur check-list sous la responsabilité du(es) chirurgien(s) et anesthésiste(s) responsables 
      de l'intervention est de ne cocher les items de la check list  que (1) si la vérification a bien été effectuée,  
      (2) si elle a été faite oralement en présence des membres de l'équipe concernée et (3) si les non conformités (marquées d'un *) 
      ont fait l'objet d'une concertation en équipe et d'une décision qui doit le cas échéant être rapportée dans l'encart spécifique.
    </td>
  </tr>
</table>