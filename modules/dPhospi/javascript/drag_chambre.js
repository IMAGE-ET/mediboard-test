
Main.add(function(){
	var elements;
	var divGauche = $('list-chambres-non-placees');
	
	//Tous les éléments draggables: les div
	elements = $$('div.chambre');
	elements.each(function(e) {
	  new Draggable( e, {revert:true});
	});

	//Toutes les zones droppables: les td
	Droppables.add(divGauche,{onDrop:TraiterDrop});
	
	elements = $$('td.conteneur-chambre');
	elements.each(function(e) {
		Droppables.add(e,{onDrop:TraiterDrop});
	});
});

function TraiterDrop(element, zoneDrop)
{	
	zoneDrop.insert(element);// Ajouter un fils à 'zoneDrop'
	savePlan(element);// sauvegarde automatique à chaque déplacement	
}

function savePlan(element){
	var url=new Url("dPhospi", "ajax_modif_plan_etage");
	url.addParam("chambre_id",element.getAttribute("data-chambre-id"));
	
	//Si la chambre se situe sur la grille
	if(element.parentNode.get("x") && element.parentNode.get("y")){
		url.addParam("plan_x",element.parentNode.get("x"));
		url.addParam("plan_y",element.parentNode.get("y"));
	}
	else{
		url.addParam("plan_x", null);
		url.addParam("plan_y", null);
	}
	url.requestUpdate(SystemMessage.id);
}