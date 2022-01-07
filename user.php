<?php
    session_start();
    //var_dump($_SESSION["user"]);
    

    class User {
        private $id;
        public $login;
        public $email;
        public $firstname;
        public $lastname;
        public $password;
        public $bdd;
        public $user;
         
        //var_dump($user);
        function __construct() {
            $bdd = mysqli_connect("localhost", "root", "", "classes");           
            $this->bdd = $bdd;
            if (!$bdd) {
                echo "echec connexion mysql:" . mysqli_connect_errno(); 
            }
        }

        public function register($login, $password, $email, $firstname, $lastname) {
           $requete = mysqli_query($this->bdd, "INSERT into utilisateurs (login, password, email, firstname, lastname) VALUES ('$login', '$password', '$email', '$firstname', '$lastname')"); 
           if ($requete == true)
           {
               echo "ok";
           }
           else {
               echo "pas ok";
           }
            return $requete;
        }

        public function connect($login, $password) {
            $requete = mysqli_query($this->bdd, "SELECT * from utilisateurs where login = '$login'");
            $login = mysqli_fetch_all($requete, MYSQLI_ASSOC);
            //var_dump($login);
            if (count($login) > 0) {
                $_SESSION["user"] = [
                    $login[0]["id"], $login[0]["login"], $login[0]["password"], $login[0]["email"], $login[0]["firstname"], $login[0]["lastname"]
                ];
                header("Refresh: 0");
            }   
        }

        public function disconnect() {
            session_destroy();
            header("Refresh: 0");            
        }

        public function delete() {          
           $user = $_SESSION["user"][0];
           var_dump($user);
           $this->user = $user;
           $requete = mysqli_query($this->bdd, "DELETE From utilisateurs where id = '$this->user'");
           var_dump($requete);
           session_destroy();
        }

        public function update($login, $password, $email, $firstname, $lastname) {
            $user = $_SESSION["user"][0];
            echo $user;
            $this->user = $user;
            $requeteU = mysqli_query($this->bdd, "UPDATE utilisateurs set login = '$login', password = '$password', email = '$email', firstname = '$firstname', lastname = '$lastname' where id = '$this->user'");
            var_dump($requeteU);
            $_SESSION["user"] = [$user, $login, $email, $firstname, $lastname];
            header("Refresh: 0");
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
            $user = $_SESSION["user"][4];
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
        $user = new user();
        print($user->getLogin());
        print($user->getEmail());
        print($user->getFirstname());
        print($user->getLastname());
        $user->register($login,$password, $email, $firstname, $lastname);
    }
    else if (isset($_POST["login"]) && isset($_POST["password"])) {
        $login = $_POST["login"];
        $password = $_POST["password"];
        $user = new user();
        $user->connect($login, $password);
    }
    else if (isset($_POST["déco"])) {
        $user = new user();
        $user->disconnect();
    }
    else if (isset($_POST["destroy"])) {
        $user = new user();
        $user->delete();
    }
    else if (!empty($_POST["loginU"]) && !empty($_POST["passwordU"]) && !empty($_POST["emailU"]) && !empty($_POST["firstnameU"]) && !empty($_POST["lastnameU"])) {
        $login = $_POST["loginU"];
        $password = $_POST["passwordU"];
        $email = $_POST["emailU"];
        $firstname = $_POST["firstnameU"];
        $lastname = $_POST["lastnameU"];
        $user = new user();
        $user->update($login, $password, $email, $firstname, $lastname);
    } 

    $user = new user();
    print($user->getLogin());
    print($user->getEmail());
    print($user->getFirstname());
    print($user->getLastname());
    print($user->getAllInfos());
    print($user->isConnected());
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>

    </header>
    <main>
        <h1>INSCRIPTION</h1>
        <form action="" method="post">
            <input type="text" name="login" placeholder="login">
            <input type="text" name="password" placeholder="password">
            <input type="text" name="email" placeholder="email">
            <input type="text" name="firstname" placeholder="firstname">
            <input type="text" name="lastname" placeholder="lastname">
            <input type="submit" value="submit">
        </form>
        <h1>Connexion</h1>
        <form action="" method="post">
            <input type="text" name="login" placeholder="login">
            <input type="text" name="password" placeholder="password">
            <input type="submit" name="submit" value="submit">
        </form>
        <h1>Déconnexion</h1>
        <form action="" method="post">
            <input type="submit" name="déco" value="Déco">
        </form>
        <h1>Supprimer</h1>
        <form action="" method="post">
            <input type="submit" name="destroy" value="supprimer">
        </form>
        <h1>Update</h1>
        <form action="" method="post">
            <input type="text" name="loginU" value=<?php echo $_SESSION["user"][1]; ?>>
            <input type="text" name="emailU" value=<?php echo $_SESSION["user"][2]; ?>>
            <input type="text" name="firstnameU" value=<?php echo $_SESSION["user"][3]; ?>>
            <input type="text" name="lastnameU" value=<?php echo $_SESSION["user"][4]; ?>>
            <input type="submit" name="updateU" value="update">
        </form>
    </main>
    <footer>

    </footer>
    
</body>
</html>