<?php
    session_start(); 
    $pdo = new PDO('mysql:host=localhost;dbname=neinGag', 'root', '');
?>

<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>neingag</title>
		<link href="./css/style.css" rel="stylesheet" type="text/css"/>
	</head>
	<body>
	<header>	
		<!-- check if the user is logged in, then display either menu with login/register or logout/profile/upload -->
		<?php
		if(!isset($_SESSION['userid']))
			include('menu.php');
		else 
			include('menu2.php');
		?>
	</header>
	<?php
	include('menu3.php');
	include('footer.php');
	?>
	<article>
		<form method="post" enctype="multipart/form-data">
			<input type="file" name="datei"><br>
			<input type="submit" value="Hochladen">
		</form>
	</article>
	<article>
		<?php
			$newImage['userName'] = $_SESSION['userid'];
			$newImage['userImagenumber'] = 1;
			$newImage['boringCounter'] = 1; 
			$newImage['comments'] = "A";

			$upload_folder = 'users/'.$newImage['userName'].'/'; //Das Upload-Verzeichnis
			$extension = strtolower(pathinfo($_FILES['datei']['name'], PATHINFO_EXTENSION));
				
			//Check IMG Type
			$allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
			if(!in_array($extension, $allowed_extensions)) {
				die("Ungültige Dateiendung. Nur png, jpg, jpeg und gif-Dateien sind erlaubt");
			}
				
			//Check IMG size
			$max_size = 500*1024; //500 KB
			if($_FILES['datei']['size'] > $max_size) {
				die("Bitte keine Dateien größer 500kb hochladen");
			}
				
			//Check IMG
			if(function_exists('exif_imagetype')) { //Die exif_imagetype-Funktion erfordert die exif-Erweiterung auf dem Server
				$allowed_types = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
				$detected_type = exif_imagetype($_FILES['datei']['tmp_name']);
				if(!in_array($detected_type, $allowed_types)) {
					die("Nur der Upload von Bilddateien ist gestattet");
				}
			}
				
			//Individual IMG ID increment
			$sql = "SELECT * FROM images WHERE userName='$newImage[userName]' ORDER BY userImagenumber DESC";
			$lastImagenumber = $pdo->query($sql)->fetch(); 
			echo '<br />'.$lastImagenumber['userImagenumber']; 

			if(empty($lastImagenumber['userImagenumber'])){
				$newImage['userImagenumber'] = 1; 
			}else {
				$lastImagenumber['userImagenumber']++;
				$newImage['userImagenumber'] = $lastImagenumber['userImagenumber']; 
			}

			$newPath = $upload_folder.$newImage['userName'].'_'.$newImage['userImagenumber'].'.'.$extension; 

			//Move IMG to Userfolder
			move_uploaded_file($_FILES['datei']['tmp_name'], $newPath);
			$statement = $pdo->prepare("INSERT INTO images (userName, userImagenumber, boringCounter, comments) VALUES (:userName, :userImagenumber, :boringCounter, :comments)");
			$result = $statement->execute($newImage); 
			echo 'Bild erfolgreich hochgeladen: <a href="users">'.$newPath.'</a>';
		?>
	</article>

	<article>
		<section>
			upload stuff here
		</section>
	</article>
	</body>
</html>