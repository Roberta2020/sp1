<?php
    session_start();
    if(isset($_GET['action']) and $_GET['action'] == 'logout')
    {
        session_start();
        unset($_SESSION['username']);
        unset($_SESSION['password']);
        unset($_SESSION['logged_in']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./login.css">
    <title>Browser</title>
</head>
<body>
<?php
    if (isset($_POST['login']) 
        && !empty($_POST['username']) 
        && !empty($_POST['password'])) 
    {	
        if ($_POST['username'] == 'Roberta' 
            && $_POST['password'] == '1234')
        {
            $_SESSION['logged_in'] = true;
            $_SESSION['timeout'] = time();
            $_SESSION['username'] = 'Roberta';
                header("Location: browser.php");
        } else {
                    $msg = 'Wrong username or password';
                }
    }
?>

    <div style="background-color: cadetblue; width: 120px">
        <form class = "login-card" action="" method="post">
            <h3>Log in</h3>
             <h4><?php echo $msg; ?></h4>
            <input class = "username" type="text" name="username" placeholder="username = Roberta" required autofocus></br>
            <input class = "password" type="password" name="password" placeholder="password = 1234" required>
            <button class = "login-button" type="submit" name="login">Login</button>
        </form>
    </div>   
</body>
</html>