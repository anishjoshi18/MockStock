<!DOCTYPE html>
<html lang="en">

<?php
include "classes.inc.php";
include "conn.inc.php";

//change the share price of companies (from functions.index.php)    
if($session_db != "off")
    changePrices($conn, $time_limit_for_company, $price_limit_for_company);
    
//leave if admin not logged in
if(!isset($_SESSION['gos_admin']))
{
    header("Location:login.php");
}
/*    
//edit the news details    
if(isset($_GET['id']) && isset($_POST['name']) && isset($_POST['abbr']) && isset($_POST['description']) && isset($_POST['price']))
{

    $id = $_GET['id'];
    $name = $_POST['name'];
    $abbr = $_POST['abbr'];
    $description = nl2br($_POST['description']);
    $price = $_POST['price'];
    

    
    $query = "UPDATE companies SET name = '$name', abbr = '$abbr', description = '$description' WHERE id = $id";
    
    
    $company = new Company($id);
    if($company->set_price($conn, $price) && mysqli_query($conn, $query))
    {
        echo "<div id='note'>Details updated for $name. <a id='close' class='pull-right'>[Close]&nbsp;</a></div>";
    }
    else
        echo "Gandlo";
}
 */  
//change the password for admin    
if(isset($_POST['current_p']) && isset($_POST['new_p']) && isset($_POST['new_confirm_p']))
{
    $query = "SELECT password FROM admin";
    if($run = mysqli_query($conn, $query))
    {
        $array = mysqli_fetch_assoc($run);
        
        $real_p = $array['password'];
    }
    
    if(md5($_POST['current_p']) != $real_p)
    {
        echo "<script>alert('Wrong current password.')</script>";
        header("refresh:0,url=admin_password.php");       
    }
    $query_change_p = "UPDATE admin SET password = '".md5($_POST['new_p'])."'";
    if(mysqli_query($conn, $query_change_p))
    {
         echo "<div id='note'>Password Changed Successfuly. <a id='close' class='pull-right'>[Close]&nbsp;</a></div>";
    }
    
}
  
//adding the new news
if(isset($_POST['news_name']) && isset($_POST['news_description']))
{
	echo "<div id='note'> msc snmn<a id='close' class='pull-right'>[Close]&nbsp;</a></div>";
    $name = $_POST['news_name'];
    $description = nl2br($_POST['news_description']);
    
    //insert into 'news' table
    $query_resgister = "INSERT INTO news(heading, description) VALUES('$name', '$description')";

		if(mysqli_query($conn, $query_resgister))
		{
            $last_id = mysqli_insert_id($conn);
            
			echo "<div id='note'>New News Added: $name<a id='close' class='pull-right'>[Close]&nbsp;</a></div>";
		}
		else
			echo "Error Registering.";
}
?>
    
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=1024">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>VISION 2K18</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sidebar.css" rel="stylesheet">
    <link href="css/table.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    
        <style>
    #balance{
        color: #f6f8f6;
        font-size: 23px;
        background-color: #004D40;
        padding-bottom: 6px;
        padding-top: 6px;
        margin: 8px;
        }
            body{
                font-family: 'Montserrat', sans-serif;
            }
    
    </style>

</head>

<body>

    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li id="balance">
                    VISION 2K18
                </li>
                
                <li>
                    <a href="admin.php">Session</a>
                </li>
                <li>
                    <a href="companies_admin.php">Companies</a>
                </li>
				<li>
                    <a href="news_admin.php"  class="active">News</a>
                </li>
                <li>
                    <a href="users.php">Users</a>
                </li>
                <li>
                    <a href="admin_password.php">Change Password</a>
                </li>
                <li>
                    <a href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12"><br><br>
                        <a href="new_news.php" class="btn btn-primary btn-md">Add a news</a><br><br>
                        <table class="table-fill">
                            <thead>
                            <tr>
                            <th>No.</th>
                            <th>Heading</th>
                            <th>Descripition</th>
                            <th>Edit</th>
                            <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="table-hover">
                                
                                <?php
                                
                                $query = "SELECT * FROM news";
                                
                                if($run = mysqli_query($conn, $query))
                                {
                                    if(mysqli_num_rows($run) < 1)
                                    {
                                        echo "<tr><td colspan='6'>No News to show</td></tr>";
                                    }
                                    else
                                    {
                                        $no = 0;
                                        while($array = mysqli_fetch_assoc($run))
                                        {
                                            $no++;
                                            $news_id = $array['id'];
                                            $name = $array['heading'];
                                            $description = $array['description'];

                                            echo "<tr>
                                                    <td>".$no."</td>
                                                    <td>$name</td>
                                                    <td>".substr($description, 0,50);
                                            if(strlen($description)> 50)
                                                echo "...";
                                            echo "</td>
                                                    <td>
                                                        <form action='news_edit.php' method = 'post'><input type='text' value='$news_id' name='edit_id' hidden><input type='submit' class='btn btn-sm btn-primary' value='Edit'></form>
                                                    </td>
                                                ";
											echo " <td>
                                                        <form action='news_apply.php' method = 'post'><input type='text' value='$news_id' name='apply_id' hidden><input type='submit' class='btn btn-sm btn-primary' value='Apply'></form>
                                                    </td>
                                                </tr>";
                                        }
                                    }
                                }   
                                
                                ?>
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    
        <script>
 close = document.getElementById("close");
 close.addEventListener('click', function() {
   note = document.getElementById("note");
   note.style.display = 'none';
 }, false);
</script>

</body>

</html>


<?php




?>
