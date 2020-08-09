
<!DOCTYPE html>
<html>
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta charset="utf-8">
      <link rel="stylesheet" href="statistics4.css">
    </head>
    <body>
      <div class = "container">
        <div class="title">
          <h1> CASH4LIFE </h1>
        </div>

        <img src="Cash4Life.png" />

        <div class="menu">
            <ul>
              <li><a href = "index.php?page=1">Results</a></li>
              <li><a href = "statistics.php">Statistics</a></li>
              <li> <a href="#">Other games</a> </li>
            </ul>
        </div>


      <?php
      $pageNr = 1;
      if(isset($_GET["page"]))
        $pageNr = $_GET["page"];

			require_once('connectDb.php');

			$query = "SELECT id, date, 1stNr, 2ndNr, 3rdNr, 4thNr, 5thNr, luckySum FROM cash4lifetable
			WHERE id <= (SELECT max(id) FROM cash4lifetable) - 20 * ($pageNr - 1) ORDER BY date DESC LIMIT 20";

			$response = @mysqli_query($dbc, $query);

		   	if($response) {

				echo '<table align="center" cellspacing="15" cellpading="8">
        <tr>
          <th colspan = "7"> RESULTS </th>
        </tr>
				<tr>
					<td align="center"><b>DRAW DATE</b></td>
					<td align="center" colspan="5"><b>WINNING NUMBER</b></td>
					<td align="center"><b>LUCKY SUM</b></td>
				</tr>';

				while($row = mysqli_fetch_array($response)){
					echo '<tr>
						<td align="center">'.$row['date'].'</td>
						<td align="center">'.$row['1stNr'].'</td>
						<td align="center">'.$row['2ndNr'].'</td>
						<td align="center">'.$row['3rdNr'].'</td>
						<td align="center">'.$row['4thNr'].'</td>
						<td align="center">'.$row['5thNr'].'</td>
						<td align="center">'.$row['luckySum'].'</td>';
					echo '</tr>';
				}

				echo '</table>';
			} else {
				echo "Failed to find database table<br>";
				echo mysqli_error($dbc);
			}

				mysqli_close($dbc);

        echo '<ul>';
          for($i = 1; $i <= 27; $i++) {
              if($pageNr == $i) {
                echo '<a class="selected" href="?page='.$i.'">'.$i.'</a>';
              }
              else {
                  echo '<a href="?page='.$i.'">'.$i.'</a>';
              }

            }
        echo '</ul>';

        ?>
    </div>
    </body>
</html>
