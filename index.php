<!DOCTYPE html>
<html>
<head>
	<title>Welcome to Holla</title> <!-- Sets the title of the web page -->

	<meta name="description" content="Search the web for sites and images."> <!-- Meta tag for the page description -->
	<meta name="keywords" content="Search engine, holla, websites"> <!-- Meta tag for keywords related to the page -->
	<meta name="author" content="Reece Kenney"> <!-- Meta tag for the author of the page -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Meta tag to ensure proper rendering on mobile devices -->

	<link rel="stylesheet" type="text/css" href="assets/css/style.css"> <!-- Links to the external CSS file for styling -->

</head>
<body>

	<div class="wrapper indexPage"> <!-- Main wrapper for the page, with a class for styling the index page -->
	

		<div class="mainSection"> <!-- Main section of the page -->

			<div class="logoContainer"> <!-- Container for the site logo -->
				<img src="assets/images/hollaLogo.png" title="Logo of our site" alt="Site logo"> <!-- Site logo with title and alt text for accessibility -->
			</div>

			<div class="searchContainer"> <!-- Container for the search form -->

				<form action="search.php" method="GET"> <!-- Form for submitting search queries, using GET method to append query to URL -->

					<input class="searchBox" type="text" name="term"> <!-- Input field for the search term, styled with the searchBox class -->
					<input class="searchButton" type="submit" value="Search"> <!-- Submit button for the form, styled with the searchButton class -->

				</form>

			</div>

		</div>

	</div>

</body>
</html>
