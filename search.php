<?php
// Include necessary files and initialize variables
include("config.php"); // Include configuration file (assuming it contains database connection)
include("classes/SiteResultsProvider.php"); // Include class for handling search results

// Check if search term is provided via GET parameter
if(isset($_GET["term"])) {
    $term = $_GET["term"];
} else {
    exit("You must enter a search term"); // Exit script if search term is not provided
}

// Initialize other parameters with default values if not provided via GET
$type = isset($_GET["type"]) ? $_GET["type"] : "sites";
$page = isset($_GET["page"]) ? $_GET["page"] : 1;
$operator = isset($_GET["operator"]) ? strtoupper($_GET["operator"]) : 'AND';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Holla</title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css"> <!-- Link to CSS stylesheet -->
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="headerContent">
                <div class="logoContainer">
                    <a href="index.php">
                        <img src="assets/images/hollaLogo.png"> <!-- Holla logo -->
                    </a>
                </div>
                <div class="searchContainer">
                    <!-- Search form with search term input and operator select -->
                    <form action="search.php" method="GET">
                        <div class="searchBarContainer">
                            <input class="searchBox" type="text" name="term" value="<?php echo htmlspecialchars($term); ?>">
                            <div class="searchButtonGroup">
                                <select name="operator" class="operatorSelect">
                                    <!-- Options for search operators (AND, OR, NOT) -->
                                    <option value="AND" <?php echo ($operator == 'AND') ? 'selected' : ''; ?>>AND</option>
                                    <option value="OR" <?php echo ($operator == 'OR') ? 'selected' : ''; ?>>OR</option>
                                    <option value="NOT" <?php echo ($operator == 'NOT') ? 'selected' : ''; ?>>NOT</option>
                                </select>
                                <button class="searchButton">
                                    <img src="assets/images/icons/search.png"> <!-- Search icon -->
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tabsContainer">
                    <ul class="tabList">
                        <li class="<?php echo $type == 'sites' ? 'active' : '' ?>">
                            <!-- Tab for displaying search results for 'sites' -->
                            <a href='<?php echo "search.php?term=$term&type=sites&operator=$operator"; ?>'>
                                Sites
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mainResultsSection">
            <?php
            // Initialize results provider and set page size
            $resultsProvider = new SiteResultsProvider($con); // Assuming $con is the database connection
            $pageSize = 20; // Number of results per page

            // Retrieve number of results for the given search term and operator
            $numResults = $resultsProvider->getNumResults($term, $operator);
            echo "<p class='resultsCount'>$numResults results found</p>"; // Display total number of results

            // Display HTML for search results based on current page, page size, term, and operator
            echo $resultsProvider->getResultsHtml($page, $pageSize, $term, $operator);
            ?>
        </div>
        <div class="paginationContainer">
            <div class="pageButtons">
                <div class="pageNumberContainer">
                    <img src="assets/images/start.png"> <!-- Pagination start icon -->
                </div>
                <?php
                $pagesToShow = 10; // Number of pagination links to show
                $numPages = ceil($numResults / $pageSize); // Calculate total number of pages
                $pagesLeft = min($pagesToShow, $numPages);

                $currentPage = $page - floor($pagesToShow / 2);

                // Adjust current page number based on pagination logic
                if($currentPage < 1) {
                    $currentPage = 1;
                }

                if($currentPage + $pagesLeft > $numPages + 1) {
                    $currentPage = $numPages + 1 - $pagesLeft;
                }

                // Generate pagination links
                while($pagesLeft != 0 && $currentPage <= $numPages) {
                    if($currentPage == $page) {
                        echo "<div class='pageNumberContainer'>
                                <img src='assets/images/pageSelected.png'> <!-- Selected page indicator -->
                                <span class='pageNumber'>$currentPage</span>
                            </div>";
                    } else {
                        echo "<div class='pageNumberContainer'>
                                <a href='search.php?term=$term&type=$type&page=$currentPage&operator=$operator'>
                                    <img src='assets/images/page.png'> <!-- Pagination page icon -->
                                    <span class='pageNumber'>$currentPage</span>
                                </a>
                            </div>";
                    }

                    $currentPage++;
                    $pagesLeft--;
                }
                ?>
                <div class="pageNumberContainer">
                    <img src='assets/images/end.png'> <!-- Pagination end icon -->
                </div>
            </div>
        </div>
    </div>
</body>
</html>
