<?php
    session_start();

    class Userpdo {

        private $servername;
        private $username;
        private $password;
        private $dbname;
        private $charset;
        public $connex;
        public $login;
        public $passwordUser;
        public $email;
        public $firstname;
        public $lastname;
        public $user;

        public function __construct() {
            $this->servername = "localhost";
            $this->username = "root";
            $this->password = "";
            $this->dbname = "classes";
            $this->charset = "utf8";
            
            
            try {
                $dsn = "mysql:host=".$this->servername.";dbname=".$this->dbname.";charset=".$this->charset;
                
                $connex = new PDO($dsn, $this->username, $this->password);
                $this->connex = $connex;
                $this->connex->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $this->connex;
                
            }
            catch(PDOException $e) {
                echo "connexion failed: " . $e->getmessage(); 
            }
        }

        public function register($login, $passwordUser, $email, $firstname, $lastname) {

            try {
                $sql = "INSERT into utilisateurs (login, password, email, firstname, lastname) values (:login, :passwordUser, :email, :firstname, :lastname)";                

                //on prepare la requete
                $requete = $this->connex->prepare($sql);

                $requete->bindValue(":login", $login, PDO::PARAM_STR);
                $requete->bindValue(":passwordUser", $passwordUser, PDO::PARAM_STR);
                $requete->bindValue(":email", $email, PDO::PARAM_STR);
                $requete->bindValue(":firstname", $firstname, PDO::PARAM_STR);
                $requete->bindValue(":lastname", $lastname, PDO::PARAM_STR);
                
                //execute la requete
                $requete->execute();
            } catch (PDOException $e) {
                echo $requete . "<br>" . $e->getMessage();
            }

        }

        public function connect($login, $password) {
            try {
                $sql = "SELECT * from utilisateurs where login = :login";
                $requete = $this->connex->prepare($sql);
                $requete->bindValue(":login", $login, PDO::PARAM_STR);
                $requete->execute();
                $info = $requete->fetchAll(PDO::FETCH_ASSOC );
                $count = $requete->rowcount();
                
                if ($count > 0) {
                    $_SESSION["user"] = [
                        $info[0]['id'],
                        $info[0]['login'],
                        $info[0]['password'],
                        $info[0]['email'],
                        $info[0]['firstname'],
                        $info[0]['lastname'],
                    ];
                    header("resfresh: 0");
                }
                

            } catch (PDOException $e) {
                echo $requete . "<br>" . $e->getMessage();
            }
        }

        public function disconnect() {
            session_destroy();
            header("Refresh: 0");
        }

        public function delete() {
            try {
                $user = $_SESSION["user"][0];
                $sql = "DELETE from utilisateurs where id = $user";
                $requete = $this->connex->prepare($sql);
                $requete->execute();
                session_destroy();
                header("refresh: 0");
            } catch (PDOException $e) {
                echo $requete . "<br>" . $e->getMessage();
            }
        }

        public function update($login, $passwordUser, $email, $firstname, $lastname) {
            try {
                $userU = $_SESSION["user"][0];
                echo $userU;
                $sql = "UPDATE utilisateurs set login = :login, password = :password, email = :email, firstname = :firstname, lastname = :lastname WHERE id = $userU";
                $requete = $this->connex->prepare($sql);
                $requete->bindValue(":login", $login, PDO::PARAM_STR);
                $requete->bindValue(":password", $passwordUser, PDO::PARAM_STR);
                $requete->bindValue(":email", $email, PDO::PARAM_STR);
                $requete->bindValue(":firstname", $firstname, PDO::PARAM_STR);
                $requete->bindValue(":lastname", $lastname, PDO::PARAM_STR);
                $requete->execute();
                $_SESSION["user"] = [$userU, $login, $passwordUser, $email, $firstname, $lastname];
                header("Refresh: 0");
            }
            catch (PDOException $e){
                echo $requete . "<br>" . $e->getMessage();
            }
        }

        public function isConnected() {
            if (isset($_SESSION["user"])) {
                return true;
            }
            else 
                return false;
        }

        public function getAllinfos() {?>
           
            <table>
                <thead>
                    <th>login</th>
                    <th>password</th>
                    <th>email</th>
                    <th>Firstname</th>
                    <th>lastname</th>
                </thead>
                <tbody>
                    <td>
                        <?php
                            echo $_SESSION["user"][1];
                        ?>
                    </td>
                    <td>
                        <?php 
                            echo $_SESSION["user"][2];
                        ?>
                    </td>
                    <td>
                        <?php
                            echo $_SESSION["user"][3];
                        ?>
                    </td>
                    <td>
                        <?php
                            echo $_SESSION["user"][4];
                        ?>
                    </td>
                    <td>
                        <?php
                            echo $_SESSION["user"][5];
                        ?>
                    </td>
                </tbody>
            </table><?php
        }

        public function getLogin() {
            $user = $_SESSION["user"][1];
            $this->user = $user;
            return $this->user;
        }

        public function getEmail() {
            $user = $_SESSION["user"][3];
            $this->user = $user;
            return $this->user;
        }

        public function getFirstname() {
            $user = $_SESSION["user"][4];
            $this->user = $user;
            return $this->user;
        }

        public function getLastname() {
            $user = $_SESSION["user"][5];
            $this->user = $user;
            return $this->user;
        }
    }

    if (isset($_POST["login"]) && isset($_POST["password"]) && isset($_POST["email"]) && isset($_POST["firstname"]) && isset($_POST["lastname"])) {
        $login = $_POST["login"];
        $password = $_POST["password"];
        $email = $_POST["email"];
        $firstname = $_POST["firstname"];
        $lastname = $_POST["lastname"];
        $user = new Userpdo();
        $user->register($login, $password, $email, $firstname, $lastname);
    }
    else if (isset($_POST["login"]) && isset($_POST["password"])) {
        $login = $_POST["login"];
        $password = $_POST["password"];
        $user = new Userpdo();
        $user->connect($login, $password);
    }
    else if (isset($_POST["deco"])) {
        $user = new Userpdo();
        $user->disconnect();
    }
    else if (isset($_POST["loginU"]) && isset($_POST["passwordU"]) && isset($_POST["emailU"]) && isset($_POST["firstnameU"]) && isset($_POST["lastnameU"])) {
        $login = $_POST["loginU"];
        $password = $_POST["passwordU"];
        $email = $_POST["emailU"];
        $firstname = $_POST["firstnameU"];
        $lastname = $_POST["lastnameU"];
        $user = new Userpdo();
        $user->update($login, $password, $email, $firstname, $lastname);
    }
    else if (isset($_POST["delete"])) {
        $user = new Userpdo();
        $user->delete();
    }
   
   
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>user-pdo</title>
</head>
<body>
    <header>

    </header>
    <main>
        <h1>INSCRIPTION</h1>
        <form action="user-pdo.php" method="post">
            <input type="text" name="login" placeholder="login">
            <input type="text" name="password" placeholder="password">
            <input type="text" name="email" placeholder="email">
            <input type="text" name="firstname" placeholder="firstname">
            <input type="text" name="lastname" placeholder="lastname">
            <input type="submit" name="submit" value="s'inscrire">
        </form>
        <h1>CONNEXION</h1>
        <form action="user-pdo.php" method="post">
            <input type="text" name="login" placeholder="login">
            <input type="text" name="password" placeholder="password">
            <input type="submit" name="submit" value="connexion">
        </form>
        <h1>DECONNEXION</h1>
        <form action="" method="post">
            <input type="submit" name="deco" value="deconexion">
        </form>
        <h1>Delete</h1>
        <form action="" method="post">
            <input type="submit" name="delete" value="supprimer">
        </form>
        <h1>Update</h1>
        <form action="" method="post">
            <input type="text" name="loginU" value="<?php echo $_SESSION["user"][1] ?>">
            <input type="text" name="passwordU" value="<?php echo $_SESSION["user"][2] ?>">
            <input type="text" name="emailU" value="<?php echo $_SESSION["user"][3] ?>">
            <input type="text" name="firstnameU" value="<?php echo $_SESSION["user"][4] ?>">
            <input type="text" name="lastnameU" value="<?php echo $_SESSION["user"][5] ?>">
            <input type="submit" name="submitU" value="modifier">
        </form>
        <h1>Connecté/Status</h1>
            <?php
                $user = new Userpdo();
                print($user->isConnected());
            ?>
        <h1>GetAllinfos</h1>
            <?php    
                $user = new Userpdo();
                print($user->getAllinfos());
            ?>                    
        <h1>Login</h1>
        <?php
             $user = new Userpdo();
             print($user->getLogin());
        ?>
        <h1>Email</h1>
        <?php
             $user = new Userpdo();
             print($user->getEmail());
        ?>
        <h1>Firstname</h1>
        <?php
             $user = new Userpdo();
             print($user->getFirstname());
        ?>
        <h1>Lastname</h1>
        <?php
             $user = new Userpdo();
             print($user->getLastname());
        ?>
    </main>
    <footer>

    </footer>
</body>
</html>