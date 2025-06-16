<?php
#*******************************************************************************************#


				/**
				 * Dashboard-Zugang der Blog-Applikation.
				 */

				
				#******************************************#
				#********** ENABLE STRICT TYPING **********#
				#******************************************#
				
				/** 
				 * Aktiviert strikte Typprüfung:
				 * Dadurch erzwingt PHP, dass übergebene Werte den deklarierten Typen entsprechen.
				 */
				declare(strict_types=1);
				

#*******************************************************************************************#


				#****************************************#
				#********** PAGE CONFIGURATION **********#
				#****************************************#
				
				/**
				 * Konfigurations- und Initialisierungsdateien einbinden.
				 *
				 * - config.inc.php: Allgemeine Konfiguration
				 * - db.inc.php: Datenbankfunktionen
				 * - dateTime.inc.php: Datumsfunktionen
				 */	
				require_once('./include/config.inc.php');
				require_once('./include/db.inc.php');
				require_once('./include/dateTime.inc.php');
				
				/**
				 * Daten-Klassen einbinden (für Kategorien, Benutzer, Blog-Einträge)
				 */
				#********** INCLUDE DATA CLASSES **********#
				require_once('./class/Category.class.php');
				require_once('./class/User.class.php');				
				require_once('./class/Blog.class.php');
				
				/**
				 * Controller-Klassen zur Validierung und Datenbereinigung
				 */				
				#********** INCLUDE CONTROLLER CLASSES **********#
				require_once('./class/ControllerClasses/Sanitizer.class.php');
				require_once('./class/ControllerClasses/SimpleValidator.class.php');
				
				
#***************************************************************************************#	

			
				#******************************************#
				#********** INITIALIZE VARIABLES **********#
				#******************************************#
				
				/**
				 * Initialisierung von Variablen zur späteren Verwendung.
				 */
				
				$category 					= NULL;
				$blog						= NULL;
				
				$dbError					= NULL;
				$dbSuccess					= NULL;				
				
				$errorCatLabel				= NULL;
				$errorHeadline 				= NULL;
				$errorImageUpload 			= NULL;
				$errorBlogImageAlignment 	= NULL;
				$errorContent 				= NULL;


#***************************************************************************************#


				#****************************************#
				#********** SECURE PAGE ACCESS **********#
				#****************************************#

				/**
				 * Startet oder setzt die Session fort.
				 * Verhindert unautorisierten Zugriff durch Prüfung der Session-ID und IP-Adresse.
				 *
				 * Wird keine gültige Session erkannt, erfolgt ein Logout und eine Weiterleitung.
				 */				

				session_name('wwwblogprojectde');
				
				#********** START|CONTINUE SESSION	**********#
				if( session_start() === false ) {
					// Fehlerfall
if(DEBUG) 		echo "<p class='debug auth err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n";				
									
				} else {
					// Erfolgsfall
if(DEBUG)		echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Session erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n";				
// if(DEBUG_V)	echo "<pre class='debug auth value'><b>Line " . __LINE__ . "</b>: \$_SESSION<br>". print_r($_SESSION, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";
				
					
					#*******************************************#
					#********** CHECK FOR VALID LOGIN **********#
					#*******************************************#					

					#********** A) NO VALID LOGIN **********#
					if( isset($_SESSION['ID']) === false OR $_SESSION['IPAddress'] !== $_SERVER['REMOTE_ADDR'] ) {
						// Fehlerfall (User ist nicht eingeloggt)
if(DEBUG)			echo "<p class='debug auth err'><b>Line " . __LINE__ . "</b>: Login konnte nicht validiert werden! <i>(" . basename(__FILE__) . ")</i></p>\n";				
							
							
						#********** DENY PAGE ACCESS **********#
						// 1. Session löschen
						session_destroy();
						
						// 2. User auf öffentliche Seite umleiten
						header('LOCATION: ./');
						
						// 3. Fallback, falls die Umleitung per HTTP-Header ausgehebelt werden sollte
						exit();
					
					
					#********** B) VALID LOGIN **********#
					} else {
						// Erfolgsfall (User ist eingeloggt)
if(DEBUG)			echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Login wurde erfolgreich validiert. <i>(" . basename(__FILE__) . ")</i></p>\n";				

						session_regenerate_id(true);

					#********** GENERATE USER OBJECT **********#
					// $userID = NULL, $userFirstName = NULL, $userLastName = NULL,
					// $userEmail = NULL, $userCity = NULL, $userPassword = NULL
					$user = new User(userID:$_SESSION['ID'], userFirstName:$_SESSION['userFirstName'], userLastName:$_SESSION['userLastName'], userCity:$_SESSION['userCity']);
						
					} // CHECK FOR VALID LOGIN END
						
				} // SECURE PAGE ACCESS END
				
				
#***************************************************************************************#

	
				#********************************************#
				#********** PROCESS URL PARAMETERS **********#
				#********************************************#
				
				/**
				 * Verarbeitet URL-Parameter, hier zum Ausführen eines Logouts.
				 */
				
				// Schritt 1 URL: Prüfen, ob Parameter übergeben wurde
				if( isset($_GET['action']) ) {
if(DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: URL-Parameter 'action' wurde übergeben... <i>(" . basename(__FILE__) . ")</i></p>";	
			
			
					// Schritt 2 URL: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$action = Sanitizer::sanitizeString($_GET['action']);
if(DEBUG_V)		echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$action = $action <i>(" . basename(__FILE__) . ")</i></p>";
		
		
					// Schritt 3 URL: ggf. Verzweigung
					
					
					#********** LOGOUT **********#
					if( $_GET['action'] === 'logout' ) {
if(DEBUG)			echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: 'Logout' wird durchgeführt... <i>(" . basename(__FILE__) . ")</i></p>";	
						
						session_destroy();
						header("Location: ./");
						exit();
					}
					
				} // PROCESS URL PARAMETERS END


#***************************************************************************************#			

	
				#*************************************************#
				#********** PROCESS FORM 'NEW CATEGORY' **********#
				#*************************************************#
				
				/**
				 * Verarbeitet das Formular zum Erstellen einer neuen Kategorie.
				 *
				 * - Werte auslesen und entschärfen
				 * - Validierung der Eingabefelder
				 * - Kategorie in der DB speichern, falls gültig
				 * - Prüfung auf doppelte Kategoriebezeichnung
				 */
				
				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formNewCategory']) === true ) {
if(DEBUG) 		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: Formular 'New Category' wurde abgeschickt... <i>(" . basename(__FILE__) . ")</i></p>";	
		
		
					// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";

				#********** GENERATE CATEGORY OBJECT **********#
				// $catID = NULL, $catLabel = NULL
				$category = new Category(catLabel:$_POST['catLabel']);
				
if(DEBUG_V)		echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$catLabel: category->getCatLabel() <i>(" . basename(__FILE__) . ")</i></p>";
				
				
					// Schritt 3 FORM: Werte validieren
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";

					$errorCatLabel = SimpleValidator::validateInputString($category->getCatLabel(), maxLength: 50);
					
					
					#********** FINAL FORM VALIDATION **********#
					if( $errorCatLabel !== NULL ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>";						
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>";						
						
						// Schritt 4 FORM: Daten weiterverarbeiten

						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#
						
						// Schritt 1 DB: DB-Verbindung herstellen
						$PDO = dbConnect('blog_oop');
						
						#********** CHECK IF CATEGORY NAME ALREADY EXISTS **********#
						$count = $category->checkIfCategoryExist($PDO);
						
						if( $count !== 0 ) {
							// Fehlerfall: Kategorie existiert bereits							
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Kategorie '" . $category->getCatLabel() . "' existiert bereits! <i>(" . basename(__FILE__) . ")</i></p>";
							$errorCatLabel = "Diese Kategorie existiert bereits.";
						} else {
							// Erfolgsfall: Kategorie existiert noch nicht → speichern
if(DEBUG)					echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Neue Kategorie wird gespeichert. <i>(" . basename(__FILE__) . ")</i></p>";

							#********** SAVE CATEGORY INTO DB **********#							
							$rowCount = $category->saveCategoryToDB($PDO);
							
							if( $rowCount !== 1 ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: FEHLER beim Speichern der neuen Kategorie! <i>(" . basename(__FILE__) . ")</i></p>";
								$dbError = 'Es ist ein Fehler aufgetreten! Bitte versuchen Sie es später noch einmal.';
						
							} else {
								// Erfolgsfall								
								$category->setCatID($PDO->lastInsertId());
								
if(DEBUG)					echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Kategorie <b>'{$category->getCatLabel()}'</b> wurde erfolgreich unter der ID{$category->getCatID()} in der DB gespeichert. <i>(" . basename(__FILE__) . ")</i></p>";								
								$dbSuccess = "Die neue Kategorie mit dem Namen <b>'{$category->getCatLabel()}'</b> wurde erfolgreich gespeichert.";
								// Felder zurücksetzen
								$category = NULL;
							} // SAVE CATEGORY INTO DB END
							 
						} // CHECK IF CATEGORY NAME ALREADY EXISTS END
						
						// DB-Verbindung schließen
						dbClose($PDO, $PDOStatement);
						
					} // FINAL FORM VALIDATION END

				} // PROCESS FORM 'NEW CATEGORY' END
				
				
#***************************************************************************************#
			
				
				#**********************************************#
				#********** FETCH CATEGORIES FROM DB **********#
				#**********************************************#
				
				/**
				 * Lädt alle Kategorien aus der Datenbank.
				 */
				
if(DEBUG)	echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: Lade Kategorien aus DB... <i>(" . basename(__FILE__) . ")</i></p>";
				
				// Schritt 1 DB: DB-Verbindung herstellen
				$PDO = dbConnect('blog_oop');
				
				$allCategoriesArray = Category::fetchAllCategoriesFromDB($PDO);
if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$allCategoriesArray<br>". print_r($allCategoriesArray, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";
				
				// DB-Verbindung schließen
				dbClose($PDO, $PDOStatement);
						
// if(DEBUG_V)	echo "<pre class='debug auth value'><b>Line " . __LINE__ . "</b>: \$allCategoriesArray<br>". print_r($allCategoriesArray, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";


#***************************************************************************************#


				#***************************************************#
				#********** PROCESS FORM 'NEW BLOG ENTRY' **********#
				#***************************************************#
				
				/**
				 * Verarbeitet das Formular zum Erstellen eines neuen Blogeintrags.
				 *
				 * - Werte auslesen, entschärfen und validieren
				 * - Optional: Bild hochladen und prüfen
				 * - Speichern des Eintrags in der Datenbank
				 */
				
				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formNewBlogEntry']) === true ) {			
if(DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: Formular 'New Blog Entry' wurde abgeschickt... <i>(" . basename(__FILE__) . ")</i></p>";	


					// Schritt 2 FORM: Daten auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";

					#********** GENERATE BLOG OBJECT **********#
					// CONSTRUCTOR SIGNATURE:
					// $category = new Category(),
					// $user = new User(),
					// $blogID = NULL, 
					// $blogHeadline = NULL, 
					// $blogImagePath = NULL,
					// $blogImageAlignment = NULL, 
					// $blogContent = NULL, 
					// $blogDate = NULL
					
					#********** GENERATE CATEGORY OBJECT **********#
					// $catID = NULL, $catLabel = NULL
					
					#********** GENERATE USER OBJECT **********#
					// $userID = NULL, $userFirstName = NULL, $userLastName = NULL,
					// $userEmail = NULL, $userCity = NULL, $userPassword = NULL

					$blog = new Blog(new Category($_POST['catID']),
									$user,
									blogHeadline:$_POST['blogHeadline'], blogContent:$_POST['blogContent'], blogImageAlignment:$_POST['blogImageAlignment']);


					// Schritt 3 FORM: ggf. Werte validieren
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$errorHeadline 				= SimpleValidator::validateInputString($blog->getBlogHeadline());
					$errorBlogImageAlignment 	= SimpleValidator::validateInputString($blog->getBlogImageAlignment());
					$errorContent 				= SimpleValidator::validateInputString($blog->getBlogContent(), minLength:5, maxLength:20000);


					#********** FINAL FORM VALIDATION PART I (FIELDS VALIDATION) **********#					
					if( $errorHeadline !== NULL OR $errorContent !== NULL OR $errorBlogImageAlignment !== NULL ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: FINAL FORM VALIDATION PART I: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>";
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: FINAL FORM VALIDATION PART I: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>";

						#*******************************************#
						#********** OPTIONAL: FILE UPLOAD **********#
						#*******************************************#
						
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Prüfe auf Bildupload... <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						// Prüfen, ob eine Datei hochgeladen wurde
						if( $_FILES['blogImage']['tmp_name'] === '' ) {
if(DEBUG)				echo "<p class='debug hint'>Line <b>" . __LINE__ . "</b>: Bildupload ist nicht aktiv. <i>(" . basename(__FILE__) . ")</i></p>";
						
						} else {
if(DEBUG)				echo "<p class='debug hint'>Line <b>" . __LINE__ . "</b>: Bild Upload ist aktiv... <i>(" . basename(__FILE__) . ")</i></p>";

							// imageUpload() liefert ein Array zurück, das eine Fehlermeldung (String oder NULL) enthält
							// sowie den Pfad zum gespeicherten Bild (String oder NULL)
							$validateImageUploadResultArray = SimpleValidator::validateImageUpload($_FILES['blogImage']['tmp_name']);
					
							
							#********** VALIDATE IMAGE UPLOAD RESULTS **********#
							if( $validateImageUploadResultArray['imageError'] !== NULL ) {
								// Fehlerfall
								$errorImageUpload = $validateImageUploadResultArray['imageError'];
								
							} elseif( $validateImageUploadResultArray['imagePath'] !== NULL ) {
								// Erfolgsfall
if(DEBUG) 					echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Bild wurde erfolgreich unter <i>'" . $validateImageUploadResultArray['imagePath'] . "'</i> gespeichert. <i>(" . basename(__FILE__) . ")</i></p>";
								// Pfad zum Bild speichern
								$blog->setBlogImagePath($validateImageUploadResultArray['imagePath']);
							}
							#****************************************************#
							
						} // OPTIONAL: FILE UPLOAD END
						#*************************************************************************#
						
						
						#********** FINAL FORM VALIDATION PART II (IMAGE UPLOAD) **********#					
						if( $errorImageUpload !== NULL ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: FINAL FORM VALIDATION PART II: Bilduploadfehler: $validateImageUploadResultArray[imageError] <i>(" . basename(__FILE__) . ")</i></p>";
							
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: FINAL FORM VALIDATION PART II: Kein Bilduploadfehler. <i>(" . basename(__FILE__) . ")</i></p>";

							#********** SAVE BLOG ENTRY DATA INTO DB **********#
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Speichere Blogeintrag in DB... <i>(" . basename(__FILE__) . ")</i></p>\n";
							
							// Schritt 1 DB: DB-Verbindung herstellen
							$PDO = dbConnect();
							
							// Schritt 4 DB: Schreiberfolg prüfen
							$rowCount = $blog->saveBlogToDB($PDO);							
if(DEBUG_V)				echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$rowCount: $rowCount <i>(" . basename(__FILE__) . ")</i></p>";						
							
							if( $rowCount !== 1 ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: FEHLER beim Speichern des Blogbeitrags! <i>(" . basename(__FILE__) . ")</i></p>";
								$dbError = 'Es ist ein Fehler aufgetreten! Bitte versuchen Sie es später noch einmal.';
														
							} else {
								// Erfolgsfall
								$blog->setBlogID($PDO->lastInsertId());
							
if(DEBUG)					echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Blogbeitrag erfolgreich mit der ID{$blog->getBlogID()} in der DB gespeichert. <i>(" . basename(__FILE__) . ")</i></p>";
								$dbSuccess = 'Der Blogbeitrag wurde erfolgreich gespeichert.';
								
								// Felder aus Formular wieder leeren								
								$blog = NULL;
								
							} // SAVE BLOG ENTRY INTO DB END
							
							// DB-Verbindung schließen
							dbClose($PDO, $PDOStatement);
							
						} // FINAL FORM VALIDATION PART II (IMAGE UPLOAD) END
							
					} // FINAL FORM VALIDATION PART I (FIELDS VALIDATION) END
					
				} // PROCESS FORM 'NEW BLOG ENTRY' END


#***************************************************************************************#			
?>

<!doctype html>

<html>

	<head>
		<meta charset="utf-8">
		<title>PHP-Projekt Blog</title>
		<link rel="stylesheet" href="./css/main.css">
		<link rel="stylesheet" href="./css/debug.css">
	</head>
	
	<body class="dashboard">

		<!-- ---------- PAGE HEADER START ---------- -->	
		<header class="fright">
			<a href="?action=logout">Logout</a><br>
			<a href="./"><< zum Frontend</a>
		</header>
		<div class="clearer"></div>

		<br>
		<hr>
		<br>		
		<!-- ---------- PAGE HEADER END ---------- -->
		
		<h1 class="dashboard">PHP-Projekt Blog - Dashboard</h1>
		<p class="name">Aktiver Benutzer: <?= $user->getUserFirstName() ?> <?= $user->getUserLastName() ?></p>
		
		
		<!-- ---------- POPUP MESSAGE START ---------- -->
		<?php if( $dbError OR $dbSuccess ): ?>
		<popupBox>
			<?php if($dbError): ?>
			<h3 class="error"><?= $dbError ?></h3>
			<?php elseif($dbSuccess): ?>
			<h3 class="success"><?= $dbSuccess ?></h3>
			<?php endif ?>
			<a class="button" onclick="document.getElementsByTagName('popupBox')[0].style.display = 'none'">Schließen</a>
		</popupBox>		
		<?php endif ?>
		<!-- ---------- POPUP MESSAGE END ---------- -->
		
		
		<!-- ---------- LEFT PAGE COLUMN START ---------- -->
		<main class="forms fleft">	
						
			<h2 class="dashboard">Neuen Blog-Eintrag verfassen</h2>
			<p class="small">
				Um einen Blogeintrag zu verfassen, muss dieser einer Kategorie zugeordnet werden.<br>
				Sollte noch keine Kategorie vorhanden sein, erstellen Sie diese bitte zunächst.
			</p> 
			
			
			<!-- ---------- FORM 'NEW BLOG ENTRY' START ---------- -->
			<form action="" method="POST" enctype="multipart/form-data">
				<input class="dashboard" type="hidden" name="formNewBlogEntry">
				
				<br>
				<label>Kategorie:</label>
				<select class="dashboard bold" name="catID">	
				<?php if( $allCategoriesArray !== false ): ?>				
					<?php foreach($allCategoriesArray AS $categoryObject): ?>
						<option value='<?= $categoryObject->getCatID() ?>' 
						<?php if($blog?->getCategory()->getCatID() == $categoryObject->getCatID()) echo 'selected'?>><?= $categoryObject->getCatLabel() ?></option>
					<?php endforeach ?>
				<?php else: ?>
					<option value='' style='color: darkred'>Bitte zuerst eine Kategorie anlegen!</option>			
				<?php endif ?>
				</select>
				
				<br>
				
				<label>Überschrift:</label>
				<span class="error"><?= $errorHeadline ?></span><br>
				<input class="dashboard" type="text" name="blogHeadline" placeholder="..." value="<?= $blog?->getBlogHeadline() ?>"><br>
				
				
				<!-- ---------- IMAGE UPLOAD START ---------- -->
				<label>[Optional] Bild veröffentlichen:</label>
				<span class="error"><?= $errorImageUpload ?></span>
				<imageUpload>					
					
					<!-- -------- INFOTEXT FOR IMAGE UPLOAD START -------- -->
					<p class="small">
						Erlaubt sind Bilder des Typs 
						<?php $allowedMimetypes = implode( ', ', array_keys(IMAGE_ALLOWED_MIME_TYPES) ) ?>
						<?= strtoupper( str_replace( array(', image/jpeg', 'image/'), '', $allowedMimetypes) ) ?>.
						<br>
						Die Bildbreite darf 	<?= IMAGE_MAX_WIDTH ?> Pixel nicht übersteigen.<br>
						Die Bildhöhe darf 	<?= IMAGE_MAX_HEIGHT ?> Pixel nicht übersteigen.<br>
						Die Dateigröße darf 	<?= IMAGE_MAX_SIZE/1024 ?>kB nicht übersteigen.
					</p>
					<!-- -------- INFOTEXT FOR IMAGE UPLOAD END -------- -->
					
					<input type="file" name="blogImage">
					<select class="alignment fright" name="blogImageAlignment">
						<option value="fleft" 	<?php if($blog?->getBlogImageAlignment() == 'fleft') echo 'selected'?>>align left</option>
						<option value="fright" 	<?php if($blog?->getBlogImageAlignment() == 'fright') echo 'selected'?>>align right</option>
					</select>
				</imageUpload>
				<br>	
				<!-- ---------- IMAGE UPLOAD END ---------- -->
				
				
				<label>Inhalt des Blogeintrags:</label>
				<span class="error"><?= $errorContent ?></span><br>
				<textarea class="dashboard" name="blogContent" placeholder="..."><?= $blog?->getBlogContent() ?></textarea><br>
				
				<div class="clearer"></div>
				
				<input class="dashboard" type="submit" value="Veröffentlichen">
			</form>
			<!-- ---------- FORM 'NEW BLOG ENTRY' END ---------- -->
			
		</main>
		<!-- ---------- LEFT PAGE COLUMN END ---------- -->		
		
		
		<!-- ---------- RIGHT PAGE COLUMN START ---------- -->
		<aside class="forms fright">		
			<h2 class="dashboard">Neue Kategorie anlegen</h2>
			
			
			<!-- ---------- FORM 'NEW CATEGORY' START ---------- -->			
			<form class="dashboard" action="" method="POST">
			
				<input class="dashboard" type="hidden" name="formNewCategory">
				
				<label>Name der neuen Kategorie:</label>
				<span class="error"><?= $errorCatLabel ?></span><br>
				<!--
						Neu in PHP8: Der 'Chaining Operator ->' ist nun nullable (optional Chaining Operator: '?->'), 
						d.h. dass vor einem etwaigen Methodenaufruf geprüft wird, ob die Variable des Objekts existiert 
						und einen anderen Wert als NULL hat (entspricht der Existenzprüfung mittels isset()).
						Nur wenn die Variable existiert und nicht NULL ist, wird der anschließende Methodenaufruf 
						durchgeführt.
						
						Der Vorteil dieses Optional Chaining Operators ist, dass man nicht mehr gezwungen ist, jedes 
						auf einer Seite verwendete Objekt als leeres Objekt vorzuinitialisieren. Eine Initialisierung
						mit NULL reicht nun völlig aus.
					-->
				<input class="dashboard" type="text" name="catLabel" placeholder="..." value="<?= $category?->getCatLabel() ?>"><br>

				<input class="dashboard" type="submit" value="Neue Kategorie anlegen">
			</form>
			<!-- ---------- FORM 'NEW CATEGORY' END ---------- -->
			
		
		</aside>

		<div class="clearer"></div>
		<!-- ---------- RIGHT PAGE COLUMN END ---------- -->

	</body>
</html>