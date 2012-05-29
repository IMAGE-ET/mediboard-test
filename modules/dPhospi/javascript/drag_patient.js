
Main.add(function(){
	var elements;
	var divGauche = $('list-patients-non-placees');
	
	//Tous les éléments draggables
	elements=divGauche.select('div.draggable');
	elements.each(function(e) {
		  new Draggable( e, {	revert:true, 
								scroll: window, 
								ghosting: true});
	});
	
	//Toutes les zones droppables
	Droppables.add(divGauche,{onDrop:TraiterDrop});
	
	elements=$$('td.chambre');
	elements.each(function(e) {
		if(e.select('div.patient').length<e.getAttribute("data-nb-lits")){
			Droppables.add(e,{onDrop:TraiterDrop});			
		}
	});
});

function TraiterDrop(element, zoneDrop)
{
	element.addClassName("patient");
	zoneDrop.appendChild(element);
	savePlan(element, zoneDrop);
	if(zoneDrop.select('div.patient').length==zoneDrop.getAttribute("data-nb-lits")){
		Droppables.remove(zoneDrop);		
	}
}

function savePlan(element, zoneDrop){
	element.style.width="120px";
	var url=new Url("dPhospi", "do_affectation_aed");
	
	url.addParam("del", 0);
	url.addParam("sejour_id",element.getAttribute("data-sejour-id"));
	url.addParam("entree",element.getAttribute("data-entree"));
	url.addParam("sortie",element.getAttribute("data-sortie"));
	url.addParam("lit_id",zoneDrop.getAttribute("data-lit-id"));
	
	url.requestUpdate(SystemMessage.id);
}