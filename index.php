<?
	require $_SERVER['DOCUMENT_ROOT']."/secure/core.php";
?>	

<html>
	<head>
		<title>Yahoo API Test</title>
		
		<!-- jQuery and Ajax -->
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="design/js/ajax.js"></script>
		
		<!-- CSS -->
		<link rel="stylesheet" href="design/css/style.css" type="text/css"/>
	</head>
	
	<body class="webkit chrome mac">
    		<div id="wrapper">
			<div class="header">
  				<a href="#">Yahoo API</a>
			</div>
    			<div id="ajaxResponse"></div>
    			<div id="content">
				<form method="POST" action="#" id="form"> 
  					<input type="text" id="username" placeholder="Enter Yahoo username!" />
  					<input type="password" id="password" placeholder="Enter Yahoo password!" />
  					<input type="submit" value="Submit" class="button blue" id="submit" /><br />
				</form>	
				
				<div id="contacts">
  					<table class="gridtable">
  						<thead>
  							<tr>
  								<th>Email</th>
  								<th>Status</th>
  								<th>Action</th>
  							</tr>
  						</thead>
  						<tbody id="contacts-ajax">
  							<tr>
  								<td colspan="3">Your contact list will appear here after login!</td>
  							</tr>
  						</tbody>
  					</table>
  				</div>
				
  				<div id="results">
  					<textarea id="msg" rows="20" cols="50"></textarea>
    					<textarea id="robot" rows="20" cols="50" disabled></textarea>
    					<div id="start">
    						<input type="hidden" id="user" value="">
    						<button class="button blue" id="submitChat">Start Generation</button>
    					</div>
  				</div>

    			</div>
   		</div>
  	</body>
</html>