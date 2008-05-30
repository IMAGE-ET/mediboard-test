{{mb_include_script module=system script="mb_object"}}

<h2 class="module {{$m}}">Fusion de médecins</h2>

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_medecins_fusion" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="medecin1_id" value="{{$medecin1->medecin_id}}" />
<input type="hidden" name="medecin2_id" value="{{$medecin2->medecin_id}}" />

<table class="form">
  <tr>
    <th class="category">Champ</th>
    <th width="30%" class="category">1er medecin</th>
    <th width="30%" class="category">2ème medecin</th>
    <th width="30%" class="category">Résultat</th>
  </tr>
  
  {{assign var=object1 value=$medecin1}}
  {{assign var=object2 value=$medecin2}}
  {{assign var=object_final value=$finalMedecin}}
  {{assign var=i value=0}}
  
  {{include field="nom"             file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="prenom"          file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="adresse"         file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="cp"              file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="ville"           file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="tel"             file="inc_acc/inc_fusion_field_tel.tpl" field_name=""}}{{assign var=i value=$i+5}}
  {{include field="fax"             file="inc_acc/inc_fusion_field_tel.tpl" field_name=""}}{{assign var=i value=$i+5}}
  {{include field="email"           file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="disciplines"     file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="orientations"    file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="complementaires" file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  <tr>
    <td class="button" colspan="4">
      <button type="button" class="search" onclick="MbObject.viewBackRefs('{{$medecin1->_class_name}}', ['{{$medecin1->_id}}', '{{$medecin2->_id}}']);">
        {{tr}}CMbObject-merge-moreinfo{{/tr}}
      </button>

      <button type="submit" class="submit">
        {{tr}}Merge{{/tr}}
      </button>
    </td>
  </tr>
</table>

</form>