<?php
class controleur {
	
	public function erreur404() {
		http_response_code(404);
		(new vue)->erreur404();
	}

	public function verifierAttributsJson($objetJson, $listeDesAttributs) {
		$verifier = true;
		foreach($listeDesAttributs as $unAttribut) {
			if(!isset($objetJson->$unAttribut)) {
				$verifier = false;
			}
		}
		return $verifier;
	}

	public function getTaches() {
		$donnees = null;

		if(isset($_GET["id"])) {
			if((new tache)->exists($_GET["id"])) {
				http_response_code(200);
				$donnees = (new tache)->get($_GET["id"]);
			}
			else {
				http_response_code(404);
				$donnees = array("message" => "Tache introuvable");
			}
		}
		else {
			http_response_code(200);
			$donnees = (new tache)->getAll();
		}
		
		(new vue)->transformerJson($donnees);
	}

	public function ajouterTache() {
		$donnees = json_decode(file_get_contents("php://input"));
		$renvoi = null;
		if($donnees === null) {
			http_response_code(400);
			$renvoi = array("message" => "JSON envoyé incorrect");
		}
		else {
			$attributsRequis = array("description", "categorie", "priorite");
			if($this->verifierAttributsJson($donnees, $attributsRequis)) {
				$resultat = (new tache)->insert($donnees->description, $donnees->categorie, $donnees->priorite);
					
				if($resultat != false) {
					http_response_code(201);
					$renvoi = array("message" => "Ajout effectué avec succès", "id_tache" => $resultat);
				}
				else {
					http_response_code(500);
					$renvoi = array("message" => "Une erreur interne est survenue");
				}
			}
			else {
				http_response_code(400);
				$renvoi = array("message" => "Données manquantes");
			}
		}

		(new vue)->transformerJson($renvoi);
	}

	public function modifierTache() {
		$donnees = json_decode(file_get_contents("php://input"));
		$renvoi = null;
		if($donnees === null) {
			http_response_code(400);
			$renvoi = array("message" => "JSON envoyé incorrect");
		}
		else {
			$attributsRequis = array("id", "description", "categorie", "priorite");
			if($this->verifierAttributsJson($donnees, $attributsRequis)) {
				$resultat = (new tache)->update($donnees->id, $donnees->description, $donnees->categorie, $donnees->priorite);
					
				if($resultat != false) {
					http_response_code(200);
					$renvoi = array("message" => "Modification effectuée avec succès");
				}

				else {
					http_response_code(500);
					$renvoi = array("message" => "Une erreur interne est survenue");
				}
			}
			else {
				http_response_code(400);
				$renvoi = array("message" => "Données manquantes");
			}
		}

		(new vue)->transformerJson($renvoi);
	}

	public function supprimerTache() {
		$donnees = json_decode(file_get_contents("php://input"));
		$renvoi = null;
		if($donnees === null) {
			http_response_code(400);
			$renvoi = array("message" => "JSON envoyé incorrect");
		}
		else {
			$attributsRequis = array("id");
			if($this->verifierAttributsJson($donnees, $attributsRequis)) {
				if((new tache)->exists($donnees->id)) {
					$resultat = (new tache)->delete($donnees->id);
					
					if($resultat != false) {
						http_response_code(200);
						$renvoi = array("message" => "Suppression effectuée avec succès");
					}
					else {
						http_response_code(500);
						$renvoi = array("message" => "Une erreur interne est survenue");
					}
				}
				else {
					http_response_code(400);
					$renvoi = array("message" => "La machine spécifiée n'existe pas");
				}
			}
			else {
				http_response_code(400);
				$renvoi = array("message" => "Données manquantes");
			}
		}

		(new vue)->transformerJson($renvoi);
	}
}