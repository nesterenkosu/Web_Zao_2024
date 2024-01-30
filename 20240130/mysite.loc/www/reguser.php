<?php
	$db_conn = mysqli_connect("localhost","root","");
	if(!$db_conn)
		die("Ошибка подключения к БД");
		
	mysqli_select_db($db_conn,"db_users");
	
	mysqli_query($db_conn,"SET NAMES utf8");
	
	//Была ли нажата кнопка "Зарегистрироваться"
	if(isset($_POST["btn_go"])){
		//Фильтрация пользовательского ввода
		//для предотвращения SQL-инъекций
		$user_name=mysqli_real_escape_string($db_conn,$_POST["user_name"]);
		$user_age=(int)$_POST["user_age"];
		$user_mail=mysqli_real_escape_string($db_conn,$_POST["user_mail"]);
		
		if(trim($_POST["user_id"])!="") {
			$id=(int)$_POST["user_id"];
			mysqli_query($db_conn,"
				UPDATE users
				SET Name='$user_name',
					Age='$user_age',
					Email='$user_mail'
				WHERE
					ID=$id
			");
		}	
		else
			mysqli_query($db_conn,"
				INSERT INTO users (Name,Age,Email)
				VALUES('$user_name','$user_age','$user_mail')
			");
		
		//Сброс POST-параметров путём перезагрузки
		//текущей страницы
		header("Location: $_SERVER[PHP_SELF]");
	}
	
	$form_fields=Array();
	//Если была нажата кнопка "Редактировать"
	if(isset($_GET["edit_id"])) {
		$id=(int)$_GET["edit_id"];
		
		$res=mysqli_query($db_conn,"SELECT * FROM users WHERE ID=$id");
		
		$form_fields=mysqli_fetch_array($res);
	}
	
	//Если была нажата кнопка "Удалить"
	if(isset($_GET["delete_id"])) {
		$id=(int)$_GET["delete_id"];
		mysqli_query($db_conn,"DELETE FROM users WHERE ID=$id");
		
		//Сброс GET-параметров путём перезагрузки
		//текущей страницы
		header("Location: $_SERVER[PHP_SELF]");
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<script src="js/jquery-3.7.1.min.js"></script>
		<script>
		$(function() {
			$("#btn_go").click(function(){
				let errmsg = "";
				let user_name = $("#user_name").val();
				let user_age = $("#user_age").val();
				let user_mail = $("#user_mail").val();
				
				$("input").css("border-color","");
				
				if(user_name.trim()=="") {
					errmsg+="Поле Имя не заполнено <br/>";
					$("#user_name").css("border-color","red");
				}
				
				if(user_age.trim()=="") {
					errmsg+="Поле Возраст не заполнено <br/>";
					$("#user_age").css("border-color","red");
				}else if(!/^[0-9]+$/.test(user_age)) {
					errmsg+="Поле Возраст содержит недопустимые символы <br/>";
					$("#user_age").css("border-color","red");
				}
				
				if(user_mail.trim()=="") {
					errmsg+="Поле Email не заполнено <br/>";
					$("#user_mail").css("border-color","red");
				}else if(!/^[A-Za-z\.\-\_]+@[A-Za-z\.\-\_]+\.[A-Za-z]+$/.test(user_mail)) {
				errmsg+="Поле Email имеет неверный формат <br/>";
					$("#user_mail").css("border-color","red");
				}
				
				
				//if(errmsg.trim()!="")
					$("#errmsg").html(errmsg);
			});
		});
		</script>
		<style>
			div#errmsg {
				color: red;
			}
		</style>
	</head>
	<body>
		<div id="errmsg"></div>
		<form action="" method="POST">
		ID:<br/>
		<input name="user_id" type="text" size="2" value="<?=$form_fields["ID"]?>"/><br/>
		Имя: <br/>
		<input id="user_name" name="user_name" type="text" size="50" value="<?=$form_fields["Name"]?>"/><br/>
		Возраст: <br/>
		<input id="user_age" name="user_age" type="text" size="3" value="<?=$form_fields["Age"]?>"/><br/>
		Email: <br/>
		<input id="user_mail" name="user_mail" type="text" size="10" value="<?=$form_fields["Email"]?>"/><br/>
		<input id="btn_go" name="btn_go" type="submit" value="Сохранить"/>
		</form>
		
		<table border="1">
			<tr>
				<th>ID</th>
				<th>Имя</th>
				<th>Возраст</th>
				<th>Email</th>
				<th></th>
			</tr>
			<?
			$res=mysqli_query($db_conn,"SELECT * FROM users");
			?>
			<?while($user=mysqli_fetch_array($res)):?>
			<tr>
				<td><?=$user["ID"]?></td>
				<td><?=$user["Name"]?></td>
				<td><?=$user["Age"]?></td>
				<td><?=$user["Email"]?></td>
				<td>
					<a href="?edit_id=<?=$user["ID"]?>">Редактировать</a>
					<a href="?delete_id=<?=$user["ID"]?>" onclick="return confirm('Действительно удалить этого пользователя?')">Удалить</a>
				</td>
			</tr>
			<?endwhile;?>
		</table>
	</body>
</html>
