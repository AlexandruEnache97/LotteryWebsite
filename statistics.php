<!DOCTYPE html>
<html lang="en" dir="ltr">
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
    	//start connection
    	require_once('connectDb.php');

      //get today date
      $today = substr(date(DATE_ATOM), 0, 10);

      //dynamic array with index, nr of drawns, last date of drawn
      $data = Array();
      for ($i = 0; $i < 60;){
          array_push($data,
              array(++$i, 0, "2015-04-02")
          );
      }

      //statistics for nr of games
      if(isset($_POST["numberOfDays"])) {
        $games = $_POST["numberOfDays"];
      }
      else {
        $games = 100;
      }
      ?>

      <div class = "selectNr">
        <p> Last days of draws: </p>
        <form class="selectDays" action="statistics.php" method="post">
          <input type="radio" id="50" name="numberOfDays" value="50" <?php echo ($games == 50) ? 'checked>' : '' ?>
          <label for="male">50</label><br>

          <input type="radio" id="100" name="numberOfDays" value="100" <?php echo ($games == 100) ? 'checked>' : '' ?>
          <label for="male">100</label><br>

          <input type="radio" id="200" name="numberOfDays" value="200" <?php echo ($games == 200) ? 'checked>' : '' ?>
          <label for="male">200</label><br>

          <input type="radio" id="400" name="numberOfDays" value="400" <?php echo ($games == 400) ? 'checked>' : '' ?>
          <label for="male">400</label><br>

          <input type="submit"><br>
        </form>

        <br>
      </div>

      <div class="selectDate">
        <p> Select period of draw days: </p>
        <form class="insertDate" action="statistics.php" method="post">
          <input type="date" id="start" name="trip-start" value= <?php echo isset($_POST["trip-start"]) ? $_POST["trip-start"] : date('Y-m-d', strtotime('-'.$games.' days')) ?> min="2018-02-14" max= <?php $today ?> >
          <p class="text"> - </p>
          <input type="date" id="end" name="trip-end" value= <?php echo isset($_POST["trip-end"]) ? $_POST["trip-end"] : $today ?> min="2018-02-14" max= <?php $today ?> >

          <input type="submit"><br>
        </form>
      </div>
      <br>

      <?php
      //get data from database
      if(isset($_POST["trip-start"])) {
          $startDate = $_POST["trip-start"];
          $endDate = $_POST["trip-end"];

          if ($stmt = $dbc->prepare("SELECT date, 1stNr, 2ndNr, 3rdNr, 4thNr, 5thNr, luckySum luckySum FROM cash4lifetable WHERE date between ? and ? ORDER BY date DESC")) {
              $stmt->bind_param("ss", $startDate, $endDate);
              $stmt->execute();
              $response = $stmt->get_result();
          }

      } else {
      	$query = "SELECT date, 1stNr, 2ndNr, 3rdNr, 4thNr, 5thNr, luckySum FROM cash4lifetable ORDER BY date DESC LIMIT $games";
        $response = @mysqli_query($dbc, $query);
      }

      //save nr of drawns and last date of drawn for every number
    	if($response) {

        $games = mysqli_num_rows($response);

        echo '<br><table align="center" cellspacing="15" cellpading="8">
          <tr>
            <th colspan = "5" size = "36">FREQUENCY TABLE: '. $games . ' draws</th>
          </tr>
          <tr>
            <th colspan = "5" >'. (isset($_POST["trip-start"]) ?  $startDate : date('Y-m-d', strtotime('-'.$games.' days'))). '&#160;&#160;&#160;  - &#160;&#160;&#160;' . (isset($_POST["trip-end"]) ? $endDate : $today).  '</th>
          </tr>
          <tr>
            <td><b>NUMBER</b></td>
            <td><b>DRAW TIMES</b></td>
            <td><b>FREQUENCY</b></td>
            <td><b>DAYS AGO</b></td>
            <td><b>LAST DRAW</b></td>
          </tr>';


    		while($row = mysqli_fetch_array($response)){

    			$data[$row['1stNr']-1][1]++;
    			$data[$row['2ndNr']-1][1]++;
    			$data[$row['3rdNr']-1][1]++;
    			$data[$row['4thNr']-1][1]++;
    			$data[$row['5thNr']-1][1]++;


          if($row['date'] > $data[$row['1stNr']-1][2]) {
             $data[$row['1stNr']-1][2] = $row['date'];
          }

          if($row['date'] > $data[$row['2ndNr']-1][2]) {
             $data[$row['2ndNr']-1][2] = $row['date'];
          }

          if($row['date'] > $data[$row['3rdNr']-1][2]) {
             $data[$row['3rdNr']-1][2] = $row['date'];
          }

          if($row['date'] > $data[$row['4thNr']-1][2]) {
             $data[$row['4thNr']-1][2] = $row['date'];
          }

          if($row['date'] > $data[$row['5thNr']-1][2]) {
             $data[$row['5thNr']-1][2] = $row['date'];
          }
    		}
    	}

      //copy data in another array
      $orderedData = Array();
      $orderedData = $data;

      //order data by nr of drawns
      for($i = 0; $i < 59; $i++) {
        for($j = $i+1; $j < 60; $j++) {
          if($orderedData[$j][1] > $orderedData[$i][1]) {

            $aux = $orderedData[$i];
            $orderedData[$i] = $orderedData[$j];
            $orderedData[$j] = $aux;

          }
        }
      }

      //put the data in html table
    	foreach($orderedData as $nr) {

        $now = new DateTime($today);
        $then = new DateTime($nr[2]);

        $diff2 = $now->diff($then);
        $diff = $diff2->m * 30 + $diff2->d;

        if($nr[1] == 0) {
          $diff = 0;
          $nr[2] = 'none';
        }

          echo '<tr>
            <td  class = "numberCircle">'.$nr[0].'</td>
            <td>'.$nr[1].'</td>
            <td>'. number_format($nr[1] / $games * 100, 2, '.', '') .'%</td>
            <td>'.$diff .'</td>
            <td>'.$nr[2].'</td>';
          echo '</tr>';
    	}
        echo '</table>';
      ?>
    </div>
  </body>
</html>
