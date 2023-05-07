<?php

    class tache {
        private $pdo;

        public function __construct() {
            $config = parse_ini_file("config.ini");
            
            try {
                $this->pdo = new \PDO("mysql:host=".$config["host"].";dbname=".$config["database"].";charset=utf8", $config["user"], $config["password"]);
            } catch(Exception $e) {
                echo $e->getMessage();
            }
        }

        public function getAll() {
            $sql = "SELECT * FROM tache ORDER BY priorite_tache";
            
            $req = $this->pdo->prepare($sql);
            $req->execute();
            
            return $req->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function get($id) {
            $sql = "SELECT * FROM tache WHERE id_tache = :id";
            
            $req = $this->pdo->prepare($sql);
            $req->bindParam(':id', $id, PDO::PARAM_INT);
            $req->execute();
            
            return $req->fetch(\PDO::FETCH_ASSOC);
        }

        public function exists($id) {
            $sql = "SELECT COUNT(*) AS nb FROM tache WHERE id_tache = :id";
            
            $req = $this->pdo->prepare($sql);
            $req->bindParam(':id', $id, PDO::PARAM_INT);
            $req->execute();
            
            $nb = $req->fetch(\PDO::FETCH_ASSOC)["nb"];
            if($nb == 1) {
                return true;
            }
            else {
                return false;
            }
        }

        public function insert($descTache, $categorieTache, $prioriteTache) {
            $sql = "INSERT INTO tache (desc_tache, categorie_tache, priorite_tache) VALUES (:desc_tache, :categorie_tache, :priorite_tache)";
            
            $req = $this->pdo->prepare($sql);
            $req->bindParam(':desc_tache', $descTache, PDO::PARAM_STR);
            $req->bindParam(':categorie_tache', $categorieTache, PDO::PARAM_STR);
            $req->bindParam(':priorite_tache', $prioriteTache, PDO::PARAM_STR);
            $result = $req->execute();
            if($result === true) {
                return $this->pdo->lastInsertId();
            }
            else {
                return false;
            }
        }

        public function update($id, $descTache, $categorieTache, $prioriteTache) {
            $sql = "UPDATE tache SET desc_tache = :desc_tache, categorie_tache = :categorie_tache, priorite_tache = :priorite_tache WHERE id_tache = :id_tache";
            
            $req = $this->pdo->prepare($sql);
            $req->bindParam(':id_tache', $id, PDO::PARAM_INT);
            $req->bindParam(':desc_tache', $descTache, PDO::PARAM_STR);
            $req->bindParam(':categorie_tache', $categorieTache, PDO::PARAM_STR);
            $req->bindParam(':priorite_tache', $prioriteTache, PDO::PARAM_STR);
            return $req->execute();
        }

        public function delete($id) {
            $sql = "DELETE FROM tache WHERE id_tache = :id_tache";
            
            $req = $this->pdo->prepare($sql);
            $req->bindParam(':id_tache', $id, PDO::PARAM_INT);
            return $req->execute();
        }
    }

?>