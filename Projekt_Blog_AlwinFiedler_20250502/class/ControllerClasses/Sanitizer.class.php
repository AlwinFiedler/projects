<?php
#*******************************************************************************************#
				
				
				#******************************************#
				#********** ENABLE STRICT TYPING **********#
				#******************************************#
				
				declare(strict_types=1);
				
				
#*******************************************************************************************#


				#*************************************#
				#********** CLASS SANITITER **********#
				#*************************************#

				
#*******************************************************************************************#


				abstract class Sanitizer {
					
					
					#*************************************#
					#********** SANITIZE STRING **********#
					#*************************************#				
					
					/**
					*
					*	Ersetzt potentiell gefährliche Steuerzeichen durch HTML-Entities
					*	Entfernt vor und nach einem String Whitespaces
					*
					*	@params		String	$value		Die zu bereinigende Zeichenkette
					*
					*	@return		String				Die bereinigte Zeichenkette
					*
					*/
					public static function sanitizeString($value) {
						#********** LOCAL SCOPE START **********#
if(DEBUG_F)			echo "<p class='debug sanitizeString'>🌀<b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('$value') <i>(" . basename(__FILE__) . ")</i></p>\n";
											
						$value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
						$value = trim($value);
						
						// Entschärften und getrimmten String zurückgeben					
						/*
							Leerstrings aus dem Formular in NULL umwandeln, damit in der DB vorhandene
							NULL-Werte nicht mit Leerstrings überschrieben werden.
						*/
						if($value === '') $value = NULL;
						
						return $value;
						#********** LOCAL SCOPE END **********#
					}

					
					#***********************************************************#
					
				}
				
				
#*******************************************************************************************#