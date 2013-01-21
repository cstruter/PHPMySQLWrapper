<?php include 'config.php'; ?>
<html>
	<head>
		<title>Some title</title>
	</head>
	<body>
		<form method="POST">
			<table>
				<tr>
					<td colspan="2">
					
					<?php

						if (isset($_POST['action']))
						{
							try
							{
								MySQL::Create('INSERT INTO example(title, value) VALUES (?title, ?value)')
										->Parameter('title', $_POST['title'], 'string')
										->Parameter('value', $_POST['value'], 'integer')
										->NonQuery();
								
								echo "Example inserted";
							}
							catch(MySQLException $ex) {
								echo $ex->getMessage();
							}
						}
					?>
					
					</td>
				</tr>
				<tr>
					<td>Title</td>
					<td><input type="text" name="title" /></td>
				</tr>
				<tr>
					<td>Value</td>
					<td><input type="text" name="value" /></td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="action" value="Go" />
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>