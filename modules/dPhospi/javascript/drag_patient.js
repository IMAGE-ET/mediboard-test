
Main.add(function(){
	var elements;
	var divGauche = $('list-patients-non-placees');
	var divDroite = $('grille');
	
	//Tous les éléments draggables
	elements=divGauche.select('div');
	elements.each(function(e) {
		  new Draggable( e, {revert:true});
	});
	
	//Toutes les zones droppables
	Droppables.add(divGauche,{onDrop:TraiterDrop});
	
	elements=divDroite.select('td');
	elements.each(function(e) {
		if(e.select('div').length<e.getAttribute("data-nb-lits")){
			Droppables.add(e,{onDrop:TraiterDrop});			
		}
	});
});

function TraiterDrop(element, zoneDrop)
{
	zoneDrop.appendChild(element);
	savePlan(element, zoneDrop);
	if(zoneDrop.select('div').length==zoneDrop.getAttribute("data-nb-lits")){
		Droppables.remove(zoneDrop);		
	}
}

function savePlan(element, zoneDrop){
	var url=new Url("dPhospi", "ajax_creation_affectation");
	
	url.addParam("sejour_id",element.getAttribute("data-sejour-id"));
	url.addParam("entree",element.getAttribute("data-entree"));
	url.addParam("sortie",element.getAttribute("data-sortie"));
	url.addParam("lit_id",zoneDrop.getAttribute("data-lit-id"));
	
	url.requestUpdate(SystemMessage.id);
}