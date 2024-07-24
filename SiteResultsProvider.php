<?php
class SiteResultsProvider { // Defines the SiteResultsProvider class

	private $con; // Private variable to hold the database connection

	public function __construct($con) {
		$this->con = $con; // Constructor method to initialize the database connection
	}

	public function getNumResults($term) {
		// Prepares a SQL query to count the number of rows that match the search term in the title, url, keywords, or description columns
		$query = $this->con->prepare("SELECT COUNT(*) as total 
										 FROM sites WHERE title LIKE :term 
										 OR url LIKE :term 
										 OR keywords LIKE :term 
										 OR description LIKE :term");

		$searchTerm = "%". $term . "%"; // Adds wildcard characters to the search term for partial matching
		$query->bindParam(":term", $searchTerm); // Binds the search term to the query parameter
		$query->execute(); // Executes the query

		$row = $query->fetch(PDO::FETCH_ASSOC); // Fetches the result as an associative array
		return $row["total"]; // Returns the total number of results
	}

	public function getResultsHtml($page, $pageSize, $term) {
		// Calculates the starting point for the results based on the current page and page size
		$fromLimit = ($page - 1) * $pageSize;

		// Prepares a SQL query to select all columns from the sites table where the search term matches the title, url, keywords, or description
		// Orders the results by clicks in descending order and limits the number of results based on the page size
		$query = $this->con->prepare("SELECT * 
										 FROM sites WHERE title LIKE :term 
										 OR url LIKE :term 
										 OR keywords LIKE :term 
										 OR description LIKE :term
										 ORDER BY clicks DESC
										 LIMIT :fromLimit, :pageSize");

		$searchTerm = "%". $term . "%"; // Adds wildcard characters to the search term for partial matching
		$query->bindParam(":term", $searchTerm); // Binds the search term to the query parameter
		$query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT); // Binds the fromLimit parameter as an integer
		$query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT); // Binds the pageSize parameter as an integer
		$query->execute(); // Executes the query

		$resultsHtml = "<div class='siteResults'>"; // Initializes the HTML string for the results

		// Loops through the query results and constructs HTML for each result
		while($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$id = $row["id"];
			$url = $row["url"];
			$title = $row["title"];
			$description = $row["description"];

			$title = $this->trimField($title, 55); // Trims the title to a specified character limit
			$description = $this->trimField($description, 230); // Trims the description to a specified character limit
			
			// Appends the HTML for each result to the resultsHtml string
			$resultsHtml .= "<div class='resultContainer'>
								<h3 class='title'>
									<a class='result' href='$url'>
										$title
									</a>
								</h3>
								<span class='url'>$url</span>
								<span class='description'>$description</span>
							</div>";
		}

		$resultsHtml .= "</div>"; // Closes the siteResults div

		return $resultsHtml; // Returns the constructed HTML string
	}

	private function trimField($string, $characterLimit) {
		// Checks if the length of the string is greater than the character limit
		$dots = strlen($string) > $characterLimit ? "..." : "";
		// Trims the string to the character limit and appends dots if it was trimmed
		return substr($string, 0, $characterLimit) . $dots;
	}
}
?>
