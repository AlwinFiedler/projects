<?php
#*******************************************************************************************#

				
				#******************************************#
				#********** ENABLE STRICT TYPING **********#
				#******************************************#

				declare(strict_types=1);
				

#*******************************************************************************************#


				#***********************************#
				#********** CLASS BLOG *************#
				#***********************************#

				
#*******************************************************************************************#

				/**
				 * Die Klasse Blog repräsentiert einen einzelnen Blogeintrag.
				 * Sie enthält Informationen wie Überschrift, Inhalt, Bild, 
				 * zugehörige Kategorie und Benutzer.
				 */
				class Blog {
					
					#*******************************#
					#********** ATTRIBUTE **********#
					#*******************************#
					
					private $blogID;
					private $blogHeadline;
					private $blogImagePath;
					private $blogImageAlignment;
					private $blogContent;
					private $blogDate;
					
					private $category;
					private $user;

					
					#***********************************************************#
					
					
					#*********************************#
					#********** CONSTRUCTOR **********#
					#*********************************#
					
					/**
					 * Blog constructor:
					 *
					 * @param Category|null 	$category
					 * @param User|null 		$user
					 * @param int|string|null 	$blogID
					 * @param string|null 		$blogHeadline
					 * @param string|null 		$blogImagePath
					 * @param string|null 		$blogImageAlignment
					 * @param string|null 		$blogContent
					 * @param string|null 		$blogDate
					 */					
					public function __construct(  	$category 			= new Category(), 
													$user 				= new User(),
													$blogID 			= NULL,
													$blogHeadline 		= NULL,
													$blogImagePath 		= NULL,
													$blogImageAlignment = NULL, 
													$blogContent 		= NULL, 
													$blogDate 			= NULL  	) 
					{
if(DEBUG_CC)		echo "<p class='debug class'>🛠 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "()  (<i>" . basename(__FILE__) . "</i>)</p>\n";						
						
						$this->setCategory($category);
						$this->setUser($user);						
						
						if($blogID 				!== NULL 	AND $blogID				!== '') 	$this->setBlogID($blogID);
						if($blogHeadline 		!== NULL 	AND $blogHeadline 		!== '') 	$this->setBlogHeadline($blogHeadline);
						if($blogImagePath 		!== NULL 	AND $blogImagePath 		!== '') 	$this->setBlogImagePath($blogImagePath);
						if($blogImageAlignment 	!== NULL 	AND $blogImageAlignment !== '') 	$this->setBlogImageAlignment($blogImageAlignment);
						if($blogContent 		!== NULL 	AND $blogContent 		!== '') 	$this->setBlogContent($blogContent);
						if($blogDate 			!== NULL 	AND $blogDate 			!== '') 	$this->setBlogDate($blogDate);
											
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
				
					#********** BLOG ID **********#
					public function getBlogID():NULL|int {
						return $this->blogID;
					}
					public function setBlogID(int|string $value):void {						
						#********** VALIDATE DATA FORMAT **********#
						if( ($value = filter_var($value, FILTER_VALIDATE_INT)) === false ) {
if(DEBUG_C)				echo "<p class='debug class err'><b>Line " . __LINE__ . "</b>: " . __METHOD__ . "():  Muss dem Format 'Integer' entsprechen! <i>(" . basename(__FILE__) . ")</i></p>\n";							
						} else {
							$this->blogID = $value;
						}
					}
					
					
					#********** BLOG HEADLINE **********#
					public function getBlogHeadline():NULL|string {
						return $this->blogHeadline;
					}
					public function setBlogHeadline(string $value):void {						
						$this->blogHeadline = Sanitizer::sanitizeString($value);
					}
					
					
					#********** BLOG IMAGE PATH **********#
					public function getBlogImagePath():NULL|string {
						return $this->blogImagePath;
					}
					public function setBlogImagePath(string $value):void {						
						$this->blogImagePath = Sanitizer::sanitizeString($value);
					}
					
					
					#********** BLOG IMAGE ALIGNMENT **********#
					public function getBlogImageAlignment():NULL|string {
						return $this->blogImageAlignment;
					}
					public function setBlogImageAlignment(string $value):void {						
						$this->blogImageAlignment = Sanitizer::sanitizeString($value);
					}
					
					
					#********** BLOG CONTENT **********#
					public function getBlogContent():NULL|string {
						return $this->blogContent;
					}
					public function setBlogContent(string $value):void {						
						$this->blogContent = Sanitizer::sanitizeString($value);
					}
					
					
					#********** BLOG DATE **********#
					public function getBlogDate():NULL|string {
						return $this->blogDate;
					}
					public function setBlogDate(string $value):void {						
						$this->blogDate = Sanitizer::sanitizeString($value);
					}
				
					
					#********** CATEGORY OBJECT **********#
					public function getCategory():Category {
						return $this->category;
					}
					public function setCategory(Category $value):void {						
						$this->category = $value;
					}
					
					
					#********** USER OBJECT **********#
					public function getUser():User {
						return $this->user;
					}
					public function setUser(User $value):void {						
						$this->user = $value;
					}
					
					
					#***********************************************************#
					

					#******************************#
					#********** METHODEN **********#
					#******************************#
					
					/**
					 * Speichert den aktuellen Blogeintrag in der Datenbank.
					 *
					 * @param PDO $PDO
					 * @return int Anzahl betroffener Datensätze
					 */
					public function saveBlogToDB(PDO $PDO):int {
if(DEBUG_C)			echo "<p class='debug class'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</p>\n";

						$sql 		= 'INSERT INTO blog (blogHeadline, blogImagePath, blogImageAlignment, blogContent, catID, userID)
										VALUES (:ph_blogHeadline, :ph_blogImagePath, :ph_blogImageAlignment, :ph_blogContent, :ph_catID, :ph_userID)';							
						$placeholders 	= array( 'ph_blogHeadline' 		=> $this->getBlogHeadline(),
												'ph_blogImagePath' 		=> $this->getBlogImagePath(),
												'ph_blogImageAlignment' => $this->getBlogImageAlignment(),
												'ph_blogContent' 		=> $this->getBlogContent(),
												'ph_catID' 				=> $this->getCategory()->getCatID(),
												'ph_userID' 			=> $this->getUser()->getUserID());
						
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
					 * Lädt alle Blogeinträge aus der Datenbank.
					 *
					 * @param PDO $PDO
					 * @param string|null $categoryFilterID Optionaler Kategorie-Filter
					 * @return array<int, Blog> Array von Blogobjekten, indiziert nach blogID
					 */
					public static function fetchAllBlogsFromDB(PDO $PDO, string $categoryFilterID = NULL){
if(DEBUG_C)			echo "<p class='debug class'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "('$categoryFilterID') (<i>" . basename(__FILE__) . "</i>)</p>\n";

					$allBlogObjectsArray = array();

						$sql 		= 'SELECT * FROM blog
										INNER JOIN user USING(userID)
										INNER JOIN category USING(catID)';				
						/* case a) 
							No condition and therefore no placeholder needed*/
						$placeholders = array();
						
						/*
							for case b)
								a condition for the category filter 
								has to be added to the sql statement 
								and therefore a placeholder must be 
								assigned and filled with a value
						*/
					
						if($categoryFilterID !== NULL) {
							$sql 		.= ' WHERE catID = :ph_catID';
							$placeholders['ph_catID'] = $categoryFilterID;
						}
						
						/*
							for both cases finally add the 'order by' command, which has to be 
							the last command in the sql statement (after any WHERE condition)
						*/
						
						$sql			.= ' ORDER BY blogDate DESC';
						
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
							
							// CONSTRUCTOR SIGNATURE:
							// $category = new Category(),
							// $user = new User(),
							// $blogID = NULL, 
							// $blogHeadline = NULL, 
							// $blogImagePath = NULL,
							// $blogImageAlignment = NULL, 
							// $blogContent = NULL, 
							// $blogDate = NULL 
							
								// CONSTRUCTOR SIGNATURE:
								// $catID = NULL, $catLabel = NULL
							
								// CONSTRUCTOR SIGNATURE:
								// $userID = NULL, $userFirstName = NULL, $userLastName = NULL,
								// $userEmail = NULL, $userCity = NULL, $userPassword = NULL
							
							$allBlogObjectsArray[$resultSet['blogID']] = new Blog(
																new Category($resultSet['catID'], $resultSet['catLabel'] ),
																new User($resultSet['userID'], $resultSet['userFirstName'], $resultSet['userLastName'], $resultSet['userEmail'], $resultSet['userCity'], $resultSet['userPassword'],),
																$resultSet['blogID'],
																$resultSet['blogHeadline'],
																$resultSet['blogImagePath'],
																$resultSet['blogImageAlignment'],
																$resultSet['blogContent'],
																$resultSet['blogDate']
															);
						}
						return $allBlogObjectsArray;
					}
					
					
					#***********************************************************#
					
				}
				
				
#*******************************************************************************************#