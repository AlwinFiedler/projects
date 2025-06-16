<?php
#*******************************************************************************************#

				
				#******************************************#
				#********** ENABLE STRICT TYPING **********#
				#******************************************#
				

				declare(strict_types=1);
				

#*******************************************************************************************#


				#************************************#
				#********** CLASS CATEGORY **********#
				#************************************#

				
#*******************************************************************************************#

				/**
				*	Repräsentiert eine Kategorie mit ID und Label
				*/
				class Category {
					
					#*******************************#
					#********** ATTRIBUTE **********#
					#*******************************#
					
					private $catID;
					private $catLabel;

					
					#***********************************************************#
					
					
					#*********************************#
					#********** CONSTRUCTOR **********#
					#*********************************#
					
					/**
					*	Category constructor:
					*
					*	@param int|string|null 	$catID
					*	@param string|null 		$catLabel
					*/
					public function __construct( 	$catID 		= NULL,
													$catLabel 	= NULL		)
					{
if(DEBUG_CC)		echo "<p class='debug class'>🛠 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "()  (<i>" . basename(__FILE__) . "</i>)</p>\n";						
						
						if($catID 		!== NULL 	AND $catID 		!== '') 	$this->setCatID($catID);
						if($catLabel 	!== NULL 	AND $catLabel 	!== '') 	$this->setCatLabel($catLabel);
						
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
				
					#********** STATE ID **********#
					public function getCatID():NULL|int {
						return $this->catID;
					}
					public function setCatID(int|string $value):void {						
						#********** VALIDATE DATA FORMAT **********#
						if( ($value = filter_var($value, FILTER_VALIDATE_INT)) === false ) {
if(DEBUG_C)				echo "<p class='debug class err'><b>Line " . __LINE__ . "</b>: " . __METHOD__ . "():  Muss dem Format 'Integer' entsprechen! <i>(" . basename(__FILE__) . ")</i></p>\n";							
						} else {
							$this->catID = $value;
						}
					}
					
					
					#********** STATE LABEL **********#
					public function getCatLabel():NULL|string {
						return $this->catLabel;
					}
					public function setCatLabel(string $value):void {						
						$this->catLabel = Sanitizer::sanitizeString($value);
					}
										
					
					#***********************************************************#
					

					#******************************#
					#********** METHODEN **********#
					#******************************#
					
					/**
					*	Prüft, ob eine Kategorie mit dem aktuellen Label bereits in der Datenbank existiert.
					*
					*	@param PDO $PDO
					*	@return int Anzahl gefundener Datensätze
					*/
					public function checkIfCategoryExist(PDO $PDO):int {
if(DEBUG_C)			echo "<p class='debug class'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</p>\n";
						$sql 		= 'SELECT COUNT(catLabel) FROM category WHERE catLabel = :ph_catLabel';						
						$placeholders 	= array( 'ph_catLabel' => $this->getCatLabel() );
						
						try {
							// Schritt 2 DB: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
							
							// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
							$PDOStatement->execute($placeholders);						
							
						} catch(PDOException $error) {
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";									
							$dbError = 'Fehler beim Zugriff auf die Datenbank!';
						}
						
						$count = $PDOStatement->fetchColumn();
if(DEBUG_V)			echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$count: $count <i>(" . basename(__FILE__) . ")</i></p>";
						return $count;						

					}					

					/**
					*	Speichert die aktuelle Kategorie in der Datenbank.
					*
					*	@param PDO $PDO
					*	@return int Anzahl betroffener Datensätze (z. B. 1 bei Erfolg)
					*/
					public function saveCategoryToDB(PDO $PDO):int {
if(DEBUG_C)			echo "<p class='debug class'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</p>\n";
						$sql 		= 'INSERT INTO category (catLabel) VALUES (:ph_catLabel)';							
						$placeholders 	= array( 'ph_catLabel' => $this->getCatLabel() );
						
						try {
							// Schritt 2 DB: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
								
							// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
							$PDOStatement->execute($placeholders);						
								
						} catch(PDOException $error) {
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";									
							$dbError = 'Fehler beim Zugriff auf die Datenbank!';								
						}
							
						// Schritt 4 DB: Schreiberfolg prüfen
						$rowCount = $PDOStatement->rowCount();
						return $rowCount;
					}
					
					/**
					*	Lädt alle Kategorien aus der Datenbank und gibt sie als assoziatives Array von Category-Objekten zurück.
					*
					*	@param PDO $PDO
					*	@return array<int, Category>	Array mit catID als Schlüssel und Category-Objekten als Werte
					*/
					public static function fetchAllCategoriesFromDB(PDO $PDO){
if(DEBUG_C)			echo "<p class='debug class'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</p>\n";
						$sql 		= 'SELECT * FROM category';				
						$placeholders 	= NULL;
						
						try {
							// Schritt 2 DB: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
					
							// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
							$PDOStatement->execute($placeholders);						
					
						} catch(PDOException $error) {
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
							$dbError = 'Fehler beim Zugriff auf die Datenbank!';
					}

						// Schritt 4 DB: Daten weiterverarbeiten (und DB-Verbindung schließen)
						/*
							Bei lesenden Operationen wie SELECT und SELECT COUNT():
							Abholen der Datensätze bzw. auslesen des Ergebnisses
						*/
						while( $resultSet = $PDOStatement->fetch(PDO::FETCH_ASSOC) ) {
// if(DEBUG_C)				echo "<pre class='debug class value'><b>Line " . __LINE__ . "</b>: \$resultSet<br>". print_r($resultSet, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";
							/*
								WHITELISTING:
								Für ein vereinfachtes Whitelisting bietet es sich an, die ID der jeweiligen
								Datensätze als numerischen Index für das Transprotarray zu verwenden.
								
								Hierdurch kann später mittels der PHP-Funktion array_key_exists() einfach
								auf den Index des Transportarrays geprüft werden.
							*/
							// $catID = NULL, $catLabel = NULL
							$allCategoryObjectsArray[$resultSet['catID']] = new Category($resultSet['catID'], $resultSet['catLabel'] );
						}
						return $allCategoryObjectsArray;
					}
					
					
					#***********************************************************#
					
				}
				
				
#*******************************************************************************************#