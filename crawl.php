<?php
include("config.php"); // Includes the configuration file that sets up the database connection
include("classes/DomDocumentParser.php"); // Includes the DomDocumentParser class for parsing HTML documents

$alreadyCrawled = array(); // Array to store URLs that have already been crawled
$crawling = array(); // Array to store URLs that are currently being crawled

function linkExists($url) {
	global $con; // Uses the global $con variable for the database connection

	$query = $con->prepare("SELECT * FROM sites WHERE url = :url"); // Prepares a SQL statement to check if a URL already exists in the database
	$query->bindParam(":url", $url); // Binds the URL parameter to the SQL query
	$query->execute(); // Executes the SQL query

	return $query->rowCount() != 0; // Returns true if the URL exists (i.e., row count is not zero), false otherwise
}

function insertLink($url, $title, $description, $keywords) {
	global $con; // Uses the global $con variable for the database connection

	$query = $con->prepare("INSERT INTO sites(url, title, description, keywords)
							VALUES(:url, :title, :description, :keywords)"); // Prepares a SQL statement to insert a new URL and its details into the database

	$query->bindParam(":url", $url); // Binds the URL parameter to the SQL query
	$query->bindParam(":title", $title); // Binds the title parameter to the SQL query
	$query->bindParam(":description", $description); // Binds the description parameter to the SQL query
	$query->bindParam(":keywords", $keywords); // Binds the keywords parameter to the SQL query

	return $query->execute(); // Executes the SQL query and returns true if the insertion was successful, false otherwise
}

function createLink($src, $url) {
	$scheme = parse_url($url)["scheme"]; // Extracts the scheme (http or https) from the URL
	$host = parse_url($url)["host"]; // Extracts the host (domain name) from the URL
	
	if(substr($src, 0, 2) == "//") { // Checks if the source URL starts with "//" (protocol-relative URL)
		$src =  $scheme . ":" . $src; // Prepend the scheme to the source URL
	}
	else if(substr($src, 0, 1) == "/") { // Checks if the source URL is an absolute path
		$src = $scheme . "://" . $host . $src; // Prepend the scheme and host to the source URL
	}
	else if(substr($src, 0, 2) == "./") { // Checks if the source URL is a relative path starting with "./"
		$src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1); // Convert to absolute URL using the current directory
	}
	else if(substr($src, 0, 3) == "../") { // Checks if the source URL is a relative path starting with "../"
		$src = $scheme . "://" . $host . "/" . $src; // Convert to absolute URL using the parent directory
	}
	else if(substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http") { // Checks if the source URL is a relative path
		$src = $scheme . "://" . $host . "/" . $src; // Convert to absolute URL using the host
	}

	return $src; // Returns the fully qualified URL
}

function getDetails($url) {
	$parser = new DomDocumentParser($url); // Creates a new DomDocumentParser object to parse the given URL

	$titleArray = $parser->getTitleTags(); // Gets the title tags from the parsed document

	if(sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) { // Checks if the title tag is empty or not found
		return; // If no title is found, exits the function
	}

	$title = $titleArray->item(0)->nodeValue; // Retrieves the title tag's text content
	$title = str_replace("\n", "", $title); // Removes newline characters from the title

	if($title == "") { // Checks if the title is empty after cleanup
		return; // If the title is empty, exits the function
	}

	$description = ""; // Initializes the description variable
	$keywords = ""; // Initializes the keywords variable

	$metasArray = $parser->getMetatags(); // Gets all meta tags from the parsed document

	foreach($metasArray as $meta) { // Iterates over each meta tag
		if($meta->getAttribute("name") == "description") { // Checks if the meta tag is for the description
			$description = $meta->getAttribute("content"); // Retrieves the content attribute of the description meta tag
		}

		if($meta->getAttribute("name") == "keywords") { // Checks if the meta tag is for keywords
			$keywords = $meta->getAttribute("content"); // Retrieves the content attribute of the keywords meta tag
		}
	}

	$description = str_replace("\n", "", $description); // Removes newline characters from the description
	$keywords = str_replace("\n", "", $keywords); // Removes newline characters from the keywords

	if(linkExists($url)) { // Checks if the URL already exists in the database
		echo "$url already exists<br>"; // Outputs a message if the URL is already in the database
	}
	else if(insertLink($url, $title, $description, $keywords)) { // Inserts the URL and its details into the database
		echo "SUCCESS: $url<br>"; // Outputs a success message if insertion was successful
	}
	else {
		echo "ERROR: Failed to insert $url<br>"; // Outputs an error message if insertion failed
	}
}

function followLinks($url) {
	global $alreadyCrawled; // Uses the global array to track already crawled URLs
	global $crawling; // Uses the global array to track currently crawling URLs

	$parser = new DomDocumentParser($url); // Creates a new DomDocumentParser object to parse the given URL

	$linkList = $parser->getLinks(); // Gets all links from the parsed document

	foreach($linkList as $link) { // Iterates over each link
		$href = $link->getAttribute("href"); // Gets the href attribute of the link

		if(strpos($href, "#") !== false) { // Ignores anchor links (URLs containing '#')
			continue;
		}
		else if(substr($href, 0, 11) == "javascript:") { // Ignores JavaScript links
			continue;
		}

		$href = createLink($href, $url); // Converts relative URLs to absolute URLs

		if(!in_array($href, $alreadyCrawled)) { // Checks if the URL has not been crawled already
			$alreadyCrawled[] = $href; // Adds the URL to the alreadyCrawled array
			$crawling[] = $href; // Adds the URL to the crawling array

			getDetails($href); // Retrieves and stores details of the URL
		}
		else return; // Stops if the URL is already in the array
	}

	array_shift($crawling); // Removes the first element from the crawling array

	foreach($crawling as $site) { // Iterates over each site in the crawling array
		followLinks($site); // Recursively follows links from the current site
	}
}

$startUrl = "https://www.instagram.com/qd.spamt/"; // Sets the starting URL for crawling
followLinks($startUrl); // Starts the crawling process from the start URL
?>
