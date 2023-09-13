<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); 

$stmt = $conn->query("SELECT tags FROM photos ORDER BY tags ASC");
$stmt->execute();
$tag_list = [];
foreach($stmt as $result) $tag_list = array_merge($tag_list, explode(" ", $result['tags']));
$tag_list = array_slice(array_count_values($tag_list), 0, 150);
?>
	<h1>Tags.</h1>

	<table>
		<tr>
			<td id="Hint"> 

							
				<h4>What are tags?</h4>
				
				<p>You can give your photos a "tag" or category. You can assign as many tags as you wish to each photo.</p>				
				
				<p>(By the way, we automatically remove <i>to</i>, <i>the</i>, <i>in</i>, <i>at</i> and <i>my</i> from this list.)</p>				

				<img src="/images/spaceball.gif" alt="spacer image" width="160" height="1"> 
			</td>
			<td id="GoodStuff">
							<p>Here are the 150 most popular tags. The bigger the link, the more popular the tag.</p>
							<p style="padding: 20px; border: solid 1px #eee; background: #f5f5f5;">
							<?php
							foreach($tag_list as $tag => $frequency	) {
								if($frequency <= 11) { 
									$frequency = $frequency + 10;
								}
								if($frequency >= 36) {
									$frequency = 36;
								}
								echo "&nbsp;<a href=\"/photos/tags/" . htmlspecialchars($tag) . "/\" style=\"font-size: " . $frequency . "px;\" class=\"PopularTag\">". htmlspecialchars($tag) . "</a>&nbsp;
								";
							} ?>
							
							</p>
							<p style="margin-top: 20px;">(You can <a href="/photos/alltags/">see the list of all the tags here</a>.)</p>


							<form action="/photos/tags/" method="get">
							<h3>Search for a tag</h3>
							
							
							<input type="text" name="q" size="17" value="">
							<input type="submit" value="SEARCH" class="Butt">
							</form>


			</td>
		</tr>
	</table>

<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>
