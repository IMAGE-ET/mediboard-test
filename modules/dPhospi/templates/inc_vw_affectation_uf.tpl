{{mb_script module=dPhospi script=affectation_uf}}

<form name="affect_uf" action="?m={{$m}}" method="post" onsubmit="return AffectationUf.onSubmit(this);" style="text-align:left;">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_affectation_aed" />
  
  <input type="hidden" name="affectation_id" value="{{$affectation->_id}}" />
  <input type="hidden" name="del" value="0" />

    <fieldset>
      <legend>{{tr}}CAffectation-uf_hebergement_id{{/tr}}</legend>
       <table class="form">  
				{{foreach from=$hebergement key=nom item=objects   name=lesservices}}
					<tr>
						<th style="width: 20%">{{if $smarty.foreach.lesservices.last}}Lit
                {{elseif  $smarty.foreach.lesservices.first}}Service
                {{else}}Chambre{{/if}}
            </th>
		        <th style="width: 15%;">{{$nom}}</th>
						
		        <td>
		        {{foreach from=$objects item=_object  name=service}}                 
		          {{$_object->_ref_uf->libelle}}{{if !$smarty.foreach.service.last}},{{/if}}
		        {{/foreach}}
		        </td>
					</tr>
				{{/foreach}}
	      <tr>
	        <th></th><th></th>
	         <td>
	        {{foreach from=$choixhebergment item=_choix}}
	            <input type="radio" name="uf_hebergement_id" value="{{$_choix->_ref_uf->_id}}"{{if $affectation->uf_hebergement_id == $_choix->_ref_uf->_id}}checked{{/if}}>  {{$_choix->_ref_uf->libelle}}  
	        {{/foreach}}
	        </td>
	      </tr>
      </table>
    </fieldset>
    
    <fieldset id="soins"  >
      <legend>{{tr}}CAffectation-uf_soins_id{{/tr}}</legend>
       <table class="form"> 
        <tr >
        	<th style="width: 20%">Service</th>
          <th style="width: 15%">{{$nomservice}}</th>
           <td>
          {{foreach from=$services item=service  name=servicesoin}}                 
            {{$service->_ref_uf->libelle}}{{if !$smarty.foreach.servicesoin.last}},{{/if}}
          {{/foreach}}
          </td>
        </tr>
        <tr>
          <th></th>
					<th></th>
           <td>
          {{foreach from=$choixsoins item=_choix}}
              <input type="radio" name="uf_soins_id" value="{{$_choix->_ref_uf->_id}}" {{if $affectation->uf_soins_id == $_choix->_ref_uf->_id}}checked{{/if}}> {{$_choix->_ref_uf->libelle}}   
          {{/foreach}}
          </td>
        </tr>
      </table>
    </fieldset>
    
    <fieldset >
      <legend>{{tr}}CAffectation-uf_medicale_id{{/tr}}</legend>
       <table class="form"> 
			 {{foreach from=$medical key=nom item=objects name=medicale}}
          <tr>
          	<th style="width: 20%">{{if $smarty.foreach.medicale.last}}Praticien{{else}}Fonction{{/if}}</th>
            <th style="width: 15%">{{$nom}}</th>
            <td>
            {{foreach from=$objects item=_object  name=service}}                 
              {{$_object->_ref_uf->libelle}}{{if !$smarty.foreach.service.last}},{{/if}}
            {{/foreach}}
            </td>
          </tr>
        {{/foreach}}
      <tr>
        <th></th>
        <th></th>
         <td>
        {{foreach from=$choixmedical item=_choix}}
        <label>
            <input type="radio" name="uf_medicale_id" value="{{$_choix->_ref_uf->_id}}" {{if $affectation->uf_medicale_id == $_choix->_ref_uf->_id}}checked{{/if}}> 
            {{$_choix->_ref_uf->libelle}}
            </label> 
        {{/foreach}}
        </td>
      </tr>
      </table>
    </fieldset>
    
    
        <button class="submit" type="submit">{{tr}}Ajouter{{/tr}}</button>
</form>