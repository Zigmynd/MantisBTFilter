<html>
<head>
	<meta charset="windows-1251"/>
	<title>Статистика .01</title>
	<link rel="stylesheet" type="text/css" href="tcal.css" />
	<script type="text/javascript" src="tcal.js"></script> 
	<script type="text/javascript" src="tsort.js"></script> 
	<style>
	body
	{
		
		color: #000000;
		font: 10pt verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif;
		margin: 5; padding: 5;
	}
	a:link, body_alink
	{
		color: #2E5B82;
	}
	a:visited, body_avisited
	{
		color: #284A67;
	}
	a:hover, a:active, body_ahover
	{
		color: #D11010;
	} 
	td, th, p, li
	{
		font: 10pt verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif;
	}
	.thead
	{	
		background: black;
		color: white;
		font: bold 11px tahoma, lucida, arial, helvetica, sans-serif;
		border:solid 1px #ffffff;

	}
	.tborder
	{
		background: #E1E1E1;
		color: #000000;
		border: 1px solid #BBBBBB;
	}	
	select
	{
		font: 11px verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif;
	}
	input
	{
		font: 11px verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif;
	}
	option, optgroup
	{
		font-size: 11px;
		font-family: verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif;
	}
	#status_10
	{
		background-color: #ef2929;
	}
	#status_15
	{
		background-color: #75507b;
	}
	#status_30
	{
		background-color: #f57900;
	}
	#status_40
	{
		background-color: #fce94f;
	}
	#status_50
	{
		background-color: #729fcf;
	}
	#status_80
	{
		background-color: #8ae234;
	}
	#status_85
	{
		background-color: #8f8c70;
	}
	#status_90
	{
		background-color: #babdb6;
	}
</style>
</head>

<body>

<?php
	error_reporting(E_ERROR);
	function save($value)
	{
		if (isset($_GET['old_value']))
		{
			foreach($_GET['old_value'] as $temp)
			{
				if ($value==$temp )
					echo 'selected="selected"';
			}
		} 
	}
	
	function save_o($value)
	{
		if (isset($_GET['new_value']))
		{
			foreach($_GET['new_value'] as $temp)
			{
				if ($value==$temp )
					echo 'selected="selected"';
			}
		} 
	}
	
	$db = mysql_connect("localhost","root","");
	mysql_query('SET NAMES cp1251');
	mysql_select_db("bugtracker", $db);
	
	$sql_user = mysql_query("Select id, username from mantis_user_table order by username ASC", $db);
?>
	<form action="" method="get">
	<select name="user">	

<?php
	while($t = mysql_fetch_row($sql_user))
	{
		if($t[0] == $_GET["user"])
			echo "<option value=\"".$t[0]."\" selected>".$t[1]."</option> <br />";			else
			echo "<option value=\"".$t[0]."\">".$t[1]."</option> <br />";
	}
	echo "</select>";

?>
	<select name="project">	
<?php
	
	$sql_project = mysql_query("Select id, name from mantis_project_table order by name ASC", $db);
	while($t = mysql_fetch_row($sql_project))
	{
		if($t[0] == $_GET["project"])
			echo "<option value=\"".$t[0]."\" selected>".$t[1]."</option> <br />";			else
			echo "<option value=\"".$t[0]."\">".$t[1]."</option> <br />";
	}
	echo "</select>";
?>

	<select multiple size="10" name="old_value[]">
		<option value="any" <?php save('any'); ?>>Любое</option>
		<option value="10" <?php save(10); ?>>Новый</option>
		<option value="15" <?php save(15); ?>>Требует уточнения</option>
		<option value="30" <?php save(30); ?>>Отложен</option>
		<option value="40" <?php save(40); ?>>На доработку</option>
		<option value="50" <?php save(50); ?>>Назначен</option>
		<option value="80" <?php save(80); ?>>Отработан</option>
		<option value="85" <?php save(85); ?>>Включен в релиз</option>
		<option value="90" <?php save(90); ?>>Закрыт</option>
	</select>

	<select multiple size="10" name="new_value[]">	
		<option value="10" <?php save_o(10); ?>>Новый</option>
		<option value="15" <?php save_o(15); ?>>Требует уточнения</option>
		<option value="30" <?php save_o(30); ?>>Отложен</option>
		<option value="40" <?php save_o(40); ?>>На доработку</option>
		<option value="50" <?php save_o(50); ?>>Назначен</option>
		<option value="80" <?php save_o(80); ?>>Отработан</option>
		<option value="85" <?php save_o(85); ?>>Включен в релиз</option>
		<option value="90" <?php save_o(90);?>>Закрыт</option>
	</select>

	 c <input type="text" name="date_from" class="tcal" value="<?php echo $_GET["date_from"]; ?>" />
	по <input type="text" name="date_to" class="tcal" value="<?php echo $_GET["date_to"]; ?>" />
<?php	
	
	
	
	echo "<input type=\"submit\" value=\"Фильтровать\">";	
	echo "</form>";


//echo implode(',',$_GET["old_value"]);
//echo implode(',',$_GET["new_value"]);




	
		foreach ( $_GET['old_value'] as $t)
		{
			if ($t == 'any')
				$_GET['old_value'] = array (10,15,30,40,50,80,85,90);				;
		}
	
	

	$sql_filter = mysql_query("SELECT bug_id, user_id, date_modified, b.status FROM `mantis_bug_history_table` as a join mantis_bug_table as b on a.bug_id = b.id WHERE b.project_id=".$_GET['project']." and old_value IN (".implode(',',$_GET["old_value"]).") and new_value IN (".implode(',',$_GET["new_value"]).") and user_id=".$_GET["user"]." and date_modified>".strtotime($_GET["date_from"])." and date_modified<".strtotime($_GET["date_to"]));	


//статус приедт $t1[3]
	
	
	if(!$sql_filter)
	{
		echo "";
	}
	else
	{
		echo "По вашему вопросу найдено: ". mysql_num_rows($sql_filter) ."<br>";
		echo "<table width='100%' class='spc'>";
		echo "<tr class=\"thead\"><td class='thd' onclick='sort(this)'>Bug</td><td class='thd' onclick='sort(this)'>Категория</td><td class='thd' onclick='sort(this)'>Суть</td><td class='thd' onclick='sort(this)'>Дата</td></tr>";

		while($t1 = mysql_fetch_row($sql_filter))
		{
			$sql_info = mysql_query("Select summary, b.name name From mantis_bug_table as a Join mantis_category_table as b on a.project_id=b.id WHERE a.id=".$t1[0]);
			while ($t2 = mysql_fetch_row($sql_info))
			{
				echo "<tr id='status_".$t1[3]."'><td><a target='_blank' href=\"/view.php?id=".$t1[0]."\">".$t1[0]."</a></td><td>".$t2[1]."</td><td>".$t2[0]."</td><td>".date("Y-m-d H:i",$t1[2])."</td></tr>";
				}
			
			
		}	
		echo "</table>";
	}
	

	mysql_close($db);
?>

</body>