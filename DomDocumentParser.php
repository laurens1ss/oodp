<?php
class DomDocumentParser { // Defines the DomDocumentParser class

	private $doc; // Private variable to hold the DOM document

	public function __construct($url) {
		// Constructor method to initialize the DOM document with the HTML content from the given URL

		// Defines options for the HTTP request, including a custom User-Agent header
		$options = array(
			'http' => array('method' => "GET", 'header' => "User-Agent: doodleBot/0.1\n")
		);
		// Creates a stream context with the defined options
		$context = stream_context_create($options);

		$this->doc = new DomDocument(); // Creates a new instance of the DomDocument class
		// Loads the HTML content from the URL into the DOM document, suppressing any warnings with the @ operator
		@$this->doc->loadHTML(file_get_contents($url, false, $context));
	}

	public function getLinks() {
		// Retrieves all <a> elements (links) from the DOM document
		return $this->doc->getElementsByTagName("a");
	}

	public function getTitleTags() {
		// Retrieves all <title> elements from the DOM document
		return $this->doc->getElementsByTagName("title");
	}

	public function getMetaTags() {
		// Retrieves all <meta> elements from the DOM document
		return $this->doc->getElementsByTagName("meta");
	}

}
?>
