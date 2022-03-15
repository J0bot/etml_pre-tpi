<?php 
//Auteur : José Carlos Gasser
//Date : 14.03.2022
 //Description : Classe Database et tout ce qui concerne le model

    /**
     * Cette classe va gérer toute la communication avec la database mysql
     */
    class Database
    {
        

        //Cette variable est notre objet pdo, il va nous servir à nous amuser avec la db dès qu'il sera lancé
        public $connector;

    
        /**
         * créer un nouvel objet PDO et se connecter à la BD
         */
        public function __construct()
        {
            //On va inclure le array qui contient les infos de connection
            include('config.php');

            //On va utiliser PDO pour parler avec la db, et pour ce faire il faut commencer par se connecter
            try{
                $dsn = "mysql:host=" . $login["ip"] . ";dbname=" . $login["db"]. ";charset=utf8"; 
                $this->connector=new PDO($dsn,$login['user'],$login['pass']);
            
                // Activer le mode exeption du pdo
                $this->connector->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                //Ce echo servait de debug
                //echo "On est dans la matrice";
            }
            //Si on arrive pas à se connecter ça va nous trow un message d'erreur
            catch(PDOException $e){
                echo "Impossible de se connecter à la base de données, avec le code d'erreur : \n";
                echo $e->getMessage();
            }
        }   

        /**
         * permet de préparer et d’exécuter une requête de type simple (sans where)
         *
         * @param string $query
         * @return array
         */
        private function querySimpleExecute($query)
        {
            $req = $this->connector->query($query);
            $dataArray = $this->formatData($req);
            $this->unsetData($req);
            return $dataArray;
        }

        /**
         * permet de préparer, de binder et d’exécuter une requête (select avec where ou insert, update et delete)
         *
         * @param string $query
         * @param array $binds
         * @return array
         */
        private function queryPrepareExecute($query, $binds)
        {
            $req = $this->connector->prepare($query);

            foreach($binds as $bind)
            {
                $req->bindValue($bind['marker'], $bind['var'], $bind['type']);
            }
            $req->execute();

            $dataArray = $this->formatData($req);

            $this->unsetData($req);

            return $dataArray;
        }

        /**
         * traiter les données d'un array pour les retourner en tableau associatif (avec PDO::FETCH_ASSOC)
         *
         * @param object $req : object pdo
         * @return array formaté
         */
        private function formatData($req)
        {
            return $req->fetchAll(PDO::FETCH_ASSOC);
        }

        /**
         * vide le jeu d’enregistrement
         *
         * @param object $req : object pdo
         * @return void
         */
        private function unsetData($req)
        {
            $req->closeCursor();
        }

        public function getState()
        {
            $query = "SELECT swiValue FROM t_switch WHERE idSwitch=1";
            $dataArray = $this->querySimpleExecute($query);
            if(isset($dataArray[0]["swiValue"]))
            {
                if($dataArray[0]["swiValue"]==1)
                {
                    return 1;
                }
            }
            return 0;

        }
        
        public function changeState($state)
        {
            $state = (int)($state);
            $query ="UPDATE t_switch SET swiValue=:swiValue WHERE idSwitch = 1";
            $binds = array (
                0 => array (
                    'var' => $state,
                    'marker' => ':swiValue',
                    'type'  => PDO::PARAM_INT
                )
            );
            $this->queryPrepareExecute($query,$binds);
        }


        /**
         * Fonction de destructeur
         */
        public function __destruct()
        {
            $this->connector = null;
        }
    }
?>