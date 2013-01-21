<?php 

include 'config.php'; 

function Remove()
{
	try
	{
		if (isset($_GET['id']))
		{
			MySQL::Create('DELETE FROM example
							where id = ?id')
				 ->Parameter('id', $_GET['id'], 'integer')
				 ->NonQuery();
				 
			if (MySQL::AffectedRows() > 0) {
				echo "Row removed";
				} else {
				echo "Row not removed";
			}
		}
	}
	catch(MySQLException $ex) {
		echo $ex->getMessage();
	}
}

function Display()
{
	try
	{
		$rows = MySQL::Create('SELECT id, title, value
								FROM example')
						->Query();
	}
	catch(MySQLException $ex) {
		echo $ex->getMessage();
	}

	if (isset($rows))
	{
		?>
		<table>
			<tr>
				<th>
					id
				</th>
				<th>
					title
				</th>
				<th>
					value
				</th>
				<th>
				</th>
			</tr>
		<?php
		foreach($rows as $row)
		{
			?>
			<tr>
				<td>
					<?php echo $row['id']; ?>
				</td>
				<td>
					<?php echo $row['title']; ?>
				</td>
				<td>
					<?php echo $row['value']; ?>
				</td>
				<td>
					<a href="?id=<?php echo $row['id']; ?>">Remove</a>
				</td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
}

?>

<html>
	<head>
		<title>Some title</title>
	</head>
	<body>
		<form method="POST">
		
			<?php Remove(); ?>
			<?php Display(); ?>
			
		</form>
	</body>
</html>