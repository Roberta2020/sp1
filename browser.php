
<?php
    session_start();
    if (!$_SESSION['logged_in']) {
        header('Location: login.php');
    }

    // LOGOUT LOGIC
    if(isset($_GET['action']) and $_GET['action'] == 'logout'){
        session_destroy();
        unset($_SESSION['username']);
        unset($_SESSION['password']);
        unset($_SESSION['logged_in']);
        header('Location: index.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./browser.css">
    <title>Document</title>
</head>
<body>
<?php
 $current = $_SERVER['REQUEST_URI'];

    if(isset($_FILES['image'])){
        $errors= array();
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        
        $file_ext = strtolower(end(explode('.',$_FILES['image']['name'])));
        $extensions = array("jpeg","jpg","png");
        if(in_array($file_ext,$extensions)=== false){
            $errors[]="extension not allowed, please choose a JPEG or PNG file.";
        }
        if($file_size > 2097152) {
            $errors[]='File size must be smaller than 2 MB';
        }
        if(empty($errors)==true) {
            move_uploaded_file($file_tmp, $file_name);
            echo "Success";
        }else{
            print_r($errors);
        }
    }

// <!------------------- Download files -->

      // FILE DOWNLOAD LOGIC
      if(isset($_POST['download'])){
        $file='./' . $_POST['download'];
        $fileToDownloadEscaped = str_replace("&nbsp;", " ", htmlentities($file, null, 'utf-8'));
        ob_clean();
        ob_start();
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf'); 
        header('Content-Disposition: attachment; filename=' . basename($fileToDownloadEscaped));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fileToDownloadEscaped)); 
        ob_end_flush();
        readfile($fileToDownloadEscaped);
        exit;
    }
// NAVIGATE 
    if (isset($_GET["dir"]) && !empty($_GET['dir'])) {
      $_SESSION['path'] = $_GET['dir'];
    } else {
      $_SESSION['path'] = '';
      $root = $_SERVER['REQUEST_URI'];
      $root = preg_replace('/\?.*/', '', $root);
      $_SESSION['root_dir'] = $root;
    }

// DELETE FILE 
if(isset($_POST['delete'])) {
    $filePath = $_GET['path'] . '/' . $_POST['delete'];
    if ($filePath == 'index.php' || 
        $filePath == 'browser.php' ||
        $filePath == 'browser.css' ||
        $filePath == 'login.css' ||
        $filePath == "README.md") {
        echo '<p style="color: red;">You cannot delete this file! Please delete something that is not important</p>';
    } else {
        unlink($filePath);
        header('Location: ' . $_SERVER['REQUEST_URI']);
    }
}
// NEW DIR FUNCTION
    function createDir() {
        $add = $_POST["add"];
        @mkdir(" ".$add); 
    }

if (isset($_POST['submit'])){
    createDir();
}
?>
    <!----------------- Browser table -->
<?php
$path = './' . $_GET["path"];
$files_and_dirs = scandir($path);
print('<h2>Current directory: ' . substr($_SESSION['root_dir'], 0, -1) . $_SESSION['path'] . '</h2>');
print("<table><th>Type</th><th>Name</th><th>Actions</th>");
foreach ($files_and_dirs as $filesNdirs)
{
    if ($filesNdirs != ".." and $filesNdirs != ".") 
    {
        $fullPath = "$path/$filesNdirs";
        print('<tr>');
        if (is_dir($fullPath))
        {
            print("<td>" . "Directory" . "</td>");
            print("<td> <a href= '?path=" . $fullPath . "'>" . $filesNdirs . "</a></td>");
            print("<td></td>");
        } else {
            print("<td>" . "Files" . "</td>");
            print("<td>" . $filesNdirs . "</td>");
            print('<td>
                <form style="display: inline-block" action="" method="POST">
                    <input type="hidden" name="delete" value="' . $filesNdirs . '">
                    <input type="submit" value="Delete">
                </form>
                <form style="display: inline-block" action="?path=' . $fullPath . '" method="POST">
                    <input type="hidden" name="download" value="' . $fullPath . '">
                    <input type="submit" value="Download">
                </form>
            </td>');
                }
        print ("</tr>");
    }
}
    print("</table>");
?>   
  <!----------------Back button-  -->
<?php    
    // BACK PATH FUNCTION
    function back_dir($currPath) 
    {
        return dirname($currPath);
    }
?>

<button class="back">
        <a href="<?php echo('?path='. back_dir($path)) ?>">Back</a>
</button>
<!--------------- Upload files -->

<form class="upload" action="" method="POST" enctype="multipart/form-data">
    <input type="file" name="image" />
    <input type="submit"/>
</form>

<!-- ------------------New directory -->
<form class="new-dir" action = "" method = "post">
    <div>
        <label>Create a new directory</label>
        <input type="text" name = "add" placeholder="Enter directory name"/>
    <button type="submit" name = "submit" >Submit</button>
    <input type="hidden" name="path" value="<?php print($_GET['path']) ?>" /> 
    </div>
</form>
    
<div class="logout">Click here to <a href = "index.php?action=logout"> logout.</div> 
</body>
</html>