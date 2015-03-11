<?php
	namespace Phast;
	
	abstract class RandomStringGeneratorCharacterSets
	{
		const AlphaNumericMixedCase = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		const AlphaNumericLowerCase = "abcdefghijklmnopqrstuvwxyz0123456789";
		const AlphaNumericUpperCase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		const AlphaOnlyUpperCase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		const AlphaOnlyLowerCase = "abcdefghijklmnopqrstuvwxyz";
	}
	
	/**
	 * Provides methods to generate a random string using a specified character set.
	 * @author Michael Becker
	 */
	class RandomStringGenerator
	{
		/**
		 * Generates a random string of the specified length using the specified character set.
		 * @param string $valid_chars The character set to use.
		 * @param int $length The length of the string to generate.
		 * @return string A random string.
		 */
		public static function Generate($valid_chars, $length)
		{
			// start with an empty random string
			$random_string = "";

			// count the number of chars in the valid chars string so we know how many choices we have
			$num_valid_chars = strlen($valid_chars);

			// repeat the steps until we've created a string of the right length
			for ($i = 0; $i < $length; $i++)
			{
				// pick a random number from 1 up to the number of valid chars
				$random_pick = mt_rand(1, $num_valid_chars);

				// take the random character out of the string of valid chars
				// subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
				$random_char = $valid_chars[$random_pick-1];

				// add the randomly-chosen char onto the end of our string so far
				$random_string .= $random_char;
			}

			// return our finished random string
			return $random_string;
		}
	}
?>