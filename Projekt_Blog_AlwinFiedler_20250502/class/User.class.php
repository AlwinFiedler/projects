<?php
#*******************************************************************************************#

				
				#******************************************#
				#********** ENABLE STRICT TYPING **********#
				#******************************************#
				
				
				declare(strict_types=1);
				

#*******************************************************************************************#


				#********************************#
				#********** CLASS USER **********#
				#********************************#

				
#*******************************************************************************************#


				/**
				*
				*	Diese Klasse repräsentiert ein Benutzerobjekt mit zugehörigen Eigenschaften
				*	wie Vorname, Nachname, E-Mail, Stadt, Passwort
				*
				*/
				class User {
					
					#*******************************#
					#********** ATTRIBUTE **********#
					#*******************************#
					
					private $userID;
					private $userFirstName;
					private $userLastName;
					private $userEmail;
					private $userCity;
					private $userPassword;


					#***********************************************************#
					
					
					#*********************************#
					#********** CONSTRUCTOR **********#
					#*********************************#

					/**
					*	User constructor:
					*
					*	@param	Integer|String|NULL	$userID
					*	@param	String|NULL			$userFirstName
					*	@param	String|NULL			$userLastName
					*	@param	String|NULL			$userEmail
					*	@param	String|NULL			$userCity
					*	@param	String|NULL			$userPassword
					*
					*/
					public function __construct(  	$userID 		= NULL, 
													$userFirstName 	= NULL, 
													$userLastName 	= NULL,
													$userEmail 		= NULL, 
													$userCity 		= NULL, 
													$userPassword 	= NULL		) 
					{
if(DEBUG_CC)		echo "<p class='debug class'>🛠 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "()  (<i>" . basename(__FILE__) . "</i>)</p>\n";						
						
						if($userID 			!== NULL 	AND $userID 		!== '') 	$this->setUserID($userID);
						if($userFirstName 	!== NULL 	AND $userFirstName 	!== '') 	$this->setUserFirstName($userFirstName);
						if($userLastName 	!== NULL 	AND $userLastName 	!== '') 	$this->setUserLastName($userLastName);
						if($userEmail 		!== NULL 	AND $userEmail 		!== '') 	$this->setUserEmail($userEmail);
						if($userCity 		!== NULL 	AND $userCity 		!== '') 	$this->setUserCity($userCity);
						if($userPassword 	!== NULL 	AND $userPassword 	!== '') 	$this->setUserPassword($userPassword);
											
if(DEBUG_CC)		echo "<pre class='debug class value'><b>Line " . __LINE__ . "</b>: \$this<br>". print_r($this, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";					
					}
				
				
					#********** DESTRUCTOR **********#
					public function __destruct() {
if(DEBUG_CC)		echo "<p class='debug class'>☠️  <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "()  (<i>" . basename(__FILE__) . "</i>)</p>\n";						
					}
					
					
					#***********************************************************#

					
					#*************************************#
					#********** GETTER & SETTER **********#
					#*************************************#
				
					#********** USER ID **********#
					public function getUserID():NULL|int {
						return $this->userID;
					}
					public function setUserID(int|string $value):void {						
						#********** VALIDATE DATA FORMAT **********#
						if( ($value = filter_var($value, FILTER_VALIDATE_INT)) === false ) {
if(DEBUG_C)				echo "<p class='debug class err'><b>Line " . __LINE__ . "</b>: " . __METHOD__ . "():  Muss dem Format 'Integer' entsprechen! <i>(" . basename(__FILE__) . ")</i></p>\n";							
						} else {
							$this->userID = $value;
						}
					}
					
					
					#********** USER FIRST NAME **********#
					public function getUserFirstName():NULL|string {
						return $this->userFirstName;
					}
					public function setUserFirstName(string $value):void {						
						$this->userFirstName = Sanitizer::sanitizeString($value);
					}
					
					
					#********** USER LAST NAME **********#
					public function getUserLastName():NULL|string {
						return $this->userLastName;
					}
					public function setUserLastName(string $value):void {						
						$this->userLastName = Sanitizer::sanitizeString($value);
					}
					
					
					#********** USER EMAIL **********#
					public function getUserEmail():NULL|string {
						return $this->userEmail;
					}
					public function setUserEmail(string $value):void {						
						$this->userEmail = Sanitizer::sanitizeString($value);
					}
					
					
					#********** USER CITY **********#
					public function getUserCity():NULL|string {
						return $this->userCity;
					}
					public function setUserCity(string $value):void {						
						$this->userCity = Sanitizer::sanitizeString($value);
					}
					
					
					#********** USER PASSWORD **********#
					public function getUserPassword():NULL|string {
						return $this->userPassword;
					}
					public function setUserPassword(string $value):void {						
						$this->userPassword = Sanitizer::sanitizeString($value);
					}					
					
					
					#***********************************************************#
					

					#******************************#
					#********** METHODEN **********#
					#******************************#
					
					/**
					*
					*	Lädt Benutzerdaten aus der Datenbank basierend auf der E-Mail-Adresse
					*	in das aktuelle Objekt.
					*
					*	@param	PDO	$PDO	Eine gültige PDO-Verbindung
					*
					*	@return	bool		TRUE bei Erfolg | FALSE bei Nichtfinden
					*
					*/
					public function fetchUserFromDbByEmail(PDO $PDO):bool {
if(DEBUG_C)			echo "<p class='debug class'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</p>\n";

						// Schritt 2 DB: SQL-Statement und Placeholders-Array erstellen	
						$sql = 'SELECT userID, userFirstName, userLastName, userEmail, userCity, userPassword FROM user 
								WHERE userEmail = :ph_userEmail';

						$placeholders = ['ph_userEmail' => $this->getUserEmail()];

						try {
							// Schritt 2 DB: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
					
							// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
							$PDOStatement->execute($placeholders);
					
						} catch(PDOException $error) {
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
							$dbError = 'Fehler beim Zugriff auf die Datenbank!';
							
						}
						
						// Schritt 4 DB: Daten weiterverarbeiten
						// Bei lesender Operation: Datensätze abholen
						$userData = $PDOStatement->fetch(PDO::FETCH_ASSOC);
						
						if ($userData === false) {
							// Fehlerfall: Kein Benutzer mit dieser E-Mail gefunden
							return false;						
						} else {
							// Erfolgsfall: Benutzer mit dieser E-Mail gefunden
							
							#********** WRITE DATA INTO CALLING OBJECT **********#
							// Mapping der Werte aus dem DB-Array in Objektattribute

							if($userData['userID'] 			!== NULL 	AND $userData['userID'] 		!== '') 	$this->setUserID($userData['userID']);
							if($userData['userFirstName'] 	!== NULL 	AND $userData['userFirstName'] 	!== '') 	$this->setUserFirstName($userData['userFirstName']);
							if($userData['userLastName'] 	!== NULL 	AND $userData['userLastName'] 	!== '') 	$this->setUserLastName($userData['userLastName']);
							if($userData['userEmail'] 		!== NULL 	AND $userData['userEmail'] 		!== '') 	$this->setUserEmail($userData['userEmail']);
							if($userData['userCity'] 		!== NULL 	AND $userData['userCity'] 		!== '') 	$this->setUserCity($userData['userCity']);
							if($userData['userPassword'] 	!== NULL 	AND $userData['userPassword'] 	!== '') 	$this->setUserPassword($userData['userPassword']);

							return true;
						
						}
					}

					
					
					#***********************************************************#
					
				}
				
				
#*******************************************************************************************#