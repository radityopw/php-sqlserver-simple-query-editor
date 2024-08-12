<?php 

function do_logout()
{

	unset($_SESSION['db_host']);
	unset($_SESSION['db_user']);
	unset($_SESSION['db_pass']);
	unset($_SESSION['db_name']);

}

function show_error()
{
	print_r( sqlsrv_errors(), false );
	echo "<a href='#' onClick='history.back();'>Back</a>";
	echo "<br>";
	
}

function create_conn()
{
	$connection_info = array('Database'=>$_SESSION['db_name'],'UID'=>$_SESSION['db_user'],'PWD'=>$_SESSION['db_pass'], 'Encrypt' => false, 'ReturnDatesAsStrings' => true);
	$conn = sqlsrv_connect($_SESSION['db_host'],$connection_info);

	if($conn === false)
	{
		show_error();
		die();
	}

	return $conn;

}

session_start();

$sudah_login = false;


if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'logout'  )
{
	do_logout();
}

if(isset($_POST['db_host']))
{
	$_SESSION['db_host'] = $_POST['db_host'];
	$_SESSION['db_user'] = $_POST['db_user'];
	$_SESSION['db_pass'] = $_POST['db_pass'];
	$_SESSION['db_name'] = $_POST['db_name'];

	sqlsrv_close(create_conn());
		
}

if(isset($_SESSION['db_user']))
{
	$sudah_login = true;

}

if(!$sudah_login)
{
?>

<html>
	<head>
		<title>php - sqlserver - simple - query - editor</title>
	</head>
	<body>
		<form action="index.php" method="post">
			<table border="0">
				<tr>
					<td>Host</td>
					<td>:</td>
					<td><input type="text" name="db_host"></td>
				</tr>
				<tr>
					<td>DB Name</td>
					<td>:</td>
					<td><input type="text" name="db_name"></td>
				</tr>
				<tr>
					<td>Username</td>
					<td>:</td>
					<td><input type="text" name="db_user"></td>
				</tr>
				<tr>
					<td>Password</td>
					<td>:</td>
					<td><input type="text" name="db_pass"></td>
				</tr>
				<tr>
					<td colspan="3">
						<input type="submit" value="login" />
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>


<?php
}

if($sudah_login)
{
?>

<html>
	<head>
		<title>Query Editor</title>
	</head>
	<body>
		<a href="?act=logout">Logout</a>
		<br>
		<?php echo "*************"; ?>
		<p>query : </p>
		<form action="index.php" method="post">
			<textarea name="qry"><?php if(isset($_POST['qry'])): ?><?=$_POST['qry']?><?php endif;?></textarea>
			<input type="submit" value="run" />
		</form>
		<?php echo "*************"; ?>
		<?php 
		if(isset($_POST['qry']))
		{
			$conn = create_conn();		

			$res = sqlsrv_query($conn,$_POST['qry']);
			if(!$res)
			{
				show_error();
				die();
			}

			while( $row = sqlsrv_fetch_array( $res, SQLSRV_FETCH_ASSOC) )
			{
				foreach($row as $key=>$value)
				{
					
					echo $key.":"."<br>";
					echo $value."<br>";

				}

				echo "*-------------------------*"."<br>";
			}

			sqlsrv_close($conn);
		} 
		?>
	</body>
</html>
<?php 
}
