    <?php
        session_start();
        
        require '../config/connexion.php';
        

        if(isset($_POST["addUser"])){
            register();
        }else if(isset($_POST["login"])){
            login();
        }

        function register(){
    
            global $connexion;
            
            $firstName       = $_POST['first_name'];
            $lastName        = $_POST['last_name'];
            $email           = $_POST['email'];
            $password        = password_hash($_POST["password"],PASSWORD_BCRYPT);
            $repeatPassword  = $_POST['repeat_password'];
            $imageName       = $_FILES['image']['name'];
            $imagetemp       = $_FILES['image']['tmp_name'];

            $query = "INSERT INTO users(first_name,last_name,email,password,image)  VALUES (?,?,?,?,?)";            

            $stmt = mysqli_prepare($connexion, $query);

            mysqli_stmt_bind_param($stmt,'sssss',$firstName,$lastName,$email,$password,$imageName,);
            $result = mysqli_stmt_execute($stmt);
            if($result){
                move_uploaded_file($imagetemp,'../pictures/'.$imageName);
                header('location:home.php');
            }else{
               echo 'something wrong';
            }
            mysqli_stmt_close($stmt);
            mysqli_close($connexion);
        }
        function getAllUsers()
        {
            $query = "SELECT * FROM users";
            $result = mysqli_query($GLOBALS["connexion"], $query);
            $rows = [];
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $rows[] = $row;
                }
                return $rows;
            }
            return null;
            mysqli_stmt_close($stmt);
            mysql_close($GLOBALS["connexion"]);
        }
        function login(){

            $email    = $_POST["email"];
            $password = $_POST["password"];
            $query = "SELECT * FROM users WHERE email = ? ";
            $stmt = mysqli_prepare($GLOBALS["connexion"],$query);
            mysqli_stmt_bind_param($stmt,'s',$email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    if(isset($_POST["remember_me"])){
                        setcookie("email", $_POST["email"], time() + 5*60);
                        setcookie("password", $_POST["passwod"], time() + 5*60);
                    }
                    header('location: home.php');
                } else {
                    echo "Invalid  password.";
                }
            } else {
                echo "Invalid email .";
            }
        }