<?php 


include ('config.php');

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Saving Coordinent State with jQuery</title>

<link rel="stylesheet" href="style.css" type="text/css" />
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.json-2.2.min.js"></script>

</head>

<body>

<div id="glassbox">
<?php 
		
		$get_coords = mysqli_query($link, "SELECT * FROM coords");
		while($row = mysqli_fetch_array($get_coords)) {
			$x = $row['x_pos'];
			$y = $row['y_pos'];
			
			echo '<div id="element" style="left:'.$x.'px; top:'.$y.'px;"><img src="nettuts.jpg" alt="Nettuts+" />Move the Box<p></p></div>';
		}	
		
		
?>
</div>
<div id="respond"></div>



</body>
<script type="text/javascript">
	$(document).ready(function() {
		$("#element").draggable({ 
				containment: '#glassbox', 
				scroll: false
		 }).mousemove(function(){
						var coord = $(this).position();
						$("p:last").text( "left: " + coord.left + ", top: " + coord.top );
		 }).mouseup(function(){ 
				var coords=[];
				var coord = $(this).position();
				var item={ coordTop:  coord.left, coordLeft: coord.top  };
			   	coords.push(item);
				var order = { coords: coords };
				$.post('updatecoords.php', 'data='+$.toJSON(order), function(response){
						if(response == "success")
							$("#respond").html('<div class="success">X and Y Coordinates Saved!</div>').hide().fadeIn(1000);
							setTimeout(function(){ $('#respond').fadeOut(1000); }, 2000);
						});	
				});
						
		});
</script>

</html>