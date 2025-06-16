<?php
#*******************************************************************************************#
				
				
				#******************************************#
				#********** ENABLE STRICT TYPING **********#
				#******************************************#
				
				declare(strict_types=1);
				
				
#*******************************************************************************************#


				#********************************************#
				#********** CLASS SIMPLE VALIDATOR **********#
				#********************************************#

				
#*******************************************************************************************#


				abstract class SimpleValidator {
					
					
					#*******************************************#
					#********** VALIDATE INPUT STRING **********#
					#*******************************************#
					
					/**
					*
					*	Prüft einen übergebenen String auf Maximallänge sowie optional 
					* 	auf Mindestlänge und Pflichtangabe.
					*	Generiert Fehlermeldung bei Leerstring und gleichzeitiger Pflichtangabe 
					*	oder bei ungültiger Länge.
					*
					*	@param	NULL|String	$value									Der zu validierende String
					*	@param	Boolean		$mandatory=INPUT_STRING_MANDATORY		Angabe zu Pflichteingabe
					*	@param	Integer		$minLength=INPUT_STRING_MIN_LENGTH		Die zu prüfende Mindestlänge
					*	@param	Integer		$maxLength=INPUT_STRING_MAX_LENGTH		Die zu prüfende Maximallänge
					*
					*	@return	String|NULL														Fehlermeldung | ansonsten NULL
					*
					*/
					public static function validateInputString(	NULL|string $value,
																				bool $mandatory=INPUT_STRING_MANDATORY, 
																				int $minLength=INPUT_STRING_MIN_LENGTH, 
																				int $maxLength=INPUT_STRING_MAX_LENGTH ):NULL|string
					{
						#********** LOCAL SCOPE START **********#
if(DEBUG_F)			echo "<p class='debug validateInputString'>🌀<b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('$value' [$minLength|$maxLength] mandatory:$mandatory ) <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						
						#********** MANDATORY CHECK **********#
						if( $mandatory === true AND $value === NULL ) {
							// Fehlerfall
							return 'Dies ist ein Pflichtfeld!';
						}
						
						
						#********** MAXIMUM LENGTH CHECK **********#
						if( $value !== NULL AND mb_strlen($value) > $maxLength  ) {
							// Fehlerfall
							return "Darf maximal $maxLength Zeichen lang sein!";
						}
						
						
						#********** MINIMUM LENGTH CHECK **********#
						if( $value !== NULL AND mb_strlen($value) < $minLength  ) {
							// Fehlerfall
							return "Muss mindestens $minLength Zeichen lang sein!";
						}
						
						
						#********** NO ERROR **********#
						return NULL;
						#********** LOCAL SCOPE END **********#
					}


#**************************************************************************************#


					#*******************************************#
					#********** VALIDATE EMAIL FORMAT **********#
					#*******************************************#				
					
					/**
					*
					*	Prüft einen übergebenen String auf eine valide Email-Adresse und auf Leerstring.
					*	Generiert Fehlermeldung bei ungültiger Email-Adresse oder Leerstring
					*
					*	@param	NULL|String	$value							Der zu übergebende String
					*
					*	@return	String|NULL									Fehlermeldung | ansonsten NULL
					*
					*/
					public static function validateEmail(NULL|string $value):NULL|string {
						#********** LOCAL SCOPE START **********#
if(DEBUG_F)			echo "<p class='debug validateEmail'>🌀<b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('$value') <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						
						#********** MANDATORY CHECK **********#
						if( $value === NULL ) {
							// Fehlerfall
							return 'Dies ist ein Pflichtfeld!';
						}
						
						
						#********** VALIDATE EMAIL ADDRESS FORMAT **********#
						if( filter_var( $value, FILTER_VALIDATE_EMAIL ) === false ) {
							// Fehlerfall
							return 'Dies ist keine gültige Email-Adresse!';
						}
						
						
						#********** NO ERROR **********#
						return NULL;
						#********** LOCAL SCOPE END **********#
					}


#**************************************************************************************#


					#*******************************************#
					#********** VALIDATE IMAGE UPLOAD **********#
					#*******************************************#
					
					/**
					*
					*	Validiert ein auf den Server hochgeladenes Bild auf korrekten und erlaubten MIME-Type, 
					*	auf Bildtyp, Bildgröße in Pixeln, Dateigröße in Bytes sowie den Header auf Plausibilität.
					*	Generiert einen unique Dateinamen sowie eine sichere Dateiendung und verschiebt das Bild 
					*	in das Zielverzeichnis.
					*
					*	@param	String	$fileTemp													Der temporäre Pfad zum hochgeladenen Bild im Quarantäneverzeichnis
					*	@param	Integer	$imageMaxWidth				=IMAGE_MAX_WIDTH				Die maximal erlaubte Bildbreite in Pixeln				
					*	@param	Integer	$imageMaxHeight				=IMAGE_MAX_HEIGHT				Die maximal erlaubte Bildhöhe in Pixeln
					*	@param	Integer	$imageMinSize				=IMAGE_MIN_SIZE					Die minimal erlaubte Dateigröße in Bytes
					*	@param	Integer	$imageMaxSize				=IMAGE_MAX_SIZE					Die maximal erlaubte Dateigröße in Bytes
					*	@param	Array	$imageAllowedMimeTypes		=IMAGE_ALLOWED_MIME_TYPES		Whitelist der zulässigen MIME-Types mit den zugehörigen Dateiendungen
					*	@param	String	$imageUploadPath			=IMAGE_UPLOAD_PATH				Das Zielverzeichnis
					*
					*	@return	Array		{'imagePath'	=>	String|NULL, 								Bei Erfolg der Speicherpfad zur Datei im Zielverzeichnis | bei Fehler NULL
					*							 'imageError'	=>	String|NULL}								Bei Erfolg NULL | Bei Fehler Fehlermeldung
					*
					*/
					public static function validateImageUpload( 	string $fileTemp,
																				int $imageMaxWidth 				= IMAGE_MAX_WIDTH,
																				int $imageMaxHeight 				= IMAGE_MAX_HEIGHT,
																				int $imageMinSize 				= IMAGE_MIN_SIZE,
																				int $imageMaxSize 				= IMAGE_MAX_SIZE,
																				array $imageAllowedMimeTypes 	= IMAGE_ALLOWED_MIME_TYPES,
																				string $imageUploadPath 		= IMAGE_UPLOAD_PATH ):array
					{
						#********** LOCAL SCOPE START **********#
if(DEBUG_F)			echo "<p class='debug validateImageUpload'>🌀<b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('$fileTemp') <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						
						#**************************************************************************#
						#********** I. GATHER INFORMATION FOR IMAGE FILE VIA FILE HEADER **********#
						#**************************************************************************#
						
						$imageDataArray = getimagesize($fileTemp);
						
// if(DEBUG_F)		echo "<pre class='debug value validateImageUpload'><b>Line " . __LINE__ . "</b>: \$imageDataArray<br>". print_r($imageDataArray, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";
						
						
						#********** 1. CHECK FOR VALID MIME TYPE **********#
						if( $imageDataArray === false ) {
							// 1. Fehlerfall: MIME TYPE IS NO VALID IMAGE MIME TYPE
							return array( 'imagePath' => NULL, 'imageError' => 'Dies ist keine gültige Bilddatei!' );	
							
						} elseif( is_array($imageDataArray) === true ) {
							// Erfolgsfall: MIME TYPE IS A VALID IMAGE MIME TYPE
											
							$imageWidth 	= filter_var($imageDataArray[0], FILTER_VALIDATE_INT);
							$imageHeight 	= filter_var($imageDataArray[1], FILTER_VALIDATE_INT);
							$imageMimeType = Sanitizer::sanitizeString($imageDataArray['mime']);
							$fileSize 		= fileSize($fileTemp);
							
if(DEBUG_F)				echo "<p class='debug value validateImageUpload'><b>Line " . __LINE__ . "</b>: \$imageWidth: $imageWidth px <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_F)				echo "<p class='debug value validateImageUpload'><b>Line " . __LINE__ . "</b>: \$imageHeight: $imageHeight px <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_F)				echo "<p class='debug value validateImageUpload'><b>Line " . __LINE__ . "</b>: \$imageMimeType: $imageMimeType <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_F)				echo "<p class='debug value validateImageUpload'><b>Line " . __LINE__ . "</b>: \$fileSize: $fileSize Byte <i>(" . basename(__FILE__) . ")</i></p>\n";
							
						} // I. GATHER INFORMATION FOR IMAGE FILE VIA FILE HEADER END
						#*********************************************************************************#
						
						
						#******************************************#
						#********** II. IMAGE VALIDATION **********#
						#******************************************#
						
						
						#********** 2. VALIDATE PLAUSIBILITY OF FILE HEADER **********#
						if( !$imageWidth OR !$imageHeight OR !$imageMimeType OR $fileSize < $imageMinSize ) {
							// 2. Fehlerfall: NON PLAUSIBLE HEADER
							return array( 'imagePath' => NULL, 'imageError' => 'Verdächtiger Dateiheader!' );
						}
						
						
						#********** 3. VALIDATE IMAGE MIME TYPES **********#
						// WHITELIST mit erlaubten MIME TYPES
						if( array_key_exists( $imageMimeType, $imageAllowedMimeTypes ) === false ) {
							// 3. Fehlerfall: MIME TYPE IS NOT ALLOWED
							return array( 'imagePath' => NULL, 'imageError' => 'Dies ist kein erlaubter Bildtyp!' );
						}
						
						
						#********** 4. VALIDATE IMAGE WIDTH **********#
						if( $imageWidth > $imageMaxWidth ) {
							// 4. Fehlerfall: IMAGE WIDTH TOO BIG
							return array( 'imagePath' => NULL, 'imageError' => "Die Bildbreite darf maximal $imageMaxWidth Pixel betragen!" );
						}					
						
						
						#********** 5. VALIDATE IMAGE HEIGHT **********#
						if( $imageHeight > $imageMaxHeight ) {
							// 5. Fehlerfall: IMAGE HEIGHT TOO BIG
							return array( 'imagePath' => NULL, 'imageError' => "Die Bildhöhe darf maximal $imageMaxHeight Pixel betragen!" );
						}					
						
						
						#********** 6. VALIDATE FILE SIZE **********#
						if( $fileSize > $imageMaxSize ) {
							// 6. Fehlerfall: FILE SIZE TOO BIG
							return array( 'imagePath' => NULL, 'imageError' => 'Die Dateigöße darf maximal ' . $imageMaxSize/1024 . 'Kb betragen!' );
						
						} // II. IMAGE VALIDATION END
						#*********************************************************************************#
						
						
						#***************************************************************#
						#********** III. PREPARE IMAGE FOR PERSISTANT STORAGE **********#
						#***************************************************************#
						
						#********** 1. GENERATE UNIQUE FILE NAME **********#
						$fileName = mt_rand() . str_shuffle('0123456789_-abcdefghikbgnegknrewkfjbklnjklmnopqrstuvwxyz-_0123456789') . str_replace(array('.', ' '), '', microtime());


						#********** 2. GENERATE FILE EXTENSION **********#
						$fileExtension = $imageAllowedMimeTypes[$imageMimeType];
// if(DEBUG_F)		echo "<p class='debug value validateImageUpload'><b>Line " . __LINE__ . "</b>: \$fileExtension: '<i>$fileExtension</i>' <i>(" . basename(__FILE__) . ")</i></p>\n";

						
						#********** 3. GENERATE FILE TARGET **********#
						$fileTarget = $imageUploadPath . $fileName . $fileExtension;

if(DEBUG_F)			echo "<p class='debug value validateImageUpload'><b>Line " . __LINE__ . "</b>: Länge: " . mb_strlen($fileTarget) . " <i>(" . basename(__FILE__) . ")</i></p>\n";				
if(DEBUG_F)			echo "<p class='debug value validateImageUpload'><b>Line " . __LINE__ . "</b>: \$fileTarget: '<i>$fileTarget</i>' <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						// III. PREPARE IMAGE FOR PERSISTANT STORAGE END
						#*****************************************************************#
						
						
						#*********************************************************#
						#********** IV. MOVE IMAGE TO FINAL DESTINATION **********#
						#*********************************************************#

						if( @move_uploaded_file( $fileTemp, $fileTarget ) === false ) {
							// 7. Fehlerfall: IMAGE CANNOT BE MOVED
if(DEBUG_F)				echo "<p class='debug err validateImageUpload'><b>Line " . __LINE__ . "</b>: FEHLER beim verschieben der Datei von '<i>$fileTemp</i>' nach '<i>$fileTarget</i>'! <i>(" . basename(__FILE__) . ")</i></p>\n";
							
							// TODO: Eintrag in ErrorLog | Email an SysAdmin						
							return array( 'imagePath' => NULL, 'imageError' => 'Es ist ein Fehler aufgetreten! Bitte kontaktieren Sie unseren Support.' );
						
						} else {
							// Erfolgsfall
if(DEBUG_F)				echo "<p class='debug ok validateImageUpload'><b>Line " . __LINE__ . "</b>: Datei erfolgreich von '<i>$fileTemp</i>' nach '<i>$fileTarget</i>' verschoben. <i>(" . basename(__FILE__) . ")</i></p>\n";
							
							return array( 'imagePath' => $fileTarget, 'imageError' => NULL );
						}
						// IV. MOVE IMAGE TO FINAL DESTINATION END
						#*****************************************************************#
						
						#********** LOCAL SCOPE END **********#
					}

					
					#***********************************************************#
					
				}
				
				
#*******************************************************************************************#