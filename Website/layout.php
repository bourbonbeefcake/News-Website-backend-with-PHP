
<!--
*
* layout.php
*
* Layout dynamically builds the website depending on the input it has received on $content and $title variables.
*
* @author     Triantafyllidis Antonios
* @copyright  2017 Triantafyllidis Antonios
*
-->
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="styles.css"/>
			<?php echo '<title>' . $title . '</title>'; ?>
			<form id="search" action="search.php" method="POST"><input type="text" name="search_box" placeholder="Search for Articles..."/></form>
	</head>
	<body>
		<header>
			<section>
				<h1>News Website</h1>
			</section>
		</header>

		<?php
		require 'navBar.php';
		?>

		<img src="images/banners/randombanner.php" />
		<main>
			<?php
			//Require the sidebar if a user is logged in
			if(isset($_SESSION['loggedin'])){
					require 'sideBar.php';
			}
 			?>

			<?php echo $content; ?>

		</main>

		<footer>
			&copy; Triantafyllidis Antonios 2017
		</footer>

	</body>
</html>
