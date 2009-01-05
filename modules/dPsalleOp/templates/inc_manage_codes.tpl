<!-- Pas d'affichage de inc_manage_codes si la consultation est deja validée -->
<form name="manageCodes" action="?m={{$module}}" method="post">
  <input type="hidden" name="m" value="{{$subject->_ref_module->mod_name}}" />
  <input type="hidden" name="dosql" value="{{$do_subject_aed}}" />
  <input type="hidden" name="{{$subject->_spec->key}}" value="{{$subject->_id}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="codes_ccam" value="{{$subject->codes_ccam}}" />
  <input type="submit" disabled="disabled" style="display:none;"/>
  <input type="hidden" name="_chir" value="{{$subject->_praticien_id}}" />
  {{if ($subject->_class_name=="COperation")}}
  <input type="hidden" name="_anesth" value="{{$subject->_ref_plageop->anesth_id}}" />
  {{/if}}
  <input type="hidden" name="_class_name" value="{{$subject->_class_name}}" />
      
  {{if $can->edit || $modif_operation}}
  {{if ($subject->_class_name=="COperation")}}
  <table class="form">
    <tr>
     
      <th style="vertical-align:middle; text-align: right">
        {{mb_label object=$subject field=anapath}}
      </th>
      <td style="vertical-align:middle;">
        {{mb_field object=$subject field=anapath onChange="submitFormAjax(this.form, 'systemMsg');"}}
      </td>
     
      <th style="vertical-align:middle; text-align: right">
        {{mb_label object=$subject field=labo}}
      </th>
      <td style="vertical-align:middle;">     
        {{mb_field object=$subject field=labo onChange="submitFormAjax(this.form, 'systemMsg');"}}
      </td>
     
    </tr>
  </table>
  {{/if}}
  {{/if}}
  <table class="form">
    <tr>
      <th class="category">Ajout et suppression de codes</th>
    </tr>
    <tr>
      <td colspan="2" style="vertical-align:middle;">
        <input name="_actes" type="hidden" value="" />
        <select name="_selCode">
          <option value="0">&mdash; Choisir un code à supprimer</option>
          {{foreach from=$subject->_associationCodesActes item=curr_code}}
          <option value="{{$curr_code.code}}" onclick="this.form._actes.value = '{{$curr_code.ids}}'">
            {{$curr_code.code|truncate:7:""|capitalize}}
          </option>
          {{/foreach}}
        </select>
        {{if $can->edit || $modif_operation}}
        <button class="trash" type="button" onclick="ActesCCAM.remove({{$subject->_id}})">
          Supprimer
        </button>
        {{/if}}
      </td>
    </tr>
    <tr>
      {{if $can->edit || $modif_operation}}
      <td colspan="2" style="vertical-align:middle;">
        <button class="search" type="button" onclick="CCAMSelector.init()">
          Rechercher un code à ajouter
        </button>
     
        <script type="text/javascript">   
          CCAMSelector.init = function(){
            this.sForm = "manageCodes";
            this.sClass = "_class_name";
            this.sChir = "_chir";
            {{if ($subject->_class_name=="COperation")}}
            this.sAnesth = "_anesth";
            {{/if}}
            this.sView = "_newCode";
          this.pop();
          }
        </script>
        
        <input type="text" size="10" name="_newCode" />
        
        <button class="tick" type="button" onclick="ActesCCAM.add('{{$subject->_id}}','{{$subject->_praticien_id}}')">
          {{tr}}Ajouter{{/tr}}
        </button>        
      </td>
      {{/if}}
    </tr>
  </table>
</form>

{{if $ajax}}
<script type="text/javascript">

oCodesManagerForm = document.manageCodes;
prepareForm(oCodesManagerForm);

</script>
{{/if}}