<?php
#*******************************************************************************************#

				
				/**
				 * Hauptskript der Blog-Applikation.
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


#*******************************************************************************************#


				#***********************************#
				#********** CLASS TESTING **********#
				#***********************************#
				
				/**
				 * Testinstanzen für Category, User und Blog zur Entwicklung und Fehlersuche.
				 */
		
				#********** Category **********#
// if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Teste Class 'Category'... <i>(" . basename(__FILE__) . ")</i></p>\n";
				
				// CONSTRUCTOR SIGNATURE:
				// $catID = NULL, $catLabel = NULL
				
				// Leeres Objekt
//				$category0 = new Category();
				
				// gefülltes Objekt
//				$category1 = new Category( '1', 'Lifestyle' );	// Object comes from FORM				
//				$category2 = new Category( 2, 'Food');			// Object comes from DB
				
				// teilgefülltes Objekt
//				$category3 = new Category( catID:'3' );
//				$category4 = new Category( catLabel:'Living' );	// Object comes from FORM
				
				
				#********** User **********#
// if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Teste Class 'User'... <i>(" . basename(__FILE__) . ")</i></p>\n";
				
				// CONSTRUCTOR SIGNATURE:
				// $userID = NULL, $userFirstName = NULL, $userLastName = NULL,
				// $userEmail = NULL, $userCity = NULL, $userPassword = NULL
				
				// Leeres Objekt
//				$user0 = new User();
				
				// gefülltes Objekt
//				$user1 = new User( userID:NULL, userFirstName:'Peter', userLastName:'Petersen',
//										 userEmail:'a@b.c', userCity:'New York', userPassword:NULL );	// Object comes from FORM				
//				$user2 = new User( userID:2, userFirstName:'Paul', userLastName:'Paulsen',
//										 userEmail:'paul@paulsen.net', userCity:'Paris', userPassword:'1234');	// Object comes from DB
				
				// teilgefülltes Objekt
//				$user3 = new User( userFirstName:'Susi', userLastName:'Sonnenschein',
//										 userEmail:'su@si.de' );


				#********** BLOG **********#
// if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Teste Class 'Blog'... <i>(" . basename(__FILE__) . ")</i></p>\n";
				
				// CONSTRUCTOR SIGNATURE:
					// $category = new Category(),
					// $user = new User(),
					// $blogID = NULL, 
					// $blogHeadline = NULL, 
					// $blogImagePath = NULL,
					// $blogImageAlignment = NULL, 
					// $blogContent = NULL, 
					// $blogDate = NULL 
				
				// Leeres Objekt
//				$blog0 = new Blog();
				
				// gefülltes/teilgefülltes Objekt
//				$blog1 = new Blog( 	category:$category1,
//									user:$user1, 
//									blogHeadline:'Typoblindtext', 
//									blogContent:'Dies ist ein Typoblindtext.'	);	// Object comes from FORM
									
												 
//				$blog2 = new Blog( 	$category2, 
//									$user2, 
//									6,
//									'Trappatoni \'98',
//									'./uploads/blogimages/744482DUUSMEATHZNJDHGNI857006_01 - calvin.gif',
//									'fleft',
//									'Es gibt im Moment in diese Mannschaft, oh, einige Spieler vergessen ihnen Profi was sie sind.',
//									'2017-08-24 14:41:22'	);	// Object comes from DB
									
				
#*******************************************************************************************#


				#******************************************#
				#********** INITIALIZE VARIABLES **********#
				#******************************************#
				
				/**
				 * Initialisierung von Variablen zur späteren Verwendung.
				 */
				
				$loggedIn				= NULL;
				$loginError 			= NULL;
				$categoryFilterID		= NULL;				


#*******************************************************************************************#
					
			
				#************************************#
				#********** VALIDATE LOGIN **********#
				#************************************#
				
				/**
				 * Startet die Session und prüft, ob ein gültiger Login vorliegt.
				 */
				
				session_name("wwwblogprojectde");
				
				
				#********** START/CONTINUE SESSION **********#
				if( session_start() === false ) {
					// Fehlerfall
if(DEBUG)		echo "<p class='debug auth err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n";				
									
				} else {
					// Erfolgsfall
if(DEBUG)		echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Session <i>'wwwblogprojectde'</i> erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n";
// if(DEBUG_V)	echo "<pre class='debug auth value'><b>Line " . __LINE__ . "</b>: \$_SESSION<br>". print_r($_SESSION, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";

					
					#*******************************************#
					#********** CHECK FOR VALID LOGIN **********#
					#*******************************************#

					
					#********** A) NO VALID LOGIN **********#				
					if( isset($_SESSION['ID']) === false OR $_SESSION['IPAddress'] !== $_SERVER['REMOTE_ADDR'] ) {
						// Fehlerfall | User ist nicht eingeloggt
if(DEBUG)			echo "<p class='debug auth'><b>Line " . __LINE__ . "</b>: User ist nicht eingeloggt. <i>(" . basename(__FILE__) . ")</i></p>\n";				

						session_destroy();
						
						$loggedIn = false;
					
					
					#********** B) VALID LOGIN **********#
					} else {
						// Erfolgsfall | User ist eingeloggt
if(DEBUG)			echo "<p class='debug auth'><b>Line " . __LINE__ . "</b>: User ist eingeloggt. <i>(" . basename(__FILE__) . ")</i></p>\n";				
					
						session_regenerate_id(true);
												
						$loggedIn = true;
						
					} // CHECK FOR VALID LOGIN END
					
				} // VALIDATE LOGIN END
				
				
#*******************************************************************************************#


				#****************************************#
				#********** PROCESS FORM LOGIN **********#
				#****************************************#
				
				/**
				 * Verarbeitet das Login-Formular:
				 * - Formularvalidierung
				 * - Datenbankabfrage
				 * - Passwortprüfung
				 * - Sessionaufbau
				 */

				#********** PREVIEW POST ARRAY **********#
// if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST<br>". print_r($_POST, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";
				#****************************************#				
						
				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formLogin']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: Formular 'Login' wurde abgeschickt... <i>(" . basename(__FILE__) . ")</i></p>";				


					// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";

					#********** GENERATE USER OBJECT **********#
					// $userID = NULL, $userFirstName = NULL, $userLastName = NULL,
					// $userEmail = NULL, $userCity = NULL, $userPassword = NULL
					$user = new User(userEmail:$_POST['userEmail']);
					
					#********** GENERATE HELPER VARIABLE **********#
					$loginPassword = Sanitizer::sanitizeString($_POST['loginPassword']);
					
if(DEBUG_V)		echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$loginPassword: $loginPassword <i>(" . basename(__FILE__) . ")</i></p>";


					// Schritt 3 FORM: Werte validieren
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";

					$errorLoginName 		= SimpleValidator::validateEmail($user->getUserEmail());
					$errorLoginPassword 	= SimpleValidator::validateInputString($loginPassword);
					
					
					#********** FINAL FORM VALIDATION **********#					
					if( $errorLoginName !== NULL OR $errorLoginPassword !== NULL ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>";						
						$loginError = 'Benutzername oder Passwort falsch!';
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>";						
									
						// Schritt 4 FORM: Daten weiterverarbeiten
						
						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#
						
						#********** FETCH USER DATA FROM DB BY EMAIL **********#
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Userdaten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						// Schritt 1 DB: DB-Verbindung herstellen
						$PDO = dbConnect('blog_oop');
						
						// Schritt 2-4 DB: Userdaten aus DB laden und ins Userobjekt schreiben
						$loginSuccess = $user->fetchUserFromDbByEmail($PDO);						
						
						// DB-Verbindung schließen
						dbClose($PDO, $PDOStatement);
						
						
						#********** VERIFY LOGIN **********#						
						if( $loginSuccess === false ) {
							// Fehlerfall:
if(DEBUG)				echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: FEHLER: Benutzername wurde nicht in DB gefunden! <i>(" . basename(__FILE__) . ")</i></p>";
							$loginError = 'Benutzername oder Passwort falsch!';

						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Benutzername wurde in DB gefunden. <i>(" . basename(__FILE__) . ")</i></p>";
													
						
							#********** VERIFY PASSWORD **********#							
							if( password_verify( $loginPassword, $user->getUserPassword()) === false ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: FEHLER: Passwort stimmt nicht mit DB überein! <i>(" . basename(__FILE__) . ")</i></p>";
								$loginError = 'Benutzername oder Passwort falsch!';
							
							} else {
								// Erfolgsfall
if(DEBUG)					echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Passwort stimmt mit DB überein. LOGIN OK. <i>(" . basename(__FILE__) . ")</i></p>";
							
																
								#********** START SESSION **********#
								if( session_start() === false ) {
									// Fehlerfall
if(DEBUG)						echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n";				
									$loginError = 'Der Loginvorgang konnte nicht durchgeführt werden!<br>
														Bitte überprüfen Sie die Sicherheitseinstellungen Ihres Browsers und 
														aktivieren Sie die Annahme von Cookies für diese Seite.';
									
									// TODO: Eintrag in ErrorLogFile
									
								} else {
									// Erfolgsfall
if(DEBUG)						echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Session erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n";				
																	
									
									#********** SAVE USER DATA INTO SESSION **********#
if(DEBUG)						echo "<p class='debug'>Line <b>" . __LINE__ . "</b>: Schreibe Userdaten in Session... <i>(" . basename(__FILE__) . ")</i></p>";
									
									$_SESSION['IPAddress'] = $_SERVER['REMOTE_ADDR'];
									$_SESSION['ID'] 	   = $user->getUserID();

									$_SESSION['userFirstName'] = $user->getUserFirstName();
									$_SESSION['userLastName']  = $user->getUserLastName();
									$_SESSION['userCity'] 	   = $user->getUserCity();
									
									
									#********** REDIRECT TO DASHBOARD **********#								
									header('Location: dashboard.php');
									
								} // START SESSION END
							
							} // VERIFY PASSWORD END
							
						} // VERIFY LOGIN NAME END
						
					} // FINAL FORM VALIDATION END

				} // PROCESS FORM LOGIN END
				
				
#***************************************************************************************#

			
				#********************************************#
				#********** PROCESS URL PARAMETERS **********#
				#********************************************#
				
				/**
				 * Verarbeitet übergebene URL-Parameter:
				 * - action=logout: Beendet die Session
				 * - action=filterByCategory: Setzt Filter-ID für spätere DB-Abfrage
				 */
				
				// Schritt 1 URL: Prüfen, ob Parameter übergeben wurde
				if( isset($_GET['action']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: URL-Parameter 'action' wurde übergeben. <i>(" . basename(__FILE__) . ")</i></p>\n";										
			
					// Schritt 2 URL: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$action = Sanitizer::sanitizeString($_GET['action']);
if(DEBUG_V)		echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$action: $action <i>(" . basename(__FILE__) . ")</i></p>";
		
					// Schritt 3 URL: ggf. Verzweigung							
							
					#********** LOGOUT **********#					
					if( $_GET['action'] === 'logout' ) {
if(DEBUG)			echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: 'Logout' wird durchgeführt... <i>(" . basename(__FILE__) . ")</i></p>";	
						
						session_destroy();
						header("Location: ./");
						exit();
						
						
					#********** FILTER BY CATEGORY **********#
					} elseif( $action === 'filterByCategory' ) {
if(DEBUG)			echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: Kategoriefilter aktiv... <i>(" . basename(__FILE__) . ")</i></p>";				
						
						
						#********** FETCH SECOND URL PARAMETER **********#
						if( isset($_GET['catID']) === true ) {
							
							// use $categoryFilterID as flag
							$categoryFilterID = Sanitizer::sanitizeString($_GET['catID']);
if(DEBUG_V)				echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$categoryFilterID: $categoryFilterID <i>(" . basename(__FILE__) . ")</i></p>\n";			
							
							/*
								TODO:
								- check if format of ID is integer
								- check if ID is actually existing in DB
							*/
						}

					} // BRANCHING END
					
				} // PROCESS URL PARAMETERS END
				
				
#***************************************************************************************#


				#************************************************#
				#********** FETCH BLOG ENTRIES FORM DB **********#
				#************************************************#

				/**
				 * Ruft Blogeinträge aus der Datenbank ab – optional gefiltert nach Kategorie-ID.
				 */				
				
if(DEBUG)	echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: Lade Blogs aus DB... <i>(" . basename(__FILE__) . ")</i></p>";
				
				// Schritt 1 DB: DB-Verbindung herstellen
				$PDO = dbConnect('blog_oop');
				
				$allBlogsArray = Blog::fetchAllBlogsFromDB($PDO, $categoryFilterID);
if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$allBlogsArray<br>". print_r($allBlogsArray, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";
				
				
				#********** A) FETCH ALL BLOG ENTRIES **********#
				if( $categoryFilterID === NULL ) {
if(DEBUG)		echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: Lade alle Blog-Einträge... <i>(" . basename(__FILE__) . ")</i></p>";


				#********** B) FILTER BLOG ENTRIES BY CATEGORY ID **********#				
				} else {
if(DEBUG)		echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: Filtere Blog-Einträge nach Kategorie-ID$categoryFilterID... <i>(" . basename(__FILE__) . ")</i></p>";					
					
				}
				
				// DB-Verbindung schließen
				dbClose($PDO, $PDOStatement);
				
				
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
?>

<!doctype html>

<html>

	<head>
		<meta charset="utf-8">
		<title>PHP-Projekt Blog</title>
		<link rel="stylesheet" href="./css/main.css">
		<link rel="stylesheet" href="./css/debug.css">
	</head>
	
	<body>
	
		<!-- ---------- PAGE HEADER START ---------- -->
		<header class="fright">
			
			<?php if( $loggedIn === false ): ?>
				<?php if($loginError): ?>
				<p class="error"><b><?= $loginError ?></b></p>
				<?php endif ?>
				
				<!-- -------- Login Form START -------- -->
				<form action="" method="POST">
					<input type="hidden" name="formLogin">
					<input type="text" name="userEmail" placeholder="Email">
					<input type="password" name="loginPassword" placeholder="Password">
					<input type="submit" value="Login">
				</form>
				<!-- -------- Login Form END -------- -->
				
			<?php else: ?>
				<!-- -------- PAGE LINKS START -------- -->
				<a href="?action=logout">Logout</a><br>
				<a href='dashboard.php'>zum Dashboard >></a>
				<!-- -------- PAGE LINKS END -------- -->
			<?php endif ?>
		
		</header>
		
		<div class="clearer"></div>
				
		<br>
		<hr>
		<br>		
		<!-- ---------- PAGE HEADER END ---------- -->
		
		
		<h1>PHP-Projekt Blog</h1>
		<p><a href='./'>:: Alle Einträge anzeigen ::</a></p>		
		
		
		<!-- ---------- BLOG ENTRIES START ---------- -->		
		<main class="blogs fleft">
			
			<?php if(empty($allBlogsArray) === true ): ?>
				<p class="info">Noch keine Blogeinträge vorhanden.</p>
			
			<?php else: ?>
				
				<?php foreach( $allBlogsArray AS $singleBlogItemObject ): ?>
					<?php $dateTimeArray = isoToEuDateTime($singleBlogItemObject->getBlogDate()) ?>
					
					<article class='blogEntry'>
					
						<a name='entry<?= $singleBlogItemObject->getBlogID() ?>'></a>
						
						<p class='fright'><a href='?action=filterByCategory&catID=<?= $singleBlogItemObject->getCategory()->getCatID() ?>'>Kategorie: <?= $singleBlogItemObject->getCategory()->getCatLabel() ?></a></p>
						<h2 class='clearer'><?= $singleBlogItemObject->getBlogHeadline() ?></h2>

						<p class='author'><?= $singleBlogItemObject->getUser()->getUserFirstName() ?> <?= $singleBlogItemObject->getUser()->getUserLastName() ?>
						(<?= $singleBlogItemObject->getUser()->getUserCity() ?>) schrieb am <?= $dateTimeArray['date'] ?> um <?= $dateTimeArray['time'] ?> Uhr:</p>
						
						<p class='blogContent'>
						
							<?php if( $singleBlogItemObject->getBlogImagePath() !== NULL ): ?>
								<img class='<?= $singleBlogItemObject->getBlogImageAlignment() ?>' src='<?= $singleBlogItemObject->getBlogImagePath() ?>' alt='' title=''>
							<?php endif ?>
							
							<?= nl2br( $singleBlogItemObject->getBlogContent(), false ) ?>	
						</p>
						
						<div class='clearer'></div>
						
						<br>
						<hr>
						
					</article>
					
				<?php endforeach ?>
			<?php endif ?>
			
		</main>		
		<!-- ---------- BLOG ENTRIES END ---------- -->
		
		
		<!-- ---------- CATEGORY FILTER LINKS START ---------- -->		
		<nav class="categories fright">

			<?php if( $allCategoriesArray === false ): ?>
				<p class="info">Noch keine Kategorien vorhanden.</p>
			
			<?php else: ?>
			
				<?php foreach( $allCategoriesArray AS $categorySingleItemObject ): ?>
					<p><a href="?action=filterByCategory&catID=<?= $categorySingleItemObject->getCatID()?>" <?php 
						if( $categorySingleItemObject->getCatID() == $categoryFilterID )
							echo 'class="active"' ?>><?= $categorySingleItemObject->getCatLabel() ?></a></p>
				<?php endforeach ?>

			<?php endif ?>
		</nav>

		<div class="clearer"></div>
		<!-- ---------- CATEGORY FILTER LINKS END ---------- -->

	</body>

</html>