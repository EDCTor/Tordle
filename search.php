<?php

define("WILDCARD", "_");

function wordContains($word, $characters) 
{
	$found = false;

	if (isset($characters[0]))
	{
		$cArry = str_split($characters);
		foreach($cArry as $c)
		{
			if (strpos($word, $c) !== false)
			{
				$found = true;
				break;
			}
		}
	}

	return $found;
}

// get the input search strings
$c1 = $_POST['c1'];
$c2 = $_POST['c2'];
$c3 = $_POST['c3'];
$c4 = $_POST['c4'];
$c5 = $_POST['c5'];
$contains = $_POST['contains'];
$doesNotContain = $_POST['doesNotContain'];

#region convert the input to lowercase or the wildcard
if (isset($c1[0]))
{
    $c1 = trim(strtolower($c1));
}
else
{
    $c1 = WILDCARD;
}
if (isset($c2[0]))
{
    $c2 = trim(strtolower($c2));
}
else
{
    $c2 = WILDCARD;
}
if (isset($c3[0]))
{
    $c3 = trim(strtolower($c3));
}
else
{
    $c3 = WILDCARD;
}
if (isset($c4[0]))
{
    $c4 = trim(strtolower($c4));
}
else
{
    $c4 = WILDCARD;
}
if (isset($c5[0]))
{
    $c5 = trim(strtolower($c5));
}
else
{
    $c5 = WILDCARD;
}
if (isset($contains[0]))
{
    $contains = trim(strtolower($contains));
}
if (isset($doesNotContain[0]))
{
    $doesNotContain = trim(strtolower($doesNotContain));
}
#endregion

#region must have something to search on...
if (
    !isset($c1[0]) &&
    !isset($c2[0]) &&
    !isset($c3[0]) &&
    !isset($c4[0]) &&
    !isset($c5[0]) &&
    !isset($contains[0]) &&
    !isset($doesNotContain[0]) 
    )
{
    echo 'Must enter something to search on';
    die;
}

#endregion

// keep track of some statistics
$totalMatches = 0;
$matches = array();

$handle = fopen("dictionary.txt.php", "r");
if ($handle) 
{
	while (($line = fgets($handle)) !== false) 
	{
		// try a series of pattern matches to rule in/out the word
		$keeper = false;

		// ensure the word is lower case since our search is lower case
		// trim off the newline characters
		$line = trim(strtolower($line));

		#region first check is for line length, limit to 5 character words
		if (strlen($line) == 5)
		{
			$keeper = true;
		}
		#endregion

		#region second check is for pattern match on letters
		if ($keeper)
		{
			//echo "five letter word: " . $line . "\n";

			if (
				($c1 == WILDCARD || $c1 == substr($line, 0, 1)) &&
				($c2 == WILDCARD || $c2 == substr($line, 1, 1)) &&
				($c3 == WILDCARD || $c3 == substr($line, 2, 1)) &&
				($c4 == WILDCARD || $c4 == substr($line, 3, 1)) &&
				($c5 == WILDCARD || $c5 == substr($line, 4, 1))
				)
			{
				// wildcard or letter match has occurred
				$keeper = true;
			}
			else
			{
				$keeper = false;
			}
		}
		#endregion

		#region third check: must contain one of these characters
		if ($keeper && isset($contains[0]))
		{
			$keeper = wordContains($line, $contains);
		}
		#endregion

		#region fourth check: does not contain
		if($keeper && isset($doesNotContain[0]))
		{
			if (!wordContains($line, $doesNotContain))
			{
				$keeper = true;
			}
			else
			{
				$keeper = false;
			}
		}
		#endregion

                // if we found a match then add the match to the return array
		if ($keeper)
		{
			$matches[] = $line;
		}
	}

	fclose($handle);
}
else 
{
	// error opening the file.
	echo "could not open the file\n";
}

// filter duplicates out of the return results
$matches = array_unique($matches, SORT_REGULAR);

// sort the return results
sort($matches);

// return all results
//echo "<div class=\"matches\"><ol>";
foreach ($matches as $m) 
{
    $totalMatches = $totalMatches + 1;
    echo "<li>" . $m . "</li>";
} 
//echo "</ol></div>";
//echo "Total Matches: " . $totalMatches . "<br/>";

?>
