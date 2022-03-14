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
        
        /**
         * récupère la liste de tous les enseignants de la BD , mais seulement ceux qui ne sont pas deleted
         *
         * @return array avec leur id, nom, prénom, surnom
         */
        public function getAllTeachers()
        {
            $query = "SELECT idTeacher,teaVoix, teaFirstName, teaName, teaNickname FROM t_teacher WHERE teaIsDeleted=0 ORDER BY teaVoix DESC";
            $dataArray = $this->querySimpleExecute($query);

            return $dataArray;
        }

        /**
         * récupère la liste de tous les enseignants de la BD, mais seulement ceux qui sont deleted
         *
         * @return array avec leur id, nom, prénom, surnom
         */
        public function getAllDeletedTeachers()
        {
            $query = "SELECT idTeacher, teaFirstName, teaName, teaNickname FROM t_teacher WHERE teaIsDeleted=1";
            $dataArray = $this->querySimpleExecute($query);

            return $dataArray;
        }

        /**
         * récupère la liste des informations pour 1 enseignant
         *
         * @param int $id
         * @return array avec id, prénom, nom, genre, surnom, origine du surnom, id de section, nom de section
         */
        public function getOneTeachers($id)
        {
            $query = "SELECT idTeacher,teaVoix, teaFirstName, teaName, teaGender, teaNickname, teaOrigine, idSection, secName, teaIsDeleted  FROM t_teacher INNER JOIN t_section on t_teacher.fkSection=t_section.idSection WHERE idTeacher=:id";
            $binds = array (
                0 => array (
                    'var' => $id,
                    'marker' => ':id',
                    'type'  => PDO::PARAM_INT
                )
            );

            $dataArray = $this->queryPrepareExecute($query, $binds);

            if (isset($dataArray[0]))
            {
                return $dataArray;
            }

            

            return "error";
        }

        /**
         * Cette fonction va nous retourner toutes les sections avec les informations
         *
         * @return array avec id et nom de section
         */
        public function getAllSections()
        {
            $query = "SELECT idSection, secName FROM t_section";
            $dataArray = $this->querySimpleExecute($query);
            return $dataArray;
        }



        /**
         * Fonction qui s'occupe de light delete un teacher, il faut donner son id et boum ça part
         *
         * @param int $id
         * @return void
         */
        public function deleteLightTeacher($id)
        {
            $query ="UPDATE t_teacher SET teaIsDeleted=1 WHERE idTeacher = :id";
            $binds = array (
                0 => array (
                    'var' => $id,
                    'marker' => ':id',
                    'type'  => PDO::PARAM_INT
                )
            );
            $this->queryPrepareExecute($query,$binds);
        }

        /**
         * Fonction qui s'occupe de restore un teacher, il faut donner son id et boum ça part
         *
         * @param int $id
         * @return void
         */
        public function restoreTeacher($id)
        {
            $query ="UPDATE t_teacher SET teaIsDeleted=0 WHERE idTeacher = :id";
            $binds = array (
                0 => array (
                    'var' => $id,
                    'marker' => ':id',
                    'type'  => PDO::PARAM_INT
                )
            );
            $this->queryPrepareExecute($query,$binds);
        }

        /**
         * Fonction qui s'occupe de restore tous les teachers
         *
         * @return void
         */
        public function restoreAllTeachers()
        {
            $query ="UPDATE t_teacher SET teaIsDeleted=0 WHERE teaIsDeleted = 1";

            $this->querySimpleExecute($query);
        }

        /**
         * Fonction qui s'occupe de delete un teacher, il faut donner son id et boum ça part
         *
         * @param int $id
         * @return void
         */
        public function deleteTeacher($id)
        {
            $query ="DELETE FROM t_teacher WHERE idTeacher = :id";
            $binds = array (
                0 => array (
                    'var' => $id,
                    'marker' => ':id',
                    'type'  => PDO::PARAM_INT
                )
            );
            $this->queryPrepareExecute($query,$binds);
        }

        /**
         * Fonction qui s'occupe de add un teacher
         *
         * @param array $array avec ces infos : teaFirstName, teaName, teaGender, teaNickname, teaOrigine, fkSection
         * @return void
         */
        public function addTeacher($array)
        {
            $query = "INSERT INTO t_teacher SET teaFirstName=:teaFirstName, teaName=:teaName, teaGender=:teaGender, teaNickname=:teaNickname, teaOrigine=:teaOrigine, fkSection=:fkSection";
            
            $binds = array (
                0 => array (
                    'var' => $array["teaFirstName"],
                    'marker' => ':teaFirstName',
                    'type'  => PDO::PARAM_STR
                ),
                1 => array (
                    'var' => $array["teaName"],
                    'marker' => ':teaName',
                    'type'  => PDO::PARAM_STR
                ),
                2 => array (
                    'var' => $array["teaGender"],
                    'marker' => ':teaGender',
                    'type'  => PDO::PARAM_STR
                ),
                3 => array (
                    'var' => $array["teaNickname"],
                    'marker' => ':teaNickname',
                    'type'  => PDO::PARAM_STR
                ),
                4 => array (
                    'var' => $array["teaOrigine"],
                    'marker' => ':teaOrigine',
                    'type'  => PDO::PARAM_STR
                ),
                5 => array (
                    'var' => $array["fkSection"],
                    'marker' => ':fkSection',
                    'type'  => PDO::PARAM_INT
                )
            );
            $this->queryPrepareExecute($query, $binds);

        }

        /**
         * Cette fonction permet d'update un teacher
         *
         * @param array $array avec ces infos : teaFirstName, teaName, teaGender, teaNickname, teaOrigine, fkSection, idTeacher
         * @return void
         */
        public function updateTeacher($array)
        {
            $query = "UPDATE t_teacher 
            SET teaFirstName=:teaFirstName, teaName=:teaName, teaGender=:teaGender, teaNickname=:teaNickname, teaOrigine=:teaOrigine, fkSection=:fkSection
            WHERE idTeacher=:idTeacher";
            
            $binds = array (
                0 => array (
                    'var' => $array["teaFirstName"],
                    'marker' => ':teaFirstName',
                    'type'  => PDO::PARAM_STR
                ),
                1 => array (
                    'var' => $array["teaName"],
                    'marker' => ':teaName',
                    'type'  => PDO::PARAM_STR
                ),
                2 => array (
                    'var' => $array["teaGender"],
                    'marker' => ':teaGender',
                    'type'  => PDO::PARAM_STR
                ),
                3 => array (
                    'var' => $array["teaNickname"],
                    'marker' => ':teaNickname',
                    'type'  => PDO::PARAM_STR
                ),
                4 => array (
                    'var' => $array["teaOrigine"],
                    'marker' => ':teaOrigine',
                    'type'  => PDO::PARAM_STR
                ),
                5 => array (
                    'var' => $array["fkSection"],
                    'marker' => ':fkSection',
                    'type'  => PDO::PARAM_INT
                ),
                6 => array (
                    'var' => $array["idTeacher"],
                    'marker' => ':idTeacher',
                    'type' => PDO::PARAM_INT
                )
            );
            $this->queryPrepareExecute($query, $binds);
        }

        /**
         * Cette fonction vérifie si l'utilisateur existe ou pas
         *
         * @param string $useLogin
         * @return int : 1 s'il exite sinon 0
         */
        public function checkIfUserExists($useLogin)
        {
            $query = "SELECT useLogin FROM t_user WHERE useLogin=:useLogin";
            $binds = array (
                0 => array (
                    'var' => $useLogin,
                    'marker' => ':useLogin',
                    'type'  => PDO::PARAM_STR
                ),
            );
            $dataArray = $this->queryPrepareExecute($query, $binds);
            foreach($dataArray as $user)
            {
                if ($user["useLogin"] == $useLogin) {
                    return 1;
                }   
            }
            return 0;
        }

        /**
         * Cette fonction va comparer le mot de passe pour vérifier s'il est valide ou pas
         *
         * @param string $useLogin
         * @param string $usePassword
         * @return int : 1 si c'est valide et 0 si c'est invalide
         */
        public function checkPassword($useLogin, $usePassword)
        {
            $query = "SELECT usePassword FROM t_user WHERE useLogin=:useLogin";
            $binds = array (
                0 => array (
                    'var' => $useLogin,
                    'marker' => ':useLogin',
                    'type'  => PDO::PARAM_STR
                ),
            );
            $hashed_psw = $this->queryPrepareExecute($query, $binds);

            if (password_verify($usePassword,$hashed_psw[0]["usePassword"])) {
                return 1;
            }
            return 0;
        }

        /**
         * Cette fonction va regarder quels droits a le user
         *
         * @param string $useLogin
         * @return int : retourne 1 s'il est admin, sinon 0
         */
        public function getUserRights($useLogin){
            $query = "SELECT useAdministrator FROM t_user WHERE useLogin=:useLogin"; //haha regardez pas cette synstaxe
            $binds = array (
                0 => array (
                    'var' => $useLogin,
                    'marker' => ':useLogin',
                    'type'  => PDO::PARAM_STR
                )
            );
            
            $rights = $this->queryPrepareExecute($query, $binds);
            if (isset($rights[0]["useAdministrator"])) {
                if ($rights[0]["useAdministrator"]== 1) {
                    return 1;
                }
            }
            return 0;
        }

        public function addVoix($id)
        {
            $query="SELECT teaVoix FROM t_teacher WHERE idTeacher=:id";
            $binds = array(
                0 => array (
                    'var' => $id,
                    'marker' => ':id',
                    'type'  => PDO::PARAM_STR
                )
            );
            $dataArray = $this->queryPrepareExecute($query,$binds);
            if ($dataArray==null) {
                return null;
            }
            $voix = $dataArray[0]["teaVoix"] +1;
            $query="UPDATE t_teacher SET teaVoix=:voix WHERE idTeacher=:id";
            $binds = array(
                0 => array (
                    'var' => $voix,
                    'marker' => ':voix',
                    'type'  => PDO::PARAM_STR
                ),
                1 => array (
                    'var' => $id,
                    'marker' => ':id',
                    'type'  => PDO::PARAM_STR
                )
            );
            $this->queryPrepareExecute($query,$binds);
        }

        public function addVoixMultiple($arrayData)
        {
            foreach($arrayData as $teacher)
            {
                $this->addVoix($teacher);
            }
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