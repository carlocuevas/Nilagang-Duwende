<?php
	session_start();
	date_default_timezone_set("Asia/Manila");
	$date = date("Y-m-d");
	require 'dbconfig.php';	
if(isset($_POST['notifyme']))
{
   
   do
	    {
			function itexmo($number,$message,$apicode)
		    {
		                 $ch = curl_init();
		                 $itexmo = array('1' => $number, '2' => $message, '3' => $apicode);
		                 curl_setopt($ch, CURLOPT_URL,"https://www.itexmo.com/php_api/api.php");
		                 curl_setopt($ch, CURLOPT_POST, 1);
		                 curl_setopt($ch, CURLOPT_POSTFIELDS, 
		                http_build_query($itexmo));
		                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		                 return curl_exec ($ch);
		                 curl_close ($ch);
		     }

	    	for ($i=0; $i <1 ; $i++) { 
	    	  		$sql= mysql_query("SELECT eoqview.ProductName , round(Eoq_Per_Box) as ABC ,round(Eoq_Per_Piece) as EFG from eoqview inner join tblproductinfo where tblproductinfo.Quantity_Per_box=(SELECT round(Reorder_Point_Per_Box)) and  tblproductinfo.Quantity_Per_Piece=(SELECT round(Reorder_Point_Per_Piece))");
	    	  		
		    		if(mysql_num_rows($sql)!=0)
		    		{

			             $cellnumber2="09068436805";
			             $text="Some products in your inventory needs to be restocked - DwarfPH";
			             $result = itexmo($cellnumber2,$text,"09287923698EA455874");
			             if ($result == ""){
			             echo "iTexMo: No response from server!!! <br>
			             Please check the METHOD used (CURL or CURL-LESS). If you are using CURL then try CURL-LESS and vice versa.  
			             Please <a href=\"https://www.itexmo.com/contactus.php\">CONTACT US</a> for help. "; 
			             }else if ($result == 0){
		         }
		         else
		        {   
		        		 echo "Error Num ". $result . " was encountered!";
		         }
	    		}
	    	}
	    	$date = date("Y-m-d", strtotime("+1 day"));
	    }
	    while($date == date("Y-m-d"));
	    exit();
}
//Invoice and OnFieldDuplicate Update
//DAYA
	$sqld=mysql_query("SELECT MAX(TransactionID) as TransactionID from tblsales");
	$row=mysql_fetch_array($sqld);
//Query For FinishTransactions

	if(isset($_POST['ETOWS']))
	{

		$sql=mysql_query("SELECT *	 from POSview where TransactionID = {$row['TransactionID']} ");
		$sql1 = mysql_query("SELECT SUM((PricePiece*QuantityPiece) + (pricebox*QuantityBox)) as TotalSales from POSview where TransactionID ={$row['TransactionID']}");
		$row1 = mysql_fetch_array($sql1);
	    $sum = $row1['TotalSales'];
		if(mysql_num_rows($sql) != 0)
		{
	?>
							<table class="table table-striped table-bordered table-hover" style="font-size:12px;height:60%;">
								<thead>
									<tr>
									  
								  
									  <th>Product Name</th>
									  <th>Dosage</th>
									  <th >Quantity Piece</th>
									  <th >Quantity Box</th>
									  <th >Price</th>
							<?php
								

							?>		  
									 <!-- <th><input type="checkbox" id="check_all" style="cursor:pointer;"/></th>-->	    
									 

									</tr>
								 </thead>
								 <tbody>
								 
								 <?php
								 while($row=mysql_fetch_array($sql))
									{
										?>

										<tr>
											<td><?php echo $row['ProductName']; ?></td>
											<td><?php echo $row['Dosage'] ?></td>
											<td><?php echo $row['QuantityPiece']; ?></td>
											<td><?php echo $row['QuantityBox']; ?></td>
                                            <td><?php echo (($row['QuantityPiece']*$row['PricePiece']) + ($row['QuantityBox']*$row['PriceBox']));?></td>

										</tr>

										<?php
									}
								 ?>

								 </tbody>
							 </table>

							 <h4 style="float:right">Total Price: <span id="Total"><?php echo $sum ?></span></h4>
							 <br/>
							 <br/>
							 <br/>


	<?php
		}
		else
		{
			echo "No Transaction Found";
		}
		exit();

	}
//Query For addQuantity
	if(isset($_POST['aded']))
		{
			$sql=mysql_query("SELECT * from InventoryView where ProdID = '{$_POST['id']}'");
			$row=mysql_fetch_object($sql);
			header("Content-type: text/x-json");
			echo json_encode($row);
			exit();
		}
//Query For showing the side table
	if(isset($_POST['ShowedVal']))
	{
		$sql=mysql_query("SELECT * from POSview where TransactionID = {$row['TransactionID']}");
		if(mysql_num_rows($sql) != 0)
		{
		?>
	    <table class="table table-hover">	
			<tbody style="color:white;">
		<?php

		while($row=mysql_fetch_array($sql))
		{
		?>
			<tr>
				<td><?php echo $row['ProductName']; ?></td>
				<td><?php echo $row['QuantityPiece']; ?></td>
				<td><?php echo $row['QuantityBox']; ?></td>
				<td><?php echo (($row['QuantityPiece']*$row['PricePiece']) + ($row['QuantityBox']*$row['PriceBox']));?></td>
				<td><button  class= "btn btn-danger remove" idz="<?php echo $row['ProductID']; ?>"idy="<?php echo $row['TransactionID']; ?>"idx="<?php echo $row['id'] ?>" style ="float:right;border:none;background-color:#e74c3c;transition:all .3s;margin-top:0px;"><i class = "glyphicon glyphicon-remove"></i></button></td>
				
			</tr>
			
		
		<?php
		
		}
		?> 	</tbody>
			</table>
		
		<?php
		}
		else
		{
			?> 

			<center><h1>No Order</h1></center>

			<?php
			
		}
		exit();
	}
//Query For Remove Product or Void
	if(isset($_POST['remover']))
		{
            mysql_query("UPDATE tblproductinfo set Quantity_Per_box = Quantity_Per_box + (Select QuantityBox from tbltrans where ProductID = '{$_POST['ProdID']}') , Quantity_Per_Piece = Quantity_Per_Piece + (Select QuantityPiece from tbltrans where ProductID = '{$_POST['ProdID']}') where ProdID = '{$_POST['ProdID']}' ");
			mysql_query("DELETE from tblTrans where ProductID = '{$_POST['ProdID']}' and TransactionID = '{$_POST['TransID']}'");
        $EOQquery=mysql_query("SELECT sum(QuantityBox) as QuantityBox , sum(QuantityPiece) as QuantityPiece from tbltrans where DateVoid Between '2016-01-01' and '2017-01-01' and ProductID='{$_POST['ProdID']}'");
				$others=mysql_query("SELECT Total_Quantity_Per_Box from tblproductinfo where ProdID='{$_POST['ProdID']}'");
				$row4=mysql_fetch_array($EOQquery);
				$row5=mysql_fetch_array($others);
				$box=$row4['QuantityBox']*$row5['Total_Quantity_Per_Box'];

				//EOQ
				     $Co=10;
				 	 $Ch=2;
					 $formula1=(2*$row4['QuantityPiece']*$Co)/$Ch;
					 $eoqpiece=sqrt($formula1);

					 $Co1=10;
				 	 $Ch1=2;
					 $formula2=(2*$box*$Co1)/$Ch1;
					 $eoqbox=sqrt($formula2);

				//REORDER
					   $REORDERbox=($box/365)*3;
				 	   $REORDERpiece = ($row4['QuantityPiece']/365)*3; 
				

				mysql_query("UPDATE tbleoq set Reorder_Point_Per_Box=$REORDERbox , Reorder_Point_Per_Piece = $REORDERpiece , Eoq_Per_Box = $eoqbox , Eoq_Per_Piece = $eoqpiece where ProdID = '{$_POST['ProdID']}'");
			exit();
		}
//Query for new trans
	if(isset($_POST['trans']))
	{	
		$date = date('Y-m-d');
		$sql1=mysql_query("INSERT INTO tblsales values(DEFAULT,'{$_POST['TransactionID1']}','".$date."','{$_POST['Total']}','{$_POST['Cash']}','{$_POST['Changed']}','{$_POST['Discount']}','{$_POST['SeniorID']}')");
		exit();
	}
//Query For ADD
	if(isset($_POST['Addsz']))
	{	
        $sql1=mysql_query("UPDATE tblproductinfo SET Quantity_Per_Piece = Quantity_Per_Piece - '{$_POST['QuantityP']}',Quantity_Per_box = Quantity_Per_box - '{$_POST['QuantityB']}' WHERE ProdId = '{$_POST['ProdId']}'");
		$sql=mysql_query("SELECT * from tblTrans where ProductID='{$_POST['ProdId']}' and TransactionID={$row['TransactionID']}");
			$date = date('Y-m-d');
			if(mysql_num_rows($sql) != NULL)
			{
				$rowszxc=mysql_fetch_array($sql);	
				mysql_query("UPDATE tblTrans SET ProductID='{$_POST['ProdId']}',QuantityPiece=('".$rowszxc['QuantityPiece']."'+'{$_POST['QuantityP']}'),QuantityBox=('".$rowszxc['QuantityBox']."'+'{$_POST['QuantityB']}'),DateVoid='".$date."',PricePiece=PricePiece  ,PriceBox=PriceBox WHERE ProductID='{$_POST['ProdId']}' and TransactionID={$row['TransactionID']} ");
			}

			else
			{
				$sql3=mysql_query("INSERT INTO tblTrans values({$row['TransactionID']},'{$_POST['ProdId']}','{$_POST['QuantityP']}','{$_POST['QuantityB']}','".$date."',(select price_per_piece from inventoryview where prodid = '{$_POST['ProdId']}' ),(select price_per_box from inventoryview where ProdID = '{$_POST['ProdId']}'))");
			}
				$EOQquery=mysql_query("SELECT sum(QuantityBox) as QuantityBox , sum(QuantityPiece) as QuantityPiece from tbltrans where DateVoid Between '2016-01-01' and '2017-01-01' and ProductID='{$_POST['ProdId']}'");
				$others=mysql_query("SELECT Total_Quantity_Per_Box from tblproductinfo where ProdID='{$_POST['ProdId']}'");
				$row4=mysql_fetch_array($EOQquery);
				$row5=mysql_fetch_array($others);
				$box=$row4['QuantityBox']*$row5['Total_Quantity_Per_Box'];

				//EOQ
				     $Co=10;
				 	 $Ch=2;
					 $formula1=(2*$row4['QuantityPiece']*$Co)/$Ch;
					 $eoqpiece=sqrt($formula1);

					 $Co1=10;
				 	 $Ch1=2;
					 $formula2=(2*$box*$Co1)/$Ch1;
					 $eoqbox=sqrt($formula2);

				//REORDER
					   $REORDERbox=($box/365)*3;
				 	   $REORDERpiece = ($row4['QuantityPiece']/365)*3; 
				

				mysql_query("UPDATE tbleoq set Reorder_Point_Per_Box=$REORDERbox , Reorder_Point_Per_Piece = $REORDERpiece , Eoq_Per_Box = $eoqbox , Eoq_Per_Piece = $eoqpiece where ProdID = '{$_POST['ProdId']}'");


		echo 0;
		exit();
	}
//Cancel Trans
	if(isset($_POST['DELE']))
		{
           $query=mysql_query("SELECT * from tbltrans where TransactionID = {$row['TransactionID']}	");
           while($row=mysql_fetch_array($query))
           {
           	mysql_query("UPDATE tblproductinfo set Quantity_Per_box = Quantity_Per_box + {$row['QuantityBox']} , Quantity_Per_Piece = Quantity_Per_Piece + {$row['QuantityPiece']}");

           	mysql_query("DELETE from tbltrans where TransactionID = {$row['TransactionID']}");
           }
			exit();
    }
//Query For Logout
	if(isset($_POST['Logout']))
	{
		session_destroy();
		header("Location:index.php");
	}
//Query For showing the Datas 
	if(isset($_POST['showtable']))
	{
		$starter=5;
		$formula = (intval($_POST['N'] - 1 )*$starter);
		$sql = mysql_query("SELECT * from InventoryView where Quantity_Per_Piece != 0 or Quantity_Per_box != 0 order by ProdID  asc limit $formula,$starter");
		?>
					<table class="table table-hover table-striped" >
						<thead>
							<th>Product Name</th>
							<th>Dosage</th>
							<th>Price Per Box</th>
							<th>Price Per Piece</th>
							<th>Add To Cart</th>
							
							
						</thead>
						<tbody>
							


		<?php			
					while($row=mysql_fetch_array($sql))
				    {	

				    ?>
				    <tr class="itozxc">
			              <td><?php echo $row['ProductName'];?></td>
					      <td><?php echo $row['Dosage'];?></td>
					      <td><?php echo $row['Price_Per_Box'];?></td> 
						  <td><?php echo $row['Price_Per_Piece'];?></td>
						  <td><input type="submit"data-toggle="modal"data-target=".AddQuantity"value="Add to Cart" ida="<?php echo $row['ProdID']; ?>" class="btn btn-danger Add" style="border-radius:2px;margin-top:0px;width:50%;;margin-left:20px;float:left;transition:all 0.3s;"/></td>
		            </tr>      
		            <?php
    		        }
    		        ?>
						</tbody>
					</table>

    		        <?php

		exit();
	}	
//Query For Paging by default
	if(isset($_POST['showpagez']))
	{	

					?>
				<button id="Previous" class="btn btn-danger" style="margin-top:0px;border-radius:0px;width:100px;"onclick="showzxc1()"><i class="glyphicon glyphicon-menu-left"></i> Previous</button>
					<?php
				    $sqlC =mysql_query("SELECT Count(*) from InventoryView where ExpirationDate >= 20160118 order by ProdId");
					$counted = mysql_result($sqlC,0)/5;
					$rounded = ceil($counted);
					 for ($i=1; $i <= $rounded ; $i++) 
						{				
						?>
								<button class="btn btn-danger pagenum" id="Pagesss" style="margin-top:0px;border-radius:0px;" value="<?php echo $i; ?>" idz="<?php echo $i; ?>"><?php echo $i; ?></button>
						<?php
						}
					?>

				<button id="Nextcx" class="btn btn-danger" style="margin-top:0px;border-radius:0px;width:100px;"onclick="showzxc()">Next <i class="glyphicon glyphicon-menu-right"></i></button>				
					<?php
				exit();	
	}
//Query For SearchValue
	if(isset($_POST['SearchValue']))
	{
		$starter=5;
		$formula = (intval($_POST['N'] - 1 )*$starter);	
		$sql1 = mysql_query("SELECT * from InventoryView where ProductName Like '%{$_POST['texttoseek']}%' or Dosage Like '%{$_POST['texttoseek']}%' or Price_Per_Box Like '%{$_POST['texttoseek']}%' or Price_Per_Piece Like '%{$_POST['texttoseek']}%' or Quantity_Per_Piece Like '%{$_POST['texttoseek']}%' and Quantity_Per_Piece != 0 or Quantity_Per_box != 0 LIMIT $formula,$starter ");
		if(mysql_num_rows($sql1) != 0)
		{
			?>
					<table class="table table-hover table-striped" >
						<thead>
							<th>Product Name</th>
							<th>Dosage</th>
							<th>Price Per Box</th>
							<th>Price Per Piece</th>
							<th>Add To Cart</th>
							
							
						</thead>
						<tbody>
			<?php				
			while($row=mysql_fetch_array($sql1))
				    {	
			    ?>
				    <tr class="itozxc">
			              <td><?php echo $row['ProductName'];?></td>
					      <td><?php echo $row['Dosage'];?></td>
					      <td><?php echo $row['Price_Per_Box'];?></td> 
						  <td><?php echo $row['Price_Per_Piece'];?></td>
						  <td><input type="submit"data-toggle="modal"data-target=".AddQuantity"value="Add to Cart" ida="<?php echo $row['ProdID']; ?>" class="btn btn-danger Add" style="border-radius:2px;margin-top:0px;width:50%;;margin-left:20px;float:left;transition:all 0.3s;"/></td>
		            </tr>  
		
		            <?php

					}
			?>
					</tbody>
				</table>
						<div class="btn-group" id="showpagezs"style="">
						</div>
			<?php
		}
		else
		{
			echo '<h1>No Record Found </h1>';
		}
		exit();
	}	
//Query for total
	if(isset($_POST['Totalsz']))
	{
						$sqlE = mysql_query("SELECT sum((PricePiece*QuantityPiece) + (pricebox*QuantityBox)) as TotalSales from POSVIEW where TransactionID = {$row['TransactionID']}");
						$rowE = mysql_fetch_array($sqlE);
					    $sum = $rowE['TotalSales'];
					    echo $sum;
						 exit();
	}
//Query for total
	if(isset($_POST['Totalszz']))
	{
						$sqlE = mysql_query("SELECT sum((PricePiece*QuantityPiece) + (pricebox*QuantityBox)) as TotalSales from POSVIEW where TransactionID = {$row['TransactionID']}");
						$rowE = mysql_fetch_array($sqlE);
					    $sum = $rowE['TotalSales'];
					    echo $sum;
						exit();
	}
//Experiment
						$sqlCA = mysql_query("SELECT COUNT(*) from inventoryview where Quantity_Per_Piece != 0 or Quantity_Per_box != 0 order by ProdID");
						$counted2 = mysql_result($sqlCA,0)/5;
						$rounded2 = ceil($counted2);
						$BA=$rounded2;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Dwarf's Pharmacy Inventory</title>
	<meta charset="UTF-8">
	<link rel="icon" href="images/icon.png" type="image/png" sizes="16x16">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="keywords" content="Inventory">
	<meta name="description" content="inventory">
	<meta name="author" content="nilagang duwende">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/POS.css">
	<script src="js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script> 
	$(document).ready(function(){
	    $("#See").click(function(){
	        $("#search").slideToggle("10000");
	    });
	});
	$(document).ready(function(){
	    $("#ito").click(function(){
	        $("#eto").fadeIn("7000");
	    });
	});

	function display_c(){
	var refresh=1000; // Refresh rate in milli seconds
	mytime=setTimeout('display_ct()',refresh)
	}

	function display_ct() {
	var strcount
	var x = new Date()
	var x1=x.getMonth() + "/" + x.getDate() + "/" + x.getYear(); 
	x1 = x1 + " - " + x.getHours( )+ ":" + x.getMinutes() + ":" + x.getSeconds();
	document.getElementById('ct').innerHTML = x1;

	tt=display_c();
	}
	</script>

	 <style type="text/css">
		body
		{
			font-family: "Century Gothic";
	        overflow-y: hidden;
	        overflow-x: hidden;
			
		}
</style>
</head>
<body onload="display_ct();">
<!-- Navbar -->
	<nav class="navbar navbar-inverse col-md-12">
	  <div class="container-fluid" >
	    <div class="navbar-header">
	      <img src="images/GayMon.JPG" class="logo" alt=""style="float:left; width:55px; height: 50px; padding: 5px;margin-left:-25px;"><span class="navbar-brand" style="font-size:40px;" >Dwarf's Pharmacy</span>
	    </div>
	     <ul class="nav navbar-nav navbar-right">
	        <li class="dropdown">
	          <a class="dropdown-toggle" data-toggle="dropdown" href="#" style="transition: all 0.3s;margin-right:-15px;">Welcome Cashier <span class="caret"></span></a>
	          <ul class="dropdown-menu">
	            <li><a name="Log_Out"data-toggle="modal" id="ito"data-target=".bs-example-modal-sm" href="#">Log Out</a></li>
	            <li><a name="Change_Password"href="#" data-toggle="modal" data-target=".bs-example-modal-lg">Change Password</a></li>
	          </ul>
	        </li>
	      </ul>
	  </div>
	 </nav>
<!-- Side Bar POS -->
	<div id="sidebar" class="col-xs-6 col-md-4">
		<span class="col-xs-6 col-md-4"style="color:white;">Product Name</span>
		<span class="col-xs-7 col-md-2"style="color:white;">Box</span>
		<span class="col-xs-7 col-md-2"style="color:white;">Piece</span>
		<span class="col-xs-7 col-md-2"style="color:white;">Total</span>
		<span class="col-xs-7 col-md-2"style="color:white;">Delete</span>
		

		<div id = "ShowedValue">
		</div>
		<div id = "innerleftdown">
		<span style = "font-size:16px;color:white;margin-left:20px;">Total</span>
		<span style = "font-size:16px;color:white;margin-left:20px;float:right;margin-right:20px;"id="Price">Php</span>
		</div>
		<span style = "font-size:16px;color:white;margin-left:20px;"></span><br/>
		<span style = "font-size:16px;color:white;margin-left:20px;position:absolute;margin-top:-10px;"id="ct"></span>
	
	 </div>
<!-- For trans -->
	<div id="POS" class="col-xs-12 col-sm-6 col-md-12"style="padding-left:0px;">       
            <img src="css/blitzer/images/Menu.png" alt="" id="show" style="float:left; width:70px; height: 70px; padding: 10px;">
            <img src="css/blitzer/images/Menu.png" alt="" id="hide" style="float:left; width:70px; height: 70px; padding: 10px;">            
            <span id="Labelz" class="Lbl">Point Of Sales</span> 
            <script type/="text/javascript">
                   $('#hide').click(function(){
                       $('#sidebar').hide('slide');
                       $('#hide').hide();
                       document.getElementById("POS").style.width="100%";
                       $('#show').show();    
                   });
                   $('#show').click(function(){
                       $('#sidebar').show('slide');
                       document.getElementById("POS").style.width="72%";
                       $('#hide').show();
                       $('#show').hide();

                   });
            </script>
			<div id="searchbar">
				<input text="text"class="form-control" id="texttosee"onkeyup="showdata()"style="border-radius:0px;width:20%;float:right;margin-left:10px;height:35px;margin-top:18px;margin-right:50px;" maxlength = "10"/>
				<span style="float:right;font-size:20px;margin-top:20px;">Search:</span>
				<div id="showdata">
				 </div>
            </div>
                
            <div id="stuck">
                <div>
                         <center>
                                <div class="btn-group" id="showpagezs"style="position:relative;">
                                </div>
                        </center>
                    </div>
                    <div id="Buttonsplace">

                        <input type="submit" class="btn btn-danger" onclick="Modaldal();"value="Finish the Transaction"data-toggle="modal"style="float:left;margin-left:50px; border-radius:0px;transition: all 0.3s;" data-target=".finishtrans"/>
                        <input type="submit" class="btn btn-danger" value="Cancel Transaction"data-toggle="modal"style="float:right;margin-right:50px; border-radius:0px;transition: all 0.3s;" data-target=".CancelTrans"/>
                    </div>
            </div>		
	</div>
<!-- Modal For AddQuantity -->
	<div class="modal fade AddQuantity" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	  <div class="modal-dialog">
	    <div class="modal-content">
	    	<div class="modal-header">
	    		<h1>Add Quantity For</h1><span id="ProdName">AA</span>
	    		<input type="hidden" id="ProdID"/>
	    	</div>
	    	<div class="modal-body">
	    			<label>Quantity For Piece</label>
		   		   	<input type="text"id="forPerPiece"class="form-control" maxlength="9" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/>
                    <input type="number"id="copy"class="form-control" style="display:none" />
		   		   	<label>Quantity For Box</label><label id="copy1" value="ewan"></label>
		   		   	<input type="text"id="forPerBox"class="form-control" maxlength="9" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/>
                    <input type="number"id="copy2"class="form-control" style="display:none"/>
		    </div>
	      	<div class="modal-footer">
	      		<input type="submit"  class="btn btn-danger" style="float:right;" onclick="addeds();"/>
                <input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
	      	</div>
	    </div>
	  </div>
	</div>
<!-- Modal for Change Password -->
	<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	    	<div class="modal-header">
	    		<h1>Change Password</h1>
	    	</div>
	    	<div class="modal-body">
		      	<label>Old Password</label>		<input type="text" name="Old_Password" class="form-control" style="width:60%;margin:auto;"/><br/>
		      	<label>New Password</label>		<input type="text" name="New_Password" class="form-control" style="width:60%;margin:auto;"maxlength="20" placeholder="20 characters max"/><br/>
		      	<label>Confirm Password</label>	<input type="text" name="Confirm_Password" class="form-control" style="width:60%;margin:auto;"maxlength="20" placeholder="20 characters max"/><br/>
		     </div>
	      	<div class="modal-footer">
	      		<input type="submit" class="btn btn-danger"/>
	      	</div>
	    </div>
	  </div>
	</div>
<!-- Modal for Log Out -->
	<div class="modal bs-example-modal-sm" id="eto" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="top:150px;">
	  <div class="modal-dialog modal-sm">
	    <div class="modal-content">
	      	<div class="modal-header ">
	    		<h1>Log Out</h1>
	    	</div>
	    	<div class="modal-body">
		     	<h5>Are you sure you want to log out ?</h5>
		     </div>
	      	<div class="modal-footer">
	    	  	<form action=""method="POST">
					<input type ="submit" value="Log Out" name="Logout" class="btn btn-danger" style="border-radius:0px;" />
		        </form> 
		      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
	      	</div>
	    </div>
	  </div>
	</div>
<!-- Modal For Cancel Trans -->
	<div class="modal CancelTrans" id="eto" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="top:150px;">
	  <div class="modal-dialog modal-sm">
	    <div class="modal-content">
	      	<div class="modal-header ">
	    		<h1>Cancel Transaction</h1>
	    	</div>
	    	<div class="modal-body">
		     	<h5>Are you sure you want Cancel the Transaction ?</h5>
		     </div>
	      	<div class="modal-footer">
	    	  	<form action=""method="POST">
					<input type ="submit" value="Cancel Transaction"onclick="CancelTrans();" class="btn btn-danger" style="border-radius:0px;" />
		        </form> 
		      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
	      	</div>
	    </div>
	  </div>
	</div>
<!-- Modal For finish trans -->
		<div class="modal fade finishtrans" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      	<div class="modal-header ">
		    		<h1>Finish Transaction</h1>
		    		<button type="button" class="close"data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		    	</div>
			    <div class="modal-body Bodybody"style="overflow-y:scroll;">
				</div>
				<div class="modal-body">
						 <br/>
						 <input type="text" id="Cash" placeholder="Cash Tendered" class="form-control" style="margin-top:0px;border-radius:0px;width:35%;float:left" maxlength="9" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/>
						 <input type="text" id="Senior" placeholder="(Optional)" class="form-control" style="margin-top:0px;border-radius:0px;width:35%;float:right"/>
						 <br/>
						 <br/>
						 <br/>
						 <label style="float:left;">Cash Tendered</label>
						 <label style="float:right;">Senior Citizen ID</label>
						  <br/>	
				</div>
		      	<div class="modal-footer">
				
		      		<input type="submit" class="btn btn-danger" style="float:right;"onclick="teller()"value="Finish Transaction"  />
		      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
					
		      	</div>
		    </div>
		  </div>
		</div>
<!-- Overlapped modal for finish trans -->
		<div class="modal fade Overlapped" id="finish" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		    	<div id="content">
			      	<div class="modal-header">
			    		<h1>Transaction <span id="TransactionD"><?php echo $row['TransactionID']; ?></span></h1>
			    		<button type="button"data-dismiss="modal" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			    	</div>
			    	<div class="modal-body Bodybody" >
			    	</div>
			    	<div class="modal-body">
			    	<table>
				     	<tr><td><span style = "font-size:17px;">Cash Tendered : </span></td><td><span id="FinalCT" style = "font-size:18px;"></span>PHP</td></tr>
						<tr><td><span style = "font-size:17px;">Total Price  </span></td><td><span id="FinalTP" style = "font-size:18px;"></span>PHP</td></tr>
						<tr><td><span style = "font-size:17px;">Discount  </span></td><td><span id="FinalDI" style = "font-size:18px;"></span></td></tr>
						<tr><td><span style = "font-size:17px;">Change  </span></td><td><span id="FinalCH" style = "font-size:18px;"></span>PHP</td></tr>
				  	</table>
				    </div>
				    <div class="modal-footer">	
				    	<h6><?php echo date("Y-m-d H-i-s");?></h6>
				    </div>
				</div>
		      	<div class="modal-footer">
		      		<input type="submit" class="btn btn-danger" id="buttonprint"style="float:right;"value="Finish Transation" onclick="newtrans();printContent('content');"/>
		      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
		      	</div>
		    </div>
		  </div>
		</div>
</body>
<script type="text/javascript">
	showdata();
	POS();
    //Function For Print
	     function printContent(data) {
	        
	         var restorePage = document.body.innerHTML;
	         var thisPrintContent = document.getElementById(data).innerHTML;
	         document.body.innerHTML = thisPrintContent;
	         window.print();
	         document.body.innerHTML = restorePage;
             window.location.href="POS.php";
	    }
	    function newtrans()
	    {
	    	var finalCash=$("#FinalCT").text();
	    	var finalTotal=$("#FinalTP").text();
	    	var finalDiscount=$("#FinalDI").text();
	    	var finalChange=$("#FinalCH").text();
	    	var finalID=$("#Senior").val();
	    	var transactiodI = $("#TransactionD").text();


	    	$.ajax({
	    		url   :"POS.php",
                    type  : "POST",
                    async : false,
                    data  : {
                            trans : 1,
                            Cash:finalCash,
                            Total:finalTotal,
                            Discount:finalDiscount,
                            Changed:finalChange,
                            SeniorID:finalID,
                            TransactionID1:transactiodI
                            },
                    success : function(finishtr)
                    {
                    	alert("transaction completed");
                    	$(".Overlapped").modal('hide');
						$(".finishtrans").modal('hide');
							showdata();
							POS();

                    } 

	    	});
	    }
	var Next = parseInt($(".pagenum").val());
	//function for showing default datas
		function showdata()
			{

				var texttosee = $('#texttosee').val();
						Next=1;
				if(texttosee != "")
				{
					$.ajax
							({
								url : "POS.php",
								type : "POST",
								async : false,
								data : {
										SearchValue : 1,
										texttoseek : texttosee,
										N : Next
									   },
								success : function(zx)
								{
									Showpages();
									if(Next == 1)
									{
										document.getElementById("Previous").disabled = true;
									}
									else if(Next == "<?php echo $BA; ?>")
										{
											document.getElementById("Nextcx").disabled = true;
										} 
									$("#showdata").html(zx);
								}
							});

				}
				else
				{
				$.ajax
					({
						url : "POS.php",
						type : "POST",
						async : false,
						data : {
								showtable : 1,
								N : Next,
							   },
						success : function(result)
						{
							Showpages();
							if(Next == 1)
							{
								document.getElementById("Previous").disabled = true;
							} 
							else if(Next == "<?php echo $BA; ?>")
							{
								document.getElementById("Nextcx").disabled = true;
							} 
							$("#showdata").html(result);
						}
					});
				}
			}
		function showzxc()
			{
				Next++;
				$.ajax
				({
					url : "POS.php",
					type : "POST",
					async : false,
					data : {
							showtable : 1,
							N : Next,
						   },
					success : function(re)
					{
						Showpages();
							if(Next == "<?php echo $BA; ?>")
							{
								document.getElementById("Nextcx").disabled = true;
							} 
						$("#showdata").html(re);
					}
				});
			}
		$('body').delegate('.pagenum','click',function()
			{
				Next=$(this).attr("idz");
				  	$.ajax
					  				 ({
					  					url:"POS.php",
										type:"POST",
										async:false,
										data    : {
												showtable : 1,
												N : Next

									  			},
										success : function(rezult)
										{
											alert(Next);
											$('#showdata').html(rezult);
											if(Next == 1)
												{
													document.getElementById("Previous").disabled = true;
												}
											else if(Next !=1)
												{
													document.getElementById("Previous").disabled = false;
												}

											Showpaged();
											
										} 
				  					});
			});
	//function for showing table search
		function showzxc1()
			{
				Next--;
				$.ajax
				({
					url : "POS.php",
					type : "POST",
					async : false,
					data : {
							showtable : 1,
							N : Next
						   },
					success : function(rez)
					{
						
						Showpages();
						if(Next == 1)
							{
								document.getElementById("Previous").disabled = true;
							}
						else if(Next !=1)
							{
								document.getElementById("Previous").disabled = false;
							}
						$("#showdata").html(rez);
					}
				});

			}
	//function for showing the page numbers
		function Showpages()
			{
				$.ajax
				({
					url : "POS.php",
					type : "POST",
					async : false,
					data : {
							showpagez : 1,
						   },
					success : function(res)
					{
						$("#showpagezs").html(res);
					}
				});
			}
	//function for showing the modal
		$("body").delegate(".Add",'click',function()
				{	
					var IdAdd = $(this).attr('ida');
					$.ajax
						({

							url      : "POS.php",
							type     : "POST",
							async    : false,
							data     :{
										aded : 1,
										id   : IdAdd
									  },
							success  : function(r)
								{
									document.getElementById("ProdName").innerHTML = r.ProductName;
									$("#forPerPiece").attr({
                                        "placeholder": "Ramaining Quantity: " + r.Quantity_Per_Piece,
                                    });
									$("#forPerBox").attr({
                                        "placeholder": "Remaining Quanity: "+ r.Quantity_Per_box,
                                        });
                                    $("#copy").val(r.Quantity_Per_Piece);
                                    $("#copy2").val(r.Quantity_Per_box);
									$("#ProdID").val(r.ProdID);
								} 
						  });

				});
	//function for addProduct
		function addeds()
		{
            if( parseInt($("#copy").val()) < parseInt($("#forPerPiece").val()) || parseInt($("#copy2").val()) < parseInt($("#forPerBox").val())){
               alert("Please input a smaller quantity than your remaining quantity");
                $("#forPerPiece").val(null);
                $("#forPerBox").val(null);
                return;
            }
            if($("#forPerPiece").val() == "" && $("#forPerBox").val() == "" ){
               alert("Please input the quantity");
                return;
            }
            if($("#forPerPiece").val() == "" || $("#forPerBox").val() == "" ){
               var Id = $("#ProdID").val();
                var QuantityPiece = $('#forPerPiece').val();
                var QuantityBox = $('#forPerBox').val();

                $.ajax
                ({
                    url   :"POS.php",
                    type  : "POST",
                    async : false,
                    data  : {
                            Addsz : 1,
                            ProdId : Id,
                            QuantityB : QuantityBox,
                            QuantityP : QuantityPiece 
                          },
                    success:function(rezult1)
                    {

                        	
                        if(rezult1 == 0)
                        {
                            $("#forPerPiece").val(null);
                            $("#forPerBox").val(null);
                            
                            POS();
                        }
                    }
                });
            return;
            }
            if(parseInt($("#copy").val()) > parseInt($("#forPerPiece").val()) &&  parseInt($("#copy2").val()) > parseInt($("#forPerBox").val())){
                var Id = $("#ProdID").val();
                var QuantityPiece = $('#forPerPiece').val();
                var QuantityBox = $('#forPerBox').val();

                $.ajax
                ({
                    url   :"POS.php",
                    type  : "POST",
                    async : false,
                    data  : {
                            Addsz : 1,
                            ProdId : Id,
                            QuantityB : QuantityBox,
                            QuantityP : QuantityPiece 
                          },
                    success:function(rezult1)
                    {
                        if(rezult1 == 0)
                        {
                            $("#forPerPiece").val(null);
                            $("#forPerBox").val(null);
                            POS();
                        }
                    }
                });
            return;
            }
			
		}
	//function for showing the transaction table
	 	function POS()
		 {
		 	$.ajax({
		 		url : "POS.php",
		 		type: "POST",
		 		async : false,
		 		data :{
		 			ShowedVal:1
		 		},
		 		success: function(zxce)
		 		{
		 			Total();
		 			$('#ShowedValue').html(zxce);
		 			$(".AddQuantity").modal("hide");
		 		}
		 	});
		 }
	//function for showing the total
	 function Total()
		{
				$.ajax({

					url:"POS.php",
					type : "POST",
					async : false,
					data : {
						Totalsz : 1
					},
					success : function(Resz)
					{
						document.getElementById("Price").innerHTML = Resz;
					}

				});
		}
	//function for showing the final output
        
		function teller()
		{
			var seniorid=$("#Senior").val();
                if($("#Cash").val() == ""){
                    alert("The cash that you tendered is invalid!");
                }
                else{
                  var abc=$("#FinalTP").val();
                  $.ajax({

					url:"POS.php",
					type : "POST",
					async : false,
					data : {
						TotalSales:abc,
						Totalszz : 1
					},
					success : function(resz)
					{
                        document.getElementById("FinalTP").innerHTML = resz;
                        if( parseInt($("#Cash").val()) < resz){
                            alert("The cash that you tendered is invalid!");
                        }
                        else if(seniorid != ""){
                            document.getElementById("FinalCT").innerHTML = $("#Cash").val();
						    document.getElementById("FinalDI").innerHTML= "20% DISCOUNT";
						    document.getElementById("FinalCH").innerHTML = ((resz * 0.2) + parseInt($("#Cash").val()))-resz ;
                            $("#finish").modal('show');
                        }
                        else if(seniorid == ""){
                            document.getElementById("FinalCT").innerHTML = $("#Cash").val();
						    document.getElementById("FinalDI").innerHTML= "No Discount";
						    document.getElementById("FinalCH").innerHTML = parseInt($("#Cash").val())-resz ;
                            $("#finish").modal('show');
                        }
					}

				});		  
                }
                
				
		}
	//function for showing the trans table to modal
		function Modaldal()
		{
			$.ajax({

				url:"POS.php",
				type : "POST",
				async : false,
				data : {
					ETOWS : 1
				},
				success : function(rezzult)
				{
					$('.Bodybody').html(rezzult);
					$('#bodybody1').html(rezzult);
				}	

			});
		}
	//function for cancelling the transaction
		function CancelTrans()
		{
			$.ajax({

				url:"POS.php",
				type : "POST",
				async : false,
				data : {
					DELE : 1
				},
				success : function(rezzult1)
				{
					alert(rezzult1);
					alert("Transaction Cancelled");
				}	

			});		
		}
	//removing one by one
		$("body").delegate('.remove','click',function()
		{
			var IDp = $(this).attr("idz");
			var IDt = $(this).attr("idy");
			var IDn = $(this).attr("idx");

			$.ajax
			({
				url : "POS.php",
				type: "POST",
				async : false,
				data : {
					remover : 1,
					ProdID : IDp,
					TransID : IDt,
					NormalID: IDn
				},
				success : function(bwi)
				{
					POS();
				}
			});
		});
	//function for automated message
		function ImessageMoAko()
		{
			$.ajax({

				url:"POS.php",
				type:"POST",
				async:false,
				data:{
					notifyme:1
				},
				success :function(namessagekana)
				{
					
				}

			});
		}
</script>
</html