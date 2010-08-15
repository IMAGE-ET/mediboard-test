<script type="text/javascript">
Main.add(function () {
  PairEffect.initGroup("serviceEffect");
  Control.Tabs.create('tabs-chambres', true);
});
</script>

<ul id="tabs-chambres" class="control_tabs">
  <li><a href="#chambres">{{tr}}CChambre{{/tr}}</a></li>
  <li><a href="#services">{{tr}}CService{{/tr}}</a></li>
  <li><a href="#prestations">{{tr}}CPrestation{{/tr}}</a></li>
</ul>
<hr class="control_tabs" />

<div id="chambres" style="display: none;">
<table class="main">
<tr>
  <td class="halfPane">
    <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;chambre_id=0">
      {{tr}}CChambre-title-create{{/tr}}
    </a>
		
    <table class="tbl">   
    <tr>
      <th colspan="3" class="title">
      	{{tr}}CChambre.all{{/tr}}
			</th>
    </tr>  
    <tr>
      <th>{{mb_title class=CChambre field=nom}}</th>
      <th>{{mb_title class=CChambre field=caracteristiques}}</th>
      <th>{{tr}}CChambre-back-lits{{/tr}}</th>
    </tr> 
		
	{{foreach from=$services item=_service}}
	<tr id="{{$_service->_guid}}-trigger">
	  <td colspan="4">{{$_service}}</td>
	</tr>
    <tbody class="serviceEffect" id="{{$_service->_guid}}">
     {{foreach from=$_service->_ref_chambres item=_chambre}}
      <tr {{if $_chambre->_id == $chambre->_id}} class="selected" {{/if}}>
        <td>
        	<a href="?m={{$m}}&amp;tab={{$tab}}&amp;chambre_id={{$_chambre->_id}}&amp;lit_id=0">
        		{{$_chambre}}
					</a>
				</td>
				
        <td class="text">
          {{mb_value object=$_chambre field=caracteristiques}}
				</td>

        {{if $_chambre->annule}} 
        <td class="cancelled">
          {{mb_title object=$_chambre field=annule}}
        </td>
        {{else}}
        <td>
          {{foreach from=$_chambre->_ref_lits item=_lit}}
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;chambre_id={{$_lit->chambre_id}}&amp;lit_id={{$_lit->_id}}">
              {{$_lit}}
            </a>
          {{/foreach}}
        </td>
        {{/if}}

      </tr>
      {{/foreach}}
    </tbody>
    {{/foreach}}   
    </table>
  </td>
	
  <td class="halfPane">
    <form name="Edit-CChambre" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
    <input type="hidden" name="dosql" value="do_chambre_aed" />
    <input type="hidden" name="del" value="0" />
		{{mb_key object=$chambre}}

    <table class="form">
    <tr>
      {{if $chambre->_id}}
      <th class="title modify text" colspan="2">
        {{mb_include module=system object=$chambre template=inc_object_notes     }}
        {{mb_include module=system object=$chambre template=inc_object_idsante400}}
	      {{mb_include module=system object=$chambre template=inc_object_history   }}
        {{tr}}CChambre-title-modify{{/tr}} '{{$chambre}}'
      {{else}}
      <th class="title text" colspan="2">
        {{tr}}CChambre-title-create{{/tr}}
      </th>
      {{/if}}
    </tr>
    
	  <tr>
      <th>{{mb_label object=$chambre field=nom}}</th>
      <td>{{mb_field object=$chambre field=nom}}</td>
    </tr>
		
	  <tr>
      <th>{{mb_label object=$chambre field=service_id}}</th>
		  <td>{{mb_field object=$chambre field=service_id options=$services}}</td>
    </tr>    
    
		<tr>
      <th>{{mb_label object=$chambre field=caracteristiques}}</th>
      <td>{{mb_field object=$chambre field=caracteristiques}}</td>
    </tr>
    
		<tr>
      <th>{{mb_label object=$chambre field=annule}}</th>
      <td>{{mb_field object=$chambre field=annule}}</td>
    </tr>
    
		<tr>
      <td class="button" colspan="2">
        {{if $chambre->_id}}
        <button class="submit" type="submit">{{tr}}Validate{{/tr}}</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la chambre',objName: $V(this.form.nom) })">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
    </table>
		
	</form>
	
  {{if $chambre->_id}}
  <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;chambre_id={{$chambre->_id}}&amp;lit_id=0">
    {{tr}}CLit-title-create{{/tr}}
  </a>
	
	<table class="tbl">
    <tr>
      <th class="category" colspan="2">
        {{tr}}CChambre-back-lits{{/tr}}
      </th>
    </tr>
    {{foreach from=$chambre->_ref_lits item=_lit}}
    <tr {{if $lit->_id == $_lit->_id}} class="selected" {{/if}}>
      <td>
        {{mb_include module=system template=inc_object_notes object=$_lit}}
        {{mb_include module=system template=inc_object_idsante400 object=$_lit}}
        {{mb_include module=system template=inc_object_history    object=$_lit}}
        <a href="?m={{$m}}&amp;tab={{$tab}}&amp;chambre_id={{$_lit->chambre_id}}&amp;lit_id={{$_lit->_id}}">
        	{{$_lit}}
				</a>
			</td>
    </tr>
    {{/foreach}}
	</table>
  
	<form name="Edit-CLit" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  	
  <input type="hidden" name="dosql" value="do_lit_aed" />
  <input type="hidden" name="del" value="0" />
	{{mb_key object=$lit}}

  <input type="hidden" name="chambre_id" value="{{$chambre->_id}}" />

    <table class="form">
    <tr>
      <th>{{mb_label object=$lit field=nom}}</th>
      <td>
        {{mb_field object=$lit field=nom}}
        {{if $lit->_id}}
        <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le lit', objName: $V(this.form.nom)})">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
    </table>
  </form>
  {{/if}}    
  </td>
</tr>
</table>
</div>

<div style="display: none;" id="services"   >
  {{include file="vw_idx_services.tpl"}}
</div>

<div style="display: none;" id="prestations">
  {{include file="vw_idx_prestations.tpl"}}
</div>