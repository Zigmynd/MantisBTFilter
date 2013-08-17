<html>
<head>
	<meta charset="windows-1251"/>
	<title>Статистика .22</title>
	<link rel="stylesheet" type="text/css" href="./css/tcal.css" />
	<link rel="stylesheet" type="text/css" href="./css/default.css" />
	<link rel="stylesheet" type="text/css" href="./css/calendar-blue.css" />
	<link rel="stylesheet" type="text/css" href="./css/status_color.css" />
	<script type="text/javascript" src="./js/tcal.js"></script> 
	<script type="text/javascript" src="./js/tsort.js"></script> 
</head>

<body>
<?php
	error_reporting(E_ERROR);
	
	function save($value)	{
		if (isset($_GET['old_value']))	{
			foreach($_GET['old_value'] as $temp)	{
				if ($value==$temp )
					echo 'selected="selected"';
			}
		} 
		else
			if ($value == 'any')
				echo 'selected="selected"';
	}
	
	function save_o($value)	{
		if (isset($_GET['new_value']))	{
			foreach($_GET['new_value'] as $temp)	{
				if ($value==$temp )
					echo 'selected="selected"';
			}
		}
		else
			if ($value == 'any')
				echo 'selected="selected"'; 
	}
	
	$db = mysql_connect("localhost","root","");
	mysql_query('SET NAMES cp1251');
	mysql_select_db("bugtracker", $db);
	
	$sql_user = mysql_query("Select id, username from mantis_user_table order by username ASC", $db);
?>
	
	<form action="" method="get">
	<select name="user">	

<?php
	while($t = mysql_fetch_row($sql_user))	{
		if($t[0] == $_GET["user"])
			echo "<option value=\"".$t[0]."\" selected>".$t[1]."</option> <br />";
		else
			echo "<option value=\"".$t[0]."\">".$t[1]."</option> <br />";
	}
	echo "</select>";
?>

	<select name="project">	

<?php
	$sql_project = mysql_query("Select id, name from mantis_project_table order by name ASC", $db);
	
	while($t = mysql_fetch_row($sql_project))	{
		if($t[0] == $_GET["project"])
			echo "<option value=\"".$t[0]."\" selected>".$t[1]."</option> <br />";
		else
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
		<option value="any" <?php save_o('any'); ?>>Любое</option>
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
	по <input type="text" name="date_to" class="tcal" value="<?php if(isset($_GET["date_to"]))echo $_GET["date_to"]; else echo date("Y-m-d");?>" />

<?php	
	echo "<input type=\"submit\" value=\"Фильтровать\">";	
	echo "</form>";

	foreach ( $_GET['old_value'] as $t)	{
		if ($t == 'any')
			$_GET['old_value'] = array (10,15,30,40,50,80,85,90);
	}
	
	foreach ( $_GET['new_value'] as $t)	{
		if ($t == 'any')
			$_GET['new_value'] = array (10,15,30,40,50,80,85,90);
	}

	$sql_filter = mysql_query("SELECT bug_id, user_id, date_modified, b.status FROM `mantis_bug_history_table` as a join mantis_bug_table as b on a.bug_id = b.id WHERE b.project_id=".$_GET['project']." and old_value IN (".implode(',',$_GET["old_value"]).") and new_value IN (".implode(',',$_GET["new_value"]).") and user_id=".$_GET["user"]." and date_modified BETWEEN UNIX_TIMESTAMP('".$_GET["date_from"]."') and UNIX_TIMESTAMP(TIMESTAMPADD(DAY,1,'".$_GET["date_to"]."')) GROUP BY bug_id");

	if(!$sql_filter)
		echo "";
	else	{
		echo "<p>По вашему вопросу найдено: <b>". mysql_num_rows($sql_filter) ."</b></p>";
		echo "<table cellspacing='1' width='100%' class='width100'>";
		echo "<tr class='row-category'><td width='5%' class='thd' onclick='sort(this)'>Bug</td><td width='10%' class='thd' onclick='sort(this)'>Категория</td><td width='68%' class='thd' onclick='sort(this)'>Суть</td><td width='12%' class='thd' onclick='sort(this)'>Дата</td></tr>";

		while($t1 = mysql_fetch_row($sql_filter))	{
			$sql_info = mysql_query("Select summary, name From mantis_bug_table as a Join mantis_category_table as b on a.category_id=b.id WHERE a.id=".$t1[0]);
			while ($t2 = mysql_fetch_row($sql_info))	{
				echo "<tr id='status_".$t1[3]."'><td><a target='_blank' href=\"/view.php?id=".$t1[0]."\">".$t1[0]."</a></td><td>".$t2[1]."</td><td>".$t2[0]."</td><td>".date("Y-m-d H:i",$t1[2])."</td></tr>";
			}
		}	
		echo "</table>";
	}
	mysql_close($db);
?>

</body>