<script type="text/javascript">

Main.add( function(){
  prepareForm('addPrise{{$type}}{{$line->_id}}');
  prepareForm('ChoixPrise-{{$line->_id}}');
} );

</script>

{{assign var=line_id value=$line->_id}}
<div style="margin-top: 5px; margin-bottom: -14px;">
  <form name="ChoixPrise-{{$line->_id}}" action="" method="post" onsubmit="return false">
	  <input name="typePrise" type="radio" value="moment{{$type}}"   onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" /><label for="typePrise_moment{{$type}}"> Moment</label>
	  <input name="typePrise" type="radio" value="foisPar{{$type}}"  onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" /><label for="typePrise_foisPar{{$type}}"> x fois par y</label>
	  <input name="typePrise" type="radio" value="tousLes{{$type}}"  onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" /><label for="typePrise_tousLes{{$type}}"> Tous les x y</label>
	</form>
</div>

<br />

<form name="addPrise{{$type}}{{$line->_id}}" action="?" method="post" style="display: none;" onsubmit="testPharma({{$line->_id}}); return onSubmitPrise(this,'{{$type}}');">
  <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prise_posologie_id" value="" />
  <input type="hidden" name="object_id" value="{{$line->_id}}" />
  <input type="hidden" name="object_class" value="{{$line->_class_name}}" />

  {{mb_field object=$prise_posologie field=quantite size=3 increment=1 min=1 form=addPrise$type$line_id}}
  {{if $line->_class_name == "CPrescriptionLineMedicament" && $type != "mode_grille"}}
  <select name="unite_prise" style="width: 75px;">
    {{foreach from=$line->_unites_prise item=_unite}}
      <option value="{{$_unite}}">{{$_unite}}</option>
    {{/foreach}}
  </select>
  {{/if}}
  {{if $line->_class_name == "CPrescriptionLineElement"}}
    {{$line->_unite_prise}}
  {{/if}}
  
  <select name="moment_unitaire_id" style="width: 150px;"></select>
  
  <span id="foisPar{{$type}}{{$line->_id}}" style="display: none;">
    {{mb_field object=$prise_posologie field=nb_fois size=3 increment=1 min=1 form=addPrise$type$line_id}} fois par 
    {{mb_field object=$prise_posologie field=unite_fois}}
  </span>
  
  <span id="tousLes{{$type}}{{$line->_id}}" style="display: none;">
    <br />tous les
    {{mb_field object=$prise_posologie field=nb_tous_les size=3 increment=1 min=1 form=addPrise$type$line_id}}          
    {{mb_field object=$prise_posologie field=unite_tous_les}}
 (J+{{mb_field object=$prise_posologie field=decalage_prise size=1 increment=1 min="0" form=addPrise$type$line_id}})
  </span>
  
  {{if $line->_id}}
    <button type="button" class="add notext" onclick="this.form.onsubmit()">{{tr}}Save{{/tr}}</button>
  {{/if}}
</form>