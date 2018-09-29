<!DOCTYPE html>
<html lang="en">

<?php
include "classes.inc.php";
include "conn.inc.php";


    
//leave if admin not logged in
if(!isset($_SESSION['gos_admin']))
{
    header("Location:login.php");
}
    

    
if(!isset($_POST['edit_id']) || empty($_POST['edit_id']))
{
    header("Location:admin.php");
}

if(isset($_POST['radio'])&&isset($_POST['mysubmit']))
{
	$c_id = $_POST['affectedCompany'];
	$amount = $_POST['radio'];
	$n_id=$_POST['edit_id'];
	
	$query_resgister = "INSERT INTO comp_news(n_id, c_id, amount) VALUES('$n_id', '$c_id', '$amount')";
	
	if(mysqli_query($conn, $query_resgister))
	{
		$last_id = mysqli_insert_id($conn);
		echo "<div id='note'>New Affected Company Added: $c_id for news $n_id<a id='close' class='pull-right'>[Close]&nbsp;</a></div>";
	}
	else
		echo "<div id='note'>Error Registering Company $c_id!!! Ek news ek baar ek company ko ek hi tareeke se affect karegi<a id='close' class='pull-right'>[Close]&nbsp;</a></div>";
}


?>
    
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=1024">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Game Of Shares</title>

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
                    Admin
                </li>
                <li>
                    <a href="admin.php">Session</a>
                </li>
                <li>
                    <a href="companies_admin.php">Companies</a>
                </li>
                <li>
                    <a href="news_admin.php" class="active">News</a>
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
                    <div class="col-lg-12">
                        
                                
                                <?php
                                
                                $query = "SELECT * FROM news WHERE id = ".$_POST['edit_id'];
                                
                                if($run = mysqli_query($conn, $query))
                                {
                                    if(mysqli_num_rows($run) < 1)
                                    {
                                        echo "This News does noy exist.";
                                    }
                                    else
                                    {
                                        
                                        while($array = mysqli_fetch_assoc($run))
                                        {
                                           
                                            $news_id = $_POST['edit_id'];
                                            $heading = $array['heading'];
                                            $description = $array['description'];
                                            
                                          ?>
                                            <form action="admin.php?id=<?php echo $news_id; ?>" method="post">
                                                <div class="form-group">
                                                    <label for="name">Heading:</label>
                                                    <input type="text" class="form-control" name="heading" value="<?php echo $heading; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="name">Description:</label>
                                                    <textarea class="form-control" rows = "6" name="n_description"><?php echo $description; ?></textarea>
                                                </div>
                                                
                                                <input type="submit" class="btn btn-success"><br><br>
                                            </form>
											<form action="news_edit.php?id=<?php echo $news_id; ?>" method="post">
												<div class="form-group">
												<label for="acompany">Affected Company:</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<?php
													$query="select * from companies;";
													$filter = mysqli_query($conn, $query);
													$menu="<select name=\"affectedCompany\"> ";
													while($row = mysqli_fetch_assoc($filter)) {
														 $menu .= "<option value=".$row['id'].">" . $row['name']. "</option>";
													}
													// Close menu form
													$menu .= "</select>";
													echo $menu
												?>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                	<input type="radio" name="radio" value="nl" > -- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
													<input type="radio" name="radio" value="ns"> - &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
													<input type="radio" name="radio" value="ps"> + &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
													<input type="radio" name="radio" value="pl"> ++
												</div>

												<?php
												echo "</td>
                                                    <td>
                                                        <form action='news_edit.php' method = 'post'><input type='text' value='$news_id' name='edit_id' hidden><input type='text' value='$news_id' name='edit_id' hidden><input type='text' value='$news_id' name='edit_id' hidden><input type='submit' name='mysubmit' class='btn btn-sm btn-primary' value='Add affected company'></form>
                                                    <td>"
												?>
											</form>
                                    <?php        
                                        }
                                    }
                                }   
                                
                                ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
		
		<div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12"><br><br>
                        <table class="table-fill">
                            <thead>
                            <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Abbr</th>
                            <th>Effect</th>
                            <th>Edit</th>
                            </tr>
                            </thead>
                            <tbody class="table-hover">
                                
                                <?php
                                
                                $query = "SELECT id, name, abbr, amount FROM comp_news, companies where c_id=id and n_id=".$_POST['edit_id'];
                                
                                if($run = mysqli_query($conn, $query))
                                {
                                    if(mysqli_num_rows($run) < 1)
                                    {
                                        echo "<tr><td colspan='6'>No Companies to show</td></tr>";
                                    }
                                    else
                                    {
										$no = 0;
                                        while($array = mysqli_fetch_assoc($run))
                                        {
                                            $no++;
                                            $company_id = $array['id'];
                                            $name = $array['name'];
                                            $abbr = $array['abbr'];
                                            $amount = $array['amount'];

                                            echo "<tr>
                                                    <td>".$no."</td>
                                                    <td>$name</td>
                                                    <td>$abbr</td>
                                                    <td>$amount";
                                            echo "</td>
                                                    <td>
                                                        <form action='company_affected_edit.php' method = 'post'><input type='text' value='$company_id' name='edit_id' hidden><input type='submit' class='btn btn-sm btn-primary' value='Edit'></form>
                                                    <td>
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
        


    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>

</html>