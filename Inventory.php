<?php
	date_default_timezone_set("Asia/Manila");
	$date = date("Y-m-d");
	session_start();
	require 'dbconfig.php';	
	//Notes
		//EOQ >> FORMULA NALANG
		//Print >> NOT YET!!
		//Graph >> Database Connection nalang
		//POS >> Auto Generated ID nalang
		//PERCENTAGE EQUIVALENT
		//50% STILL
	//Notes of ate Sam
	        //Notes
	        //Lahat ng echos na label pakipalitan ---- medyo ok na -- help pabutasan :)
	        //EOQ apply = --OK na
	        //Fix SMS alert to supplier OK!!
	        //Auto text kay admin pag may low on supply/expiry/out of stock = wala pa
	        //Add/edit supplier details -- OK!!
	//POLISHING OF PAGING!!!!	
	//For Pie
		$pieG = mysql_query("SELECT count(ProductID) as Products,ProductName from tbltrans inner join
		tblproducts where tbltrans.ProductID = tblproducts.ProdID group by tbltrans.ProductID");
		$rowsPie=array();
		$tablePie = array();
		$tablePie['cols'] = array(

	    // Labels for your chart, these represent the column titles
	    // Note that one column is in "string" format and another one is in "number" format as pie chart only required "numbers" for calculating percentage and string will be used for column title
	    array('label' => 'Product Name', 'type' => 'string'),
	    array('label' => 'Products', 'type' => 'number')
		);
		$rowsPie = array();
		while($r = mysql_fetch_assoc($pieG)) {
		    $temp = array();
		    // the following line will be used to slice the Pie chart
		    $temp[] = array('v' => (string) $r['ProductName']); 

		    // Values of each slice
		    $temp[] = array('v' => (int) $r['Products']); 
		    $rows[] = array('c' => $temp);
		}
		$tablePie['rows'] = $rows;
		$jsonPie = json_encode($tablePie);
	//For Bar
		$pieB = mysql_query("SELECT ProductName,Eoq_Per_Piece,Eoq_Per_Box from eoqview");
		$rowsBar = array();
		$tableBar = array();
		$tableBar['cols'] = array(

	    // Labels for your chart, these represent the column titles
	    // Note that one column is in "string" format and another one is in "number" format as pie chart only required "numbers" for calculating percentage and string will be used for column title
	    array('label' => 'Product Name', 'type' => 'string'),
	    array('label' => 'Eoq Per Piece', 'type' => 'number'),
	    array('label' => 'Eoq Per Box', 'type' => 'number')
		);
		$rowsBar = array();
		while($ra = mysql_fetch_assoc($pieB)) {
		    $tempe = array();
		    // the following line will be used to slice the Pie chart
		    $tempe[] = array('v' => (string) $ra['ProductName']); 

		    // Values of each slice
		    $tempe[] = array('v' => (int) $ra['Eoq_Per_Piece']); 
		    $tempe[] = array('v' => (int) $ra['Eoq_Per_Box']); 
		    $rowsBar[] = array('c' => $tempe);
		}
		$tableBar['rows'] = $rowsBar;
		$jsonBar = json_encode($tableBar);
	//Misc Query
		$sql1=mysql_query("SELECT * from tblgeneric");
		$sql2=mysql_query("SELECT * from tbldosage");
		$sql3=mysql_query("SELECT * from tblcategory");
		$sqls1=mysql_query("SELECT * from tblgeneric");
		$sqls2=mysql_query("SELECT * from tbldosage");
		$sqls3=mysql_query("SELECT * from tblcategory");
		$sqlsa1=mysql_query("SELECT * from tblgeneric");
		$sqlsa2=mysql_query("SELECT * from tbldosage");
		$sqlsa3=mysql_query("SELECT * from tblcategory");
		$datesql=mysql_query("SELECT * from tbltrans");
		while($rowDatos=mysql_fetch_array($datesql))
		{
		   $Skips[] = $rowDatos['DateVoid'];
		   
		}
		$skips1=json_encode($Skips);
	//Query For adding a Product
		if(isset($_POST['buttonSave']))
		{
			 $timezone = date_default_timezone_set('Asia/Manila');
			 $date = date("Y-m-d",strtotime("+12 months"));
			 $sql=mysql_query("INSERT INTO tblproducts (ProdID,ProductName) VALUES (DEFAULT,'{$_POST['ProductName']}')");
			 $getId=mysql_insert_id();
			 $sql11=mysql_query("INSERT INTO tblproductinfo (ProdID,GenericID,CategoryID,DosageID,Quantity_Per_Piece,Quantity_Per_box,Price_Per_Box,Price_Per_Piece,Total_Quantity_Per_Box)VALUES(DEFAULT,'{$_POST['GenericID']}','{$_POST['CategoryID']}','{$_POST['DosageID']}','{$_POST['QuantityPiece']}','{$_POST['QuantityBox']}'*10,'{$_POST['PPB']}','{$_POST['PPP']}','{$_POST['TotalQuantity']}')");
			 $sql12=mysql_query("INSERT INTO tblproductOrder(BatchNo,ProductID,SupplyDate,ExpirationDate,BoxQuantity,RetailQuantity)VALUES(DEFAULT,'{$getId}','".date('y-m-d')."','{$_POST['Datepicker']}','{$_POST['QuantityBox']}','{$_POST['QuantityPiece']}*{$_POST['QuantityBox']}')");
			 $sql13=mysql_query("INSERT into tbleoq (ProdID,Initial_Date,Last_Date_Updated,Holding_Cost,Ordering_Cost) values('{$getId}','".date('Y-m-d')."','".$date."','{$_POST['Holding']}','{$_POST['Ordering']}')");
			 $sql14=mysql_query("SELECT Count(Qua
			 	ntityBox) as QuantityBox,Count(QuantityPiece) as QuantityPiece  from POSView where ProdID='{$getId}'");
			 $rows=mysql_fetch_assoc($sql14);
			 $sql15 = mysql_query("SELECT Holding_Cost,Ordering_Cost from tbleoq where ProdID='{$getId}'");
			 $rows1=mysql_fetch_assoc($sql15);
			 $formula = (2*$rows['QuantityPiece']*$rows1['Ordering_Cost'])/$rows1['Holding_Cost'];
			 $squared = sqrt($formula);
		}
	//Query For adding a Brand
		elseif(isset($_POST['buttonSaved1']))
		{
			 $sqla0=mysql_query("INSERT INTO tblgeneric (GenericID,GenericName) VALUES(DEFAULT,'{$_POST['GenericID1']}')");
			 $getId0=mysql_insert_id();
			 $sqla1=mysql_query("INSERT INTO tblproducts (ProdID,ProductName) VALUES (DEFAULT,'{$_POST['ProductName1']}')");
			 $getId1=mysql_insert_id();
			 $sql11=mysql_query("INSERT INTO tblproductinfo (ProdID,GenericID,CategoryID,DosageID,Quantity_Per_Piece,Quantity_Per_box,Price_Per_Box,Price_Per_Piece,Total_Quantity_Per_Box)VALUES(DEFAULT,'{$_POST['GenericID']}','{$_POST['CategoryID']}','{$_POST['DosageID']}','{$_POST['Quantity']}','{$_POST['Quantity']}'*10,'{$_POST['PPB']}','{$_POST['PPP']}','{$_POST['TotalPerBox']}')");
			 $sql12=mysql_query("INSERT INTO tblproductOrder(BatchNo,ProductID,SupplyDate,ExpirationDate,BoxQuantity,RetailQuantity)VALUES(DEFAULT,'{$getId}','".date('y-m-d')."','{$_POST['Datepicker']}','{$_POST['Quantity']}','{$_POST['Quantity']}*400')");
			 $sql13=mysql_query("INSERT into tbleoq (ProdID,Holding_Cost,Ordering_Cost) values('{$getId}','{$_POST['Holding']}','{$_POST['Ordering']}')");
			 $sql14=mysql_query("SELECT Count(QuantityBox) as QuantityBox,Count(QuantityPiece) as QuantityPiece  from POSView where ProdID='{$getId}'");
			 $rows=mysql_fetch_assoc($sql14);
			 $sql15 = mysql_query("SELECT Holding_Cost,Ordering_Cost from tbleoq where ProdID='{$getId}'");
			 $rows1=mysql_fetch_assoc($sql15);
			 $formula = (2*$rows['QuantityPiece']*$rows1['Ordering_Cost'])/$rows1['Holding_Cost'];
			 $squared = sqrt($formula);	}
	//Query For adding a User
		if(isset($_POST['buttonSaveUser']))
		{
			$sql=mysql_query("INSERT INTO tbluser (UserId,UserName,Password,Lastname,FirstName,ContactNumber,UserType) values(DEFAULT,'{$_POST['UserName']}','{$_POST['password']}','{$_POST['FirstName']}','{$_POST['LastName']}','{$_POST['ContactNumber']}','Cashier')");
			echo 0;
			exit();
		}
	//Query for adding a Supplier
		if(isset($_POST['buttonSaveSupplier']))
			{
		        $sql=mysql_query("INSERT INTO tblsupplier(SupID, SupName, ContactNumber) VALUES (DEFAULT,'{$_POST['cmn']}','{$_POST['con']}')");
				echo 0;
				exit();
			}
	//Query For Editting Value
		if(isset($_POST['editValue']))
			{
				$sql=mysql_query("SELECT * from inventoryview where ProdID = '{$_POST['id']}'");
				$row=mysql_fetch_object($sql);
				header("Content-type: text/x-json");
				echo json_encode($row);
				exit();
			}
	//Query For Editting Value Supplier
		if(isset($_POST['editValue4']))
			{
				$sql=mysql_query("SELECT * from tblsupplier where SupID = '{$_POST['id']}'");
				$row=mysql_fetch_object($sql);
				header("Content-type: text/x-json");
				echo json_encode($row);
				exit();
			}
	//Query For Editting Value Users
		if(isset($_POST['editValue2']))
			{
				$sql=mysql_query("SELECT * from tbluser where UserId = '{$_POST['id']}'");
				$row=mysql_fetch_object($sql);
				header("Content-type: text/x-json");
				echo json_encode($row);
				exit();
			}
	//Query For Editting Value
		if(isset($_POST['editValue3']))
			{
				$sql=mysql_query("SELECT * from EOQVIEW where ProdID = '{$_POST['id']}'");
				$row=mysql_fetch_object($sql);
				header("Content-type: text/x-json");
				echo json_encode($row);
				exit();
			}
	//Query For Update
		if(isset($_POST['Updatez']))
			{
				mysql_query("UPDATE tblproductinfo set Price_Per_Box = '{$_POST['PricePbox']}', Price_Per_Piece = '{$_POST['PricePpiece']}' , Quantity_Per_box = Quantity_Per_box+'{$_POST['QuantityPerBox']}',Quantity_Per_Piece = Quantity_Per_Piece+'{$_POST['QuantityPerPiece']}' where ProdID = '{$_POST['ProdID']}'");
				
				echo 0;
				exit();
			}
	//Query For UpdateUser
		if(isset($_POST['UpdateUser']))
		{

				mysql_query("UPDATE tbluser set UserName = '{$_POST['UserName']}', Password = '{$_POST['password']}' , LastName = '{$_POST['LastName']}' , FirstName = '{$_POST['FirstName']}', ContactNumber = '{$_POST['ContactNumber']}'  where UserId = '{$_POST['UserId']}'");
				echo 0;
				exit();
			
		}
	//Query For UpdatePassword
		if(isset($_POST['ChangePassword']))
		{	
			$sql = mysql_query("SELECT * FROM tbluser where UserType ='Administrator'");
			$row = mysql_fetch_array($sql);
			if($_POST['Old_Password'] != $row['Password'])
				{
					echo 1;
					exit();
				}
			elseif($_POST['New_Password'] != $_POST['Confirm_Password'] )
				{
					echo 2;
					exit();
				}
			elseif($_POST['New_Password'] == $_POST['Confirm_Password'] && $_POST['Old_Password'] == $row['Password'])
			    {
					mysql_query("UPDATE tbluser set Password = '{$_POST['New_Password']}' where UserType='Administrator'  ");
					echo 0;
					exit();
				}
		}
	//Query For UpdateSupplier
		if(isset($_POST['UpdateSupplier']))
		{

				mysql_query("UPDATE tblsupplier set SupName = '{$_POST['SupplierName']}', ContactNumber = '{$_POST['Contacts']}' where SupID = '{$_POST['SupplierID']}'");
				echo 0;
				exit();
			
		}
	//Query For Showing the inventoryview
		if(isset($_POST['showtable']))
		{
			$starter=5;
			$formula = (intval($_POST['N'] - 1 )*$starter);
			$sql = mysql_query("SELECT * from inventoryview order by ProdID asc ");		?>	
			<div id="TableScroll2">
						<table class="table table-striped table-bordered table-hover col-xs-12 col-sm-6 col-md-8" style="font-size:12px;height:60%;">
							<thead>
							    <tr>
					              
							      <th style="width:11%;">Product Name</th>
							   	  <th style="width:11%;">Generic Name</th>
							      <th style="width:11%;">Category</th>
							      <th style="width:8%;">Dosage</th>
							      <th style="width:11%;">Expiration Date</th>
							      <th style="width:11%;">Quantity Per Box</th>
							      <th style="width:11%;">Quantity Per Piece</th>
							      <th style="width:11%;">Price Per Box</th>
							      <th style="width:11%;">Price Per Piece</th>	
							     <!-- <th><input type="checkbox" id="check_all" style="cursor:pointer;"/></th>-->	    
							      <th>Maintenance</th>

							    </tr>
					  	 	 </thead>
							<tbody style="font-size:13px;overflow-y:scroll;height:10%;">  
			<?php
						while($row=mysql_fetch_array($sql))
					    {	
					    			$enddate=strtotime("today");
									$date=date("Y-m-d",$enddate); 
									$d=strtotime("January 6, 2016");
									$date1=date("Y-m-d",$d);

					    ?>
	    					
						    	<tr>
					              <td style="font-size:100%;"><?php echo $row['ProductName'];?></td>
							      <td style="font-size:100%;"><?php echo $row['GenericName'];?></td>
							      <td style="font-size:100%;"><?php echo $row['CategoryName'];?></td>
							      <td style="font-size:100%;"><?php echo $row['Dosage'];?></td>
							      <td style="font-size:100%;"><?php echo $row['ExpirationDate'];?></td>
							      <td style="font-size:100%;"><?php echo $row['Quantity_Per_box'];?></td>
							      <td style="font-size:100%;"><?php echo $row['Quantity_Per_Piece'];?></td>
							      <td style="font-size:100%;"><?php echo $row['Price_Per_Box'];?></td> 
								  <td style="font-size:100%;"><?php echo $row['Price_Per_Piece'];?></td>
								  <td>
								  <div class="btn-group">
								  <button  class="btn btn-danger edit"  style="margin-top:3px;transition: all 0.3s;margin-left:5px;width:132px;border-radius:3px;" ide="<?php echo $row['ProdID']; ?>" data-toggle="modal" data-target=".editProduct"  >Edit <i class="glyphicon glyphicon-pencil"></i></button>
				            	  <button  class="btn btn-danger delete"style="margin-top:3px;transition: all 0.3s;margin-left:5px;width:132px;border-radius:3px;" idd="<?php echo $row['ProdID']; ?>">Delete <i class="glyphicon glyphicon-trash"></i></button>
				            	  <button  class="btn btn-danger EOQs"  style="margin-top:3px;transition: all 0.3s;margin-left:5px;width:132px;border-radius:3px;" idu="<?php echo $row['ProdID']; ?>" data-toggle="modal" data-target=".economic" >Update EOQ <i class="glyphicon glyphicon-file"></i></button>
				            	  </div>
				            	  </td>
				            
				 		 	   </tr>  
			 		  		      
			            <?php

	    		        }
	    		        ?>
							</tbody>  
						</table>
	    		        <?php

			exit();

		}
	//Query For ShowUser
			if(isset($_POST['showuser']))
			{		

				$sql1 = mysql_query("SELECT * from tbluser where UserType='Cashier'");
				?>
				<div id="TableScroll">
							<table class="table table-striped table-bordered table-hover col-xs-6 col-sm-3" style="overflow-y:scroll;font-size:12px;height:100px">
							<thead>
							    <tr>
					              
							      <th style="width:10%;">User Name</th>
							   	  <th style="width:10%;">Password</th>
							      <th style="width:10%;">Last Name</th>
							      <th style="width:10%;">First Name</th>
							      <th style="width:10%;">Contact Number</th>
							      <th style="width:10%;">Maintenance</th>

							    </tr>
					  	 	 </thead>
							<tbody style="font-size:13px;overflow-y:scroll;height:100px;">  
			<?php
						while($row=mysql_fetch_array($sql1))
						{
					    ?>
	    					
						    	<tr>
					              <td><?php echo $row['UserName'];?></td>
							      <td><?php echo $row['Password'];?></td>
							      <td><?php echo $row['LastName'];?></td>
							      <td><?php echo $row['FirstName'];?></td>
							      <td><?php echo $row['ContactNumber'];?></td>
								  <td>
								  <div class="btn-group">
								  <button  class="btn btn-danger edit2"  style="margin-top:0px;transition: all 0.3s;" ide="<?php echo $row['UserId']; ?>" data-toggle="modal" data-target=".editUser"  >Edit <i class="glyphicon glyphicon-pencil"></i></button>
				            	  <button  class="btn btn-danger delete1"style="margin-top:0px;transition: all 0.3s;" idd="<?php echo $row['UserId']; ?>">Delete <i class="glyphicon glyphicon-trash"></i></button>
				            	  </div>
				            	  </td>
				            
				 		 	   </tr>  
			 		  		      
			            <?php

	    		        }
	    		        ?>

							</tbody>  
							<table>
			</div>
	    		        <?php

			exit();

			}
	//Query For ShowSupplier
			if(isset($_POST['showsupplier']))
			{		
				$starter=5;
				$formula = (intval($_POST['N'] - 1 )*$starter);
				$sql1 = mysql_query("SELECT * from tblsupplier LIMIT $formula,$starter");
				?>
				<div id="TableScroll1">
							<table class="table table-striped table-bordered table-hover col-xs-6 col-sm-3" style="font-size:12px;height:60%;">
							<thead>
							    <tr>
					              
							      <th style="width:10%;">Company Name</th>
							   	  <th style="width:10%;">Contact Number</th>
	                              <th style="width:10%;">Maintenance</th>
	                            


							    </tr>
					  	 	 </thead>
							<tbody style="font-size:13px;overflow-y:scroll;height:10%;">  
			<?php
						while($row=mysql_fetch_array($sql1))
						{
					    ?>
	    					
						    	<tr>
					              <td><?php echo $row['SupName'];?></td>
							      <td><?php echo $row['ContactNumber'];?></td>

								  <td>
								  <div class="btn-group">
								  <button  class="btn btn-danger edit3"  style="margin-top:0px;transition: all 0.3s;" ide="<?php echo $row['SupID']; ?>" data-toggle="modal" data-target=".editSupplier"  >Edit <i class="glyphicon glyphicon-pencil"></i></button>
				            	  <button  class="btn btn-danger delete2"style="margin-top:0px;transition: all 0.3s;" idd="<?php echo $row['SupID']; ?>">Delete <i class="glyphicon glyphicon-trash"></i></button>
				            	  </div>
				            	  </td>
				            
				 		 	   </tr>  
			 		  		      
			            <?php

	    		        }
	    		        ?>

							</tbody>  
							</table>
					</div>
	    		        <?php

			exit();

			}
	//Query For Dashboard Count
			if(isset($_POST['NormalInvent']))
			{
				$sql=mysql_query("SELECT Count(*) As CountedNormal from inventoryview ");	
				$row=mysql_fetch_object($sql);
				header("Content-type: text/x-json");
				echo json_encode($row);
				exit();
			}
			if(isset($_POST['OutOfStocks']))
			{
				$sql=mysql_query("SELECT Count(*) As CountedOutOFStocks  from inventoryview  where Quantity_Per_box = 0 or Quantity_Per_Piece =0");
				$row=mysql_fetch_object($sql);
				header("Content-type: text/x-json");
				echo json_encode($row);
				exit();
			}
			if(isset($_POST['NearlyExpiree']))
			{
				$sql=mysql_query("SELECT Count(*) As CountedNearly from inventoryview where ExpirationDate <= 20160118  ");
				$row=mysql_fetch_object($sql);
				header("Content-type: text/x-json");
				echo json_encode($row);
				exit();
			}
			if(isset($_POST['ReorderPoints']))
			{
				$sql=mysql_query("SELECT Count(*) As CountedReorder from inventoryview  where RetailQuantity > 50  ");
				$row=mysql_fetch_object($sql);
				header("Content-type: text/x-json");
				echo json_encode($row);		
				exit();
			}
	//Query For Dashboard
			//Kulang neto is DATABASE EXTENSION
			if(isset($_POST['dashboardz']))
			{		
				?>
					<div id="DboardNInventory" >
						<img src="images/NormalInventory.png" class="NI">
						<h1 style="font-weight: bold;float:right;width: 50px;"id = "NICount">23</h1>
						<br/>
						<br/>
						<br/>
						<br/>
						<br/>
						<p class="spancolor">Normal Inventory</p>
					</div>
						
					<div id="DboardOutOfStock" onclick="OutOfStock();" data-toggle="modal" data-target=".OutOfStock">
						<img src="images/OutOfStock.png" class="OS">
						<h1 style="font-weight: bold;float:right; width:50px;" id = "OOSCount">23</h1>
						<br/>
						<br/>
						<br/>
						<br/>
						<br/>
						<p class="spancolor">Out of Stock</p>
					</div>
			
					
					<div id="DboardNearlyExpired" onclick="NearlyX();" data-toggle="modal" data-target=".NearlyExp">
						<img src="images/NearlyExpired.png" class="NE">
						<h1 style="font-weight: bold;float:right;width: 50px;" id="NearlyEx" > Here</h1>
						<br/>
						<br/>
						<br/>
						<br/>
						<br/>
						<p class="spancolor">Nearly Expired</p>
					</div>
					
					 <div id="DboardReorderPoint" data-target=".CategoriesForReordering"data-toggle="modal">
			            <img src="images/ReorderPoint.png"class="RP">
			            <br/>
			            <br/>
			            <br/>
			            <br/>
			            <br/>
	         		   <p class="spancolor">Reorder Point (EOQ)</p>
	        		</div>
	        		  <br/>
				    <br/>
				   

			    <?php
				exit();
			}		
	//Query For SearchingInventory
		if(isset($_POST['SearchValue']))
		{
			$starter=5;
			$formula = (intval($_POST['N'] - 1 )*$starter);	
			$sql1 = mysql_query("SELECT * from inventoryview where {$_POST['Searchval']} Like '%{$_POST['texttosee']}%' order by ExpirationDate desc");
				if(mysql_num_rows($sql1) != 0)
				{
                    
					?>
                                <div id="TableScroll2">
					<table class="table table-striped table-bordered table-hover col-xs-6 col-sm-3" style="font-size:12px;height:60%;">
						    <thead>
							    <tr>
					              
							      <th style="width:11%;">Product Name</th>
							   	  <th style="width:11%;">Generic Name</th>
							      <th style="width:11%;">Category</th>
							      <th style="width:8%;">Dosage</th>
							      <th style="width:11%;">Expiration Date</th>
							      <th style="width:11%;">Quantity Per Box</th>
							      <th style="width:11%;">Quantity Per Piece</th>
							      <th style="width:11%;">Price Per Box</th>
							      <th style="width:11%;">Price Per Piece</th>		
							     <!-- <th><input type="checkbox" id="check_all" style="cursor:pointer;"/></th>-->	    
							      <th>Maintenance</th>

							    </tr>
					  	 	 </thead>
							<tbody style="font-size:13px;overflow-y:scroll;height:10%;">      

					<?php
						while($row1=mysql_fetch_array($sql1))
					    {
					    ?>

		                    
						
						    	<tr>
					              <td style="font-size:100%;"><?php echo $row1['ProductName'];?></td>
							      <td style="font-size:100%;"><?php echo $row1['GenericName'];?></td>
							      <td style="font-size:100%;"><?php echo $row1['CategoryName'];?></td>
							      <td style="font-size:100%;"><?php echo $row1['Dosage'];?></td>
							      <td style="font-size:100%;"><?php echo $row1['ExpirationDate'];?></td>
							      <td style="font-size:100%;"><?php echo $row1['Quantity_Per_box'];?></td>
							      <td style="font-size:100%;"><?php echo $row1['Quantity_Per_Piece'];?></td>
							      <td style="font-size:100%;"><?php echo $row1['Price_Per_Box'];?></td> 
								  <td style="font-size:100%;"><?php echo $row1['Price_Per_Piece'];?></td>
								  <td>
								  <div class="btn-group">
								  <button  class="btn btn-danger edit"  style="margin-top:3px;transition: all 0.3s;margin-left:5px;width:132px;border-radius:3px;" ide="<?php echo $row1['ProdID']; ?>" data-toggle="modal" data-target=".editProduct"  >Edit <i class="glyphicon glyphicon-pencil"></i></button>
				            	  <button  class="btn btn-danger delete"style="margin-top:3px;transition: all 0.3s;margin-left:5px;width:132px;border-radius:3px;" idd="<?php echo $row1['ProdID']; ?>">Delete <i class="glyphicon glyphicon-trash"></i></button>
				            	  <button  class="btn btn-danger EOQs"  style="margin-top:3px;transition: all 0.3s;margin-left:5px;width:132px;border-radius:3px;" idu="<?php echo $row1['ProdID']; ?>" data-toggle="modal" data-target=".economic" >Update EOQ <i class="glyphicon glyphicon-file"></i></button>
				            	  </div>
				            	  </td>
				            
				 		 	   </tr>  
				            
				 		 	   </tr>  
			 		  		     
			              <?php
			          	  }
			          	  ?>

			          	  	</tbody> 
			          	  </table>
                </div>
			          	  <?php
			    }
			    else
			    {
			    	echo "<h1>No Record Found </h1>";
			    }
			exit();
		}
	//Query For SearchingUser
	//Query For Paging by default
		if(isset($_POST['showpages']))
		{	

						?>
					<button id="Previous" class="btn btn-danger" style="margin-top:0px;border-radius:0px;width:100px;"onclick="showzxc();" ><i class="glyphicon glyphicon-menu-left"></i> Previous</button>
						<?php
					    $sqlC = mysql_query("SELECT count(*) from inventoryview ");	
						$counted = mysql_result($sqlC,0)/5;
						$rounded = ceil($counted);
						 for ($i=1; $i <= $rounded ; $i++) 
							{				
							?>
									<button class="btn btn-danger pagenum" id="Pagesss" style="margin-top:0px;border-radius:0px;" idz="<?php echo $i; ?>"><?php echo $i; ?></button>
							<?php
							}
						?>

					<button id="Nextcx" class="btn btn-danger" style="margin-top:0px;border-radius:0px;width:100px;"onclick="showzxc1();">Next <i class="glyphicon glyphicon-menu-right"></i></button>				
						<?php
					exit();	
		}
	//Query For User Default Paging
			if(isset($_POST['showpages2']))
			{	

							?>
						<button id="Previous1" class="btn btn-danger" style="margin-top:0px;border-radius:0px;width:100px;"onclick="showUserz();" ><i class="glyphicon glyphicon-menu-left"></i> Previous</button>
							<?php
						    $sqlC = mysql_query("SELECT COUNT(*) from tbluser where UserType = 'Cashier'");
							$counted = mysql_result($sqlC,0)/5;
							$rounded = ceil($counted);
							 for ($i=1; $i <= $rounded ; $i++) 
								{				
								?>
										<button class="btn btn-danger pagenumz" id="Pagesss" style="margin-top:0px;border-radius:0px;" ida="<?php echo $i; ?>"><?php echo $i; ?></button>
								<?php
								}
							?>

						<button id="Nextcx1" class="btn btn-danger" style="margin-top:0px;border-radius:0px;width:100px;"onclick="showUserz1();">Next <i class="glyphicon glyphicon-menu-right"></i></button>				
							<?php
						exit();	
			}
	//Query For Paging with SearchInventory
		if(isset($_POST['showpages1']))
		{	
						$sqlCC = mysql_query("SELECT COUNT(*) from inventoryview where {$_POST['Searchval']} Like '%{$_POST['texttosee']}%' ");
						$counted1 = mysql_result($sqlCC,0)/5;
						$rr = ceil($counted1);

						?>
					<button id="Previous" class="btn btn-danger" style="margin-top:0px;border-radius:0px;width:100px;"onclick="showzxc();" ><i class="glyphicon glyphicon-menu-left"></i> Previous</button>
						<?php

						 for ($a=1; $a <= $rr ; $a++) 
							{				
							?>
									<button class="btn btn-danger pagenum" id="Pagesss" style="margin-top:0px;border-radius:0px;" idz="<?php echo $a; ?>"><?php echo $a; ?></button>
							<?php
							}
						?>

					<button id="Nextcx" class="btn btn-danger" style="margin-top:0px;border-radius:0px;width:100px;"onclick="showzxc1();">Next <i class="glyphicon glyphicon-menu-right"></i></button>				
						<?php
					exit();	
		}
	//Query For paging Supplier
		if(isset($_POST['showpages3']))
		{	
						$sqlCC = mysql_query("SELECT COUNT(*) from tblsupplier ");
						$counted1 = mysql_result($sqlCC,0)/5;
						$rr = ceil($counted1);

						?>
					<button id="Previous3" class="btn btn-danger" style="margin-top:0px;border-radius:0px;width:100px;"onclick="showzxc3();" ><i class="glyphicon glyphicon-menu-left"></i> Previous</button>
						<?php

						 for ($a=1; $a <= $rr ; $a++) 
							{				
							?>
									<button class="btn btn-danger pagenum2" id="Pagesss2" style="margin-top:0px;border-radius:0px;" idf="<?php echo $a; ?>"><?php echo $a; ?></button>
							<?php
							}
						?>

					<button id="Nextcx3" class="btn btn-danger" style="margin-top:0px;border-radius:0px;width:100px;"onclick="showzxc13();">Next <i class="glyphicon glyphicon-menu-right"></i></button>				
						<?php
					exit();	
		}
	//Query For Paging with SearchUser
	//Query For Delete Product
		if(isset($_POST['deletes']))
		{
			echo $_POST['ids'];
			$sql = mysql_query("DELETE FROM tblproductinfo Where ProdId = '{$_POST['id']}'");
			if($sql)
			{
				echo"Success";
			}
		}
	//Query For Delete User
		if(isset($_POST['deleteUser']))
		{
			echo $_POST['ids'];
			$sql = mysql_query("DELETE FROM tbluser Where UserId = '{$_POST['id']}'");
			if($sql)
			{
				echo"Success";
			}
		}
	//Query For Delete Supplier
		if(isset($_POST['deleteSupplier']))
		{
			echo $_POST['ids'];
			$sql = mysql_query("DELETE FROM tblsupplier Where SupID = '{$_POST['id']}'");
			if($sql)
			{
				echo"Success";
			}
		}
	//Query For Logout
		if(isset($_POST['LogOut']))
			{
				session_destroy();
				header("Location:index.php");
			}
	//Query for Color Coding Out of Stock DONE
		if(isset($_POST['outOfStock']))
		{	
			$starter=5;
			$sql = mysql_query("SELECT * from inventoryview  where Quantity_Per_box = 0 and Quantity_Per_Piece = 0  ");
			if(mysql_num_rows($sql) <> 0)
			{
			?>	
			<script type="text/javascript">
			document.getElementById("OutOfstock").style.visibility="visible";
			</script>
						<table class="table table-striped table-bordered table-hover col-md-12" style="font-size:12px;height:60%;color:width:100%;">
							<thead>
							    <tr>
					              
							      <th >Product Name</th>
							   	  <th >Generic Name</th>
							      <th >Category</th>
							      <th >Dosage</th>
							      <th >Expiration Date</th>
							      <th >Quantity</th>

							    </tr>
					  	 	 </thead>
							<tbody style="font-size:15px;overflow-y:scroll;height:10%;color:#3498db;font-weight:bold;">  
			<?php
						while($row=mysql_fetch_array($sql))
					    {	
					    			$enddate=strtotime("today");
									$date=date("Y-m-d",$enddate); 
									$d=strtotime("January 6, 2016");
									$date1=date("Y-m-d",$d);

					    ?>
	    					
						    	<tr>
					              <td><?php echo $row['ProductName'];?></td>
							      <td><?php echo $row['GenericName'];?></td>
							      <td><?php echo $row['CategoryName'];?></td>
							      <td><?php echo $row['Dosage'];?></td>
							      <td><?php echo $row['ExpirationDate'];?></td>
							      <td><?php echo $row['RetailQuantity'];?></td>
				            
				 		 	   </tr>  
			 		  		      
			            <?php

	    		        }
	    		        ?>
							</tbody>  
						</table>
	    		        <?php
	    	}
	    	else
	    	{
	   		?>	
	   					<h1>There are no products that are out of stock.</h1>
	   					<script type="text/javascript">
						document.getElementById("OutOfstock").style.visibility="hidden";
						</script>
	    	<?php
	    	}
			exit();

		
	   }
	//Query for Color Coding Nearly Expired DONE
		if(isset($_POST['Nexpired']))
		{	
			$starter=5;
			$date = date("y-m-d",strtotime("+14 day"));
			$sql1 = mysql_query("SELECT * from inventoryview where ExpirationDate <= 20160118  ");
			if(mysql_num_rows($sql1) != 0)
			{
			?>	
			<script type="text/javascript">
			document.getElementById("NearlyExpire").style.visibility="visible";
			</script>
						<table class="table table-striped table-bordered table-hover col-md-12" style="font-size:12px;height:60%;color:width:100%;">
							<thead>
							    <tr>
					              
							      <th >Product Name</th>
							   	  <th >Generic Name</th>
							      <th >Category</th>
							      <th >Dosage</th>
							      <th >Expiration Date</th>
							      <th >Quantity Per Box</th>
                                  <th >Quantity Per Piece</th>

							    </tr>
					  	 	 </thead>
							<tbody style="font-size:15px;overflow-y:scroll;height:10%;color:#2ecc71;font-weight:bold;">  
			<?php
						while($row=mysql_fetch_array($sql1))
					    {	
					    			$enddate=strtotime("today");
									$date=date("Y-m-d",$enddate); 
									$d=strtotime("January 6, 2016");
									$date1=date("Y-m-d",$d);

					    ?>
	    					
						    	<tr>
					              <td><?php echo $row['ProductName'];?></td>
							      <td><?php echo $row['GenericName'];?></td>
							      <td><?php echo $row['CategoryName'];?></td>
							      <td><?php echo $row['Dosage'];?></td>
                                    <td><?php echo $row['ExpirationDate'];?></td>
							      <td><?php echo $row['Quantity_Per_box'];?></td>
							      <td><?php echo $row['Quantity_Per_Piece'];?></td>
				            
				 		 	   </tr>  
			 		  		      
			            <?php

	    		        }
	    		        ?>
							</tbody>  
						</table>
	    		        <?php
	    	}
	    	else
	    	{
	   		?>	
	   					<h1>There no product to be expired.</h1>
	   					<script type="text/javascript">
						document.getElementById("NearlyExpire").style.visibility="hidden";
						</script>
	    	<?php
	    	}
			exit();

		}
	//Query For UpdateEOQ
		if(isset($_POST['UpdateEOQ']))
		{


				$EOQquery=mysql_query("SELECT sum(QuantityBox) as QuantityBox , sum(QuantityPiece) as QuantityPiece from tbltrans where DateVoid Between '2016-01-01' and '2017-01-01' and ProductID='{$_POST['ProdID']}'");
				$others=mysql_query("SELECT Total_Quantity_Per_Box from tblproductinfo where ProdID='{$_POST['ProdID']}'");
				$row=mysql_fetch_array($EOQquery);
				$row1=mysql_fetch_array($others);
				$box=$row['QuantityBox']*$row1['Total_Quantity_Per_Box'];

				//EOQ
				     $Co=10;
				 	 $Ch=2;
					 $formula1=(2*$row['QuantityPiece']*$Co)/$Ch;
					 $eoqpiece=sqrt($formula1);

					 $Co1=10;
				 	 $Ch1=2;
					 $formula2=(2*$box*$Co1)/$Ch1;
					 $eoqbox=sqrt($formula2);

				//REORDER
					   $REORDERbox=($box/365)*3;
				 	   $REORDERpiece = ($row['QuantityPiece']/365)*3; 
				

				mysql_query("UPDATE tbleoq set Holding_Cost = '{$_POST['Holding_Cost']}', Ordering_Cost = '{$_POST['Ordering_Cost']}' ,Reorder_Point_Per_Box=$REORDERbox , Reorder_Point_Per_Piece = $REORDERpiece , Eoq_Per_Box = $eoqbox , Eoq_Per_Piece = $eoqpiece where ProdID = '{$_POST['ProdID']}'");
				echo 0;
				exit();
			
		}
	//Query for Color Coding Recorder Point Piece
		if(isset($_POST['reorderPoint']))
		{	
			$starter=5;
			$sql=mysql_query("SELECT eoqview.ProductName as ProdName, round(Eoq_Per_Piece) as ABC from eoqview inner join tblproductinfo where tblproductinfo.Quantity_Per_Piece=(SELECT round(Reorder_Point_Per_Piece))");
			if(mysql_num_rows($sql) != 0)
			{
			?>	
			<script type="text/javascript">
			document.getElementById("rquantity").style.visibility="visible";
			</script>
						<table class="table table-striped table-bordered table-hover col-md-12" style="font-size:12px;height:60%;color:width:100%;">
							<thead>
							    <tr>
					              
							      <th>Product Name</th>
							      <th>Quantity To Order</th>
							     <!-- <th><input type="checkbox" id="check_all" style="cursor:pointer;"/></th>-->	    

							    </tr>
					  	 	 </thead>
							<tbody style="font-size:15px;overflow-y:scroll;height:10%;color: #9b59b6;font-weight:bold;">  
			<?php
						while($row=mysql_fetch_array($sql))
					    {	
					    			$enddate=strtotime("today");
									$date=date("Y-m-d",$enddate); 
									$d=strtotime("January 6, 2016");
									$date1=date("Y-m-d",$d);

					    ?>
	    					
						    	<tr>
					              <td><?php echo $row['ProdName'];?></td>
							      <td><?php echo $row['ABC']; ?></td>
				            
				 		 	   </tr>  
			 		  		      
			            <?php

	    		        }
	    		        ?>
							</tbody>  
						</table>
	    		        <?php
	    	}
	    	else
	    	{
	   		?>	
	   					<h1>There are no products that needs to be reordered.</h1>
	   					<script type="text/javascript">
						document.getElementById("rquantity").style.visibility="hidden";
						</script>
	    	<?php
	    	}
			exit();

		}
	//Query for Color Coding Recorder Point Box
		if(isset($_POST['reorderPoint1']))
		{	
			$starter=5;
			$sql = mysql_query("SELECT eoqview.ProductName as ProdName, round(Eoq_Per_Box) as ABC from eoqview inner join tblproductinfo where tblproductinfo.Quantity_Per_box=(SELECT round(Reorder_Point_Per_Box))");
			if(mysql_num_rows($sql) != 0)
			{
			?>	
			<script type="text/javascript">
			document.getElementById("rquantity").style.visibility="visible";
			</script>
						<table class="table table-striped table-bordered table-hover col-md-12" style="font-size:12px;height:60%;color:width:100%;">
							<thead>
							    <tr>
					              
							      <th>Product Name</th>
							      <th>Quantity To Order</th>
							     <!-- <th><input type="checkbox" id="check_all" style="cursor:pointer;"/></th>-->	    

							    </tr>
					  	 	 </thead>
							<tbody style="font-size:15px;overflow-y:scroll;height:10%;color: #9b59b6;font-weight:bold;">  
			<?php
						while($row=mysql_fetch_array($sql))
					    {	
					    			$enddate=strtotime("today");
									$date=date("Y-m-d",$enddate); 
									$d=strtotime("January 6, 2016");
									$date1=date("Y-m-d",$d);

					    ?>
	    					
						    	<tr>
					              <td><?php echo $row['ProdName'];?></td>
							      <td><?php echo $row['ABC']; ?></td>
				            
				 		 	   </tr>  
			 		  		      
			            <?php

	    		        }
	    		        ?>
							</tbody>  
						</table>
	    		        <?php
	    	}
	    	else
	    	{
	   		?>	
	   					<h1>There are no products that needs to be reordered.</h1>
	   					<script type="text/javascript">
						document.getElementById("rquantity").style.visibility="hidden";
						</script>
	    	<?php
	    	}
			exit();

		}
	//Query For Messaging 
		if(isset($_POST['send']))
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
	            $cellnumber2=$_POST['secondnum'];
	            $text=$_POST['Message'];
	            $result = itexmo($cellnumber2,$text,"09287923698EA455874");
	            if ($result == ""){
	            echo "iTexMo: No response from server!!! <br>
	            Please check the METHOD used (CURL or CURL-LESS). If you are using CURL then try CURL-LESS and vice versa.  
	            Please <a href=\"https://www.itexmo.com/contactus.php\">CONTACT US</a> for help. "; 
	            }else if ($result == 0){
	            echo "Message Sent!";
	        }
	        else
	        {   
	       		 echo "Error Num ". $result . " was encountered!";
	        }

	       	exit();
	    }
	//Query for SuppliersNumber
	//Query For collecting numbers
	    if(isset($_POST['mynumbers']))
	    {
	    	$sqlsa4=mysql_query("SELECT * from tblsupplier");
	    	?>	    		<select id="num2" onchange="supname()" onload="supsup()" class="form-control" style="width:40%;float:left;margin-left:100px;">
	    			<option disabled style="color=gray">Select Number</option>
	    			<?php
	    				while($row5=mysql_fetch_array($sqlsa4))
	    				{
	    					?>

	    						<option value="<?php echo $row5['ContactNumber']; ?>"><?php echo $row5['ContactNumber'] ?></option>

	    					<?php
	    				}

	    			?>

	    		</select>
                <input type="text" disabled id="names"class="form-control"style="width:40%;float:left;margin-left:15px;"/>

	    		<textarea style="margin-top:10%;margin-left:-40px;"rows="4"id="textmessage" cols="100" class="form-control" placeholder="your message goes here!!..." maxlength="100"></textarea>


	    	<?php
	    	exit();
	    }
	//Query For Dates
	    if(isset($_POST['Dated']))
	    {
	    	$as=mysql_query("SELECT * from tbltrans where DateVoid = '{$_POST['Datec']}' group by TransactionID ");
	    	?>
	    	<select class="form-control" id="transacnumber">
	    	<?php
	    	while($row=mysql_fetch_array($as))
	    	{
	    	?>
	    		<option value="<?php echo $row['TransactionID']; ?>"><?php echo $row['TransactionID']; ?></option>
	    	<?php
	    	}

	    	?>
	    	</select>
	    	<?php

	    	exit();
	    }
	//query for suppname
	    if(isset($_POST['supnumber']))
	    {
	                $sql=mysql_query("SELECT * from tblsupplier where ContactNumber = {$_POST['catname']}");
					$row=mysql_fetch_object($sql);
					header("Content-type: text/x-json");
					echo json_encode($row);
					exit();
	    }
	//Query for Notifying
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
	//Experiment
						$sqlCA = mysql_query("SELECT COUNT(*) from inventoryview");
						$counted2 = mysql_result($sqlCA,0)/5;
						$rounded2 = ceil($counted2);
						$BA=$rounded2;
						$next1= mysql_query("SELECT COUNT(*) from tbluser ");
						$nextcounted1 = mysql_result($next1,0)/5;
						$nextrr1 = ceil($nextcounted1);
						$next2= mysql_query("SELECT COUNT(*) from tblsupplier ");
						$nextcounted2 = mysql_result($next2,0)/5;
						$nextrr = ceil($nextcounted2);
						$sqlsample= mysql_query("SELECT * from tbltrans group by TransactionID");
	//Query For Print
		if(isset($_POST['printReport']))
		{
			$sql=mysql_query("SELECT * from tbltrans where DateVoid='{$_POST['datos']}' and TransactionID={$_POST['TransID']} ");
			$row=mysql_fetch_object($sql);
			header("Content-type: text/x-json");
			echo json_encode($row);
			exit();
		}
		if(isset($_POST['printReport1']))
		{
			$sql=mysql_query("SELECT * from tblsales where DateVoid='{$_POST['datees']}' and TransID={$_POST['TransaID']} ");
			$row=mysql_fetch_object($sql);
			header("Content-type: text/x-json");
			echo json_encode($row);
			exit();
		}
	//Query For TablePrint
		if(isset($_POST['ETOWS']))
		{

			$sql=mysql_query("SELECT * from POSview where TransactionID = {$_POST['transID']} and DateVoid = '{$_POST['Datos']}' ");
			$sql1 = mysql_query("SELECT SUM((PricePiece*QuantityPiece) + (PriceBox*QuantityBox)) as TotalSales from POSview where TransactionID = {$_POST['transID']} and DateVoid = '{$_POST['Datos']}'");
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
		

?>


<!DOCTYPE html>
	<!-- HELLO READERS This System Is A Sample of Inventory Management with POS EOQ WITH SMS Notification Technology -->
	<!-- Created By Nilagang Duwende Corp. -->
	<!-- Cuevas Carlo As Senior Programmer -->
	<!-- Lance, Junel as Junior Programmer -->
	<!-- Vince, Corina as Program Assistance -->
	<!-- and Ms. Charlene Sambale as Project Administratior-->
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
	<link rel="stylesheet" type="text/css" href="css/inventory.css">
	<link rel="stylesheet" href="css/blitzer/jquery-ui-1.10.4.custom.css">
	<script src="js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script src="js/jquery-ui.js"></script>
	<script type="text/javascript" src="js/gviz-api.js"></script>
	<script type="text/javascript" src="js/loader.js"></script>
<script> 
	//Extra
		var enableDates = '<?php echo $skips1; ?>';
		function DisableSpecificDates(date) 
		{
		    var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
		    return [enableDates.indexOf(string) != -1];
		}
	//For validation
		  $(function () {
		      $('.LetterText').keydown(function (e) {
		          if (e.shiftKey || e.ctrlKey || e.altKey) {
		              e.preventDefault();
		          } else {
		              var key = e.keyCode;
		              if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90))) {
		                  e.preventDefault();
		              }
		          }
		      });
	 	  });
			function NumbersOnly() 
			{
			    if (event.which != 46 && (event.which < 47 || event.which > 59))
			    {
			        event.preventDefault();
			        if ((event.which == 46) && ($(this).indexOf('.') != -1)) {
			            event.preventDefault();
			        }
			    }
			}
	//function for modal
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
	//Functions for Datepicker
			 $(function(){
			    $( "#Datepicker" ).datepicker({
			      minDate     : +1,
			      dateFormat  :'yy-mm-dd',
			      numberOfMonths: 3
			    });
			  });
			 	 $(function() {
			    $( "#Datepicker1" ).datepicker({
			      minDate     : +1,
			      dateFormat  :'yy-mm-dd',
			      numberOfMonths: 3
			    });
			  });
			 	  $(function() {
			    $( "#Datepicker2" ).datepicker({
			      minDate     : +1,
			      dateFormat  :'yy-mm-dd',
			      numberOfMonths: 3
			    });
			  });
			 	  $(function() {
			    $( "#Datepicker3" ).datepicker({
			      dateFormat  :'yy-mm-dd',
			      numberOfMonths: 3,
			      beforeShowDay: DisableSpecificDates
			    });
			  });
    // Load Charts and the corechart and barchart packages.
				      google.charts.load('current', {'packages':['corechart']});

				      // Draw the pie chart and bar chart when Charts is loaded.
				      google.charts.setOnLoadCallback(drawChart);
				      function drawChart() {
				        var data = new google.visualization.DataTable(<?php echo $jsonBar; ?>);
				   		var doto = new google.visualization.DataTable(<?php echo $jsonPie; ?>);


				        var piechart_options = {title:'Best Seller Product from least to most',
				                       width:"100%",
				                       height:"20%",
				                       chartArea : {left:"0",width:"100%"}
				                   		};
				        var piechart = new google.visualization.PieChart(document.getElementById('piechart_div'));
				        piechart.draw(doto, piechart_options);

				        var barchart_options = {title:'Eoq Ranges',
				                       width:"100%",
				                       height:"20%",
				                       chartArea : {left:"150",width:"50%"}
				                   		};
				        var barchart = new google.visualization.BarChart(document.getElementById('barchart_div'));
				        barchart.draw(data, barchart_options);

				      }
    //Time
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
        margin: 0px;
        width: 100%;
        overflow-y:hidden;
	}
</style>
</head>
<body onload="supsup();display_ct();">
    <!-- NavBar sa taas to -->
		<nav class="navbar navbar-inverse"> 
		  <div class="container-fluid" >
		    <div class="navbar-header">
		      <img src="images/GayMon.JPG" class="logo" alt=""style="float:left; width:55px; height: 50px; padding: 5px;margin-left:-10px;"><span class="navbar-brand" style="font-size:40px;" >Dwarf's Pharmacy</span>
		    </div>
		     <ul class="nav navbar-nav navbar-right">
		        <li class="dropdown">
		          <a class="dropdown-toggle" data-toggle="dropdown" href="#" style="transition: all 0.3s;">Welcome Admin <span class="caret"></span></a>
		          <ul class="dropdown-menu">
		            <li><a name="Log_Out" href="#"data-toggle="modal" id="ito" data-target=".bs-example-modal-sm" >Log Out</a></li>
		            <li><a name="Change_Password" href="#"data-toggle="modal" data-target=".Changepassk">Change Password</a></li>
		          </ul>
		        </li>
		      </ul>
		  </div>
		</nav>
	<!--SideBar nor Dashboard -->
		<div id="sidebar">
            <div style="margin-top:20px;">
			<div class="btn-group-vertical"style="width:100%;">
				<button class="btn btn-danger" style="font-size:18px;text-align:left;transition: all 0.3s;margin-bottom:-25px;padding:13px;" id="See"><i class="glyphicon glyphicon-search" style="font-size:18px;"> </i> Search</button>
						<div id="search">
						<span style="font-size:20px;color:white;margin-left:20px;">
							Category
						</span>
							<select id="Searchval" class="form-control"style="width:80%;height:20%; border:0;border-radius:0;margin:auto;">
								<option value="ProductName" selected default>Product Name</option>
								<option value="Dosage">Dosage</option>
								<option value="CategoryName">Category</option>
								<option value="Price_Per_Box">Price Per Box</option>
								<option value="Price_Per_Piece">Price Per Piece</option>
							</select>
							<br/>
							<span style="font-size:18px;color:white;margin-left:20px;">Search</span>
							<br/>
						    <input type="text" id="texttosee"onkeyup="showdata()"class="form-control" style="padding:6px 12px;height:20%;margin:auto;width:80%;border-radius:0px;"/>
						</div>
                <button class="btn btn-danger " style="font-size:18px;text-align:left;transition: all 0.3s;padding:13px;border-top:1px solid #E74C3C;height:50px;" data-toggle="modal"data-target=".addChoice" id="hey" ><i class="glyphicon glyphicon-plus"></i> Add</button> 
                <button class="btn btn-danger " onClick="dashboard();"   style="font-size:18px;text-align:left;transition: all 0.3s;padding:13px;border-top:1px solid #E74C3C;height:50px;"  id="dboard" ><i class="glyphicon glyphicon-home"></i> Dashboard<img src="images/t.png" id="see1"></button>  
				<button class="btn btn-danger " onclick="showdata1();"   style="font-size:18px;text-align:left;transition: all 0.3s;padding:13px;border-top:1px solid #E74C3C;height:50px;" id="MUser" ><i class="glyphicon glyphicon-cog"> </i> Manage User<img src="images/t.png" id="see2"></button>
				<button class="btn btn-danger " onclick="showdata();"    style="font-size:18px;text-align:left;transition: all 0.3s;padding:13px;border-top:1px solid #E74C3C;height:50px;" id="MUin" ><i class="glyphicon glyphicon-cog"> </i> Manage Inventory<img src="images/t.png" id="see3"></button>
                <button class="btn btn-danger " onclick="showsupplier();"    style="font-size:18px;text-align:left;transition: all 0.3s;padding:13px;border-top:1px solid #E74C3C;height:50px;" id="Msup" ><i class="glyphicon glyphicon-cog"> </i> Manage Supplier<img src="images/t.png" id="see5"></button>
				<button class="btn btn-danger " onclick="Graphing();"    style="font-size:18px;text-align:left;transition: all 0.3s;padding:13px;border-top:1px solid #E74C3C;height:50px;display:none" id="Graph" ><i class="glyphicon glyphicon-th-large"> </i> Graph<img src="images/t.png" id="see4"></button>
				<button class="btn btn-danger " id="Reports" style="font-size:18px;text-align:left;transition: all 0.3s;padding:13px;border-top:1px solid #E74C3C;height:50px;" id="Reports" data-toggle="modal" data-target=".PrintingReports" ><i class="glyphicon glyphicon-list-alt"> </i> Reports</button>
				<button class="btn btn-danger "  style="font-size:18px;text-align:left;transition: all 0.3s;padding:13px;border-top:1px solid #E74C3C;height:50px;" id="Notify" data-toggle="modal"data-target=".Message"><i class="glyphicon glyphicon-envelope"> </i> Notify Supplier</button>
                
                
              	<script>
                $("#dboard").click(function(){
                    $("#see1").css("display", "inherit");
                    $("#see2").css("display", "none");
                    $("#see3").css("display", "none");
                    $("#see4").css("display", "none");
                    $("#see5").css("display", "none");
                });
                $("#MUser").click(function(){
                    $("#see1").css("display", "none");
                    $("#see2").css("display", "inherit");
                    $("#see3").css("display", "none");
                    $("#see4").css("display", "none");
                    $("#see5").css("display", "none");
                });
                $("#MUin").click(function(){
                    $("#see1").css("display", "none");
                    $("#see2").css("display", "none");
                    $("#see3").css("display", "inherit");
                    $("#see4").css("display", "none");
                    $("#see5").css("display", "none");
                });
                 $("#Graph").click(function(){
                    $("#see1").css("display", "none");
                    $("#see2").css("display", "none");
                    $("#see3").css("display", "none");
                    $("#see4").css("display", "inherit");
                    $("#see5").css("display", "none");
                });
                $("#Msup").click(function(){
                    $("#see1").css("display", "none");
                    $("#see2").css("display", "none");
                    $("#see3").css("display", "none");
                    $("#see4").css("display", "none");
                    $("#see5").css("display", "inherit");
                });
                </script>
                
			</div>
		</div>
		<br/>
		<span style = "font-size:16px;color:white;margin-left:20px;position:absolute;bottom:0;margin-bottom:20px;"id="ct"></span>
            </div>
	<!--Inventory Codes COLOR CODING-->
		<div id="Inventory" class="col-xs-12 col-sm-6 col-md-12">
            <!--<img src="css/blitzer/images/Menu.png" alt="" class="buttonsliders" style="position:absolute; width:70px; height: 70px; padding: 10px;">-->
            <img src="css/blitzer/images/Menu.png" alt="" id="show" style="float:left; width:70px; height: 70px; padding: 10px;margin-left: -10px;">
            <img src="css/blitzer/images/Menu.png" alt="" id="hide" style="float:left; width:70px; height: 70px; padding: 10px;margin-left: -10px;">
            
            <span id="Labelz" class="Lbl"></span>
            
            
            <script type/="text/javascript">
                   $('#hide').click(function(){
                       $('#sidebar').hide('slide');
                       $('#hide').hide();
                       $('#show').show();
                        document.getElementById("Inventory").style.width = "100%";
                        document.getElementById("showdata").style.marginTop	="6.3%";
                        document.getElementById("piechart_div").style.marginLeft="12%";
                        document.getElementById("barchart_div").style.marginLeft="3%";
                        $("#search").slideUp("10000");
                   });
                   $('#show').click(function(){
                       $('#sidebar').show('slide');
                       $('#hide').show();
                       $('#show').hide();
                       document.getElementById("Inventory").style.width = "80%";
                       document.getElementById("piechart_div").style.marginLeft="8%";
                       document.getElementById("barchart_div").style.marginLeft="1%";
                       document.getElementById("showdata").style.marginTop	="8%";

                   });
            </script>

            <div id="dib2">
	        <div id="showdata">	
			</div>
					<div id="Charts">
				    	<div id="piechart_div" style=" float:left;margin-left:10%;width:40%;height:400px;"></div>
				    	<div id="barchart_div"style=" float:left;width:40%;height:400px;margin-left:3%;"></div>
				   </div>
            </div>
			<center>			

			<!--<button class="btn btn-danger" style="margin-top:50px;;border-radius:0px;position:absolute;"> Add new User</button>-->
			<div class="btn-group" id="showpagezs"style="display:none;">
			</div>
			<br/>
			</center> 

			<div id="Legend">

                	
					<br/>

					<div id="Color4">
						
						<br/>
						<br/>
						<br/>
						<br/>
						<p class="spancolor">Normal Inventory</p>

					</div>

					<div id="Color1" onclick="OutOfStock();" data-toggle="modal" data-target=".OutOfStock">
						<br/>
						<br/>
						<br/>
						<br/>
						<p class="spancolor" >Out of Stock</p>
						

					</div>
					<div id="Color2" onclick="NearlyX();" data-toggle="modal" data-target=".NearlyExp">
						
						<br/>
						<br/>
						<br/>
						<br/>
						<p class="spancolor">Nearly Expired</p>

					</div>
					<div id="Color3" data-target=".CategoriesForReordering"data-toggle="modal">
					    <br/>
						<br/>
						<br/>
						<br/>
						<p class="spancolor">Reorder Point (EOQ)</p>

					</div>

			</div>

		</div>
	<!--Welcome to my world >=D -->
	<!-- Modal For Change Password DONE -->
		<div class="modal fade Changepassk" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		    	<div class="modal-header">
		    		<h3>Change Password</h3>
		    	</div>
		    	<div class="modal-body">
			      	<label>Old Password</label>		<input type="text" id="Old_Password" class="form-control" style="width:60%;margin:auto;"/><br/>
			      	<label>New Password</label>		<input type="text" id="New_Password" class="form-control" style="width:60%;margin:auto;"maxlength="20" placeholder="20 characters max"/><br/>
			      	<label>Confirm Password</label>	<input type="text" id="Confirm_Password" class="form-control" style="width:60%;margin:auto;"maxlength="20" placeholder="20 characters max"/><br/>
			     </div>
		      	<div class="modal-footer">
		      		<input type="submit" class="btn btn-danger" onclick="ChangePass();"id="Change_Password"style="float:right;border-radius:0px;"value="Change Password"/>
		      		<input type="submit" class="btn btn-default" style="float:left;border-radius:0px;"data-dismiss="modal" value="Cancel"/>
		      	</div>
		    </div>
		  </div>
		</div>
	<!-- Modal for LogOut DONE -->
		<div class="modal bs-example-modal-sm" id="eto" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="top:100px;">
		  <div class="modal-dialog modal-sm">
		    <div class="modal-content">
		      	<div class="modal-header ">
		    		<h4 style="font-family:'Century Gothic';">Log Out</h4>
		    	</div>
		    	<div class="modal-body">
			     	<h6 style="font-weight:normal; font-family:'Century Gothic'">Are you sure you want to log out ?</h6>
			     </div>
		      	<div class="modal-footer">
		      	<form action=""method="POSt">
		      		<input type="submit" class="btn btn-danger" name="LogOut"style="float:right;"value="Log out"/>
		      	</form>
		      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
		      	</div>
		    </div>
		  </div>
		</div>
	<!-- Modal for add choices DONE -->
		<div class="modal fade addChoice" id="eto" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" >
		  <div class="modal-dialog modal-sm">
		    <div class="modal-content">
		    	<div class="modal-header">
		    		<h5>
		    			Add New 
		    		</h5>	
		    	</div>
		    	<div class="modal-body">
		    		<div class="btn-group-vertical"style="width:100%;">
					  <button type="button"id="AddProduct" class="btn btn-danger" style="border-radius:0px;transition: all 0.3s;"data-toggle="modal"data-target=".addNewProduct" data-dismiss="modal">Add new Product</button>
					  <button type="button"id="AddBrand" class="btn btn-danger" style="margin-top:10px;border-radius:0px;transition: all 0.3s;" data-toggle="modal"data-target=".addNewBrand" data-dismiss="modal">Add new Brand</button>
                      <button type="button"id="AddUsers" class="btn btn-danger" style="margin-top:10px;border-radius:0px;transition: all 0.3s;" data-toggle="modal"data-target=".addNewUser" data-dismiss="modal">Add new User</button>
                        <button type="button"id="AddSupplier" class="btn btn-danger" style="margin-top:10px;border-radius:0px;transition: all 0.3s;" data-toggle="modal"data-target=".addNewSupplier" data-dismiss="modal">Add new Supplier</button>
					</div>	
				</div>	
                <div class="modal-footer">
		      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
		      	</div>
		    </div>
		  </div>
		</div>
	<!-- Modal for Reorder choices DONE -->
		<div class="modal fade CategoriesForReordering" id="eto" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" >
		  <div class="modal-dialog modal-sm">
		    <div class="modal-content">
		    	<div class="modal-header">
		    		<h5>
		    			Reorder Point Category
		    		</h5>	
		    	</div>
		    	<div class="modal-body">
		    		<div class="btn-group-vertical"style="width:100%;">
					  <button type="button"id="ReorderPiece" class="btn btn-danger" style="border-radius:0px;transition: all 0.3s;"data-toggle="modal"data-target=".reorderBox" data-dismiss="modal" onclick="ReorderPoint();">Reorder For Per Piece</button>
					  <button type="button"id="ReorderBox" class="btn btn-danger" style="margin-top:10px;border-radius:0px;transition: all 0.3s;" data-toggle="modal"data-target=".reorderPiece" onclick="ReorderPoint1();" data-dismiss="modal">Reorder For Per Box</button>
                    </div>	
				</div>	
                <div class="modal-footer">
		      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
		      	</div>
		    </div>
		  </div>
		</div>
	<!-- Modal for add New Product DONE-->
		<div class="modal fade addNewProduct" id="eto" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"style="overflow-y:scroll;" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		    	<div class="modal-header">
		    		<h3>
		    			Add New Product
		    		</h3>	

		    	</div>
		    	<div class="modal-body" style="margin-left:90px;">
		    	<label for="ProductName">Product Name  </label>
		    		<input type="text"  name="ProductName" id="ProductName"class="form-control LetterText" style="display:inline;width:72.5%;" placeholder="Product Name"/>
		    		<br/>
		    		<label >Generic Name</label>
		    		<select  id="GenericID" class="form-control	" style="display:inline;width:30%;">
		    		<option disabled selected default></option>
		    		<?php
		    		while($row1=mysql_fetch_array($sql1))
		    		{
		    		?>
		    		<option  value="<?php echo $row1['GenericID'];?>"><?php echo $row1['GenericName'];?></option>
		    		<?php
					}
		    		?>
		    		</select>
		    		<label style=" margin-left:32px;">Dosage</label>
		    		<select id="DosageID" class="form-control" style="display:inline;width:30%;">
		    		<option disabled selected default></option>
		    		<?php
		    		while($row2=mysql_fetch_array($sql2))
		    		{
		    		?>
		    		<option  value="<?php echo $row2['DosageID']?>"><?php echo $row2['Dosage'];?></option>
		    		<?php
					}
		    		?>
		    		</select>
		    		<br/>
		    		
		    	    <label style=" margin-left:36px;">Category</label>
		    		<select id="CategoryID" class="form-control" style="display:inline;width:26.5%;" >
		    		<option disabled selected default ></option>
		    		<?php
		    		while($row3=mysql_fetch_array($sql3))
		    		{
		    		?>
		    		<option  value="<?php echo $row3['CategoryID']?>"><?php echo $row3['CategoryName'];?></option>
		    		<?php
					}
		    		?>
		    		</select>

		    	    <label >Quantity Per Box</label>
		    		<input id="QuantityBox" type="number" min="10" max="10000" maxlength="5"class="form-control" onkeypress="NumbersOnly();"style="display:inline;width:30%;">
		    		<br/>
		    		<center>
		    		<label >Quantity Per Piece</label>
		    		<input id="QuantityPiece" type="number" min="10" max="10000" maxlength="5"class="form-control" onkeypress="NumbersOnly();"style="display:inline;width:30%;">
		    		</center>
		    		<br/>
		    		<label >Price Per Piece</label>
		    		<input class="form-control"onkeypress='return event.charCode >= 48 && return event.charCode == 46 && event.charCode <= 57'style="display:inline;width:30%;text-align:right" id="PPP" type="text" maxlength="7" placeholder="Price per piece" />
		    		<label>Price Per Box</label>
		    		<input class="form-control"onkeypress='return event.charCode >= 48 && return event.charCode == 46 && event.charCode <= 57'style="display:inline;width:30%;text-align:right" id="PPB" type="text" maxlength="7" placeholder="Price per box" />
		    	    <br/>
		    		<label style="margin-left:-2px;">Expiration Date</label>
		    		<input type="text" id="Datepicker" class="form-control" style="display:inline;width:72%;" placeholder="Select Expiration Date" maxlength="10" onkeypress='return event.charCode >= 48 && return event.charCode == 45 && event.charCode <= 57'/>
		    		<br/>
		    		<label>Holding Cost</label>
		    		<input class="form-control"onkeypress="NumbersOnly();"style="display:inline;width:30%;text-align:right;margin-left:14px;" id="HC" type="text" maxlength="7" placeholder="Holding Cost" />
		    		<label>Ordering Cost</label>
		    		<input class="form-control"onkeypress="NumbersOnly();"style="display:inline;width:28.5%;text-align:right" id="OC" type="text" maxlength="7" placeholder="Ordering Cost" />
		    		<label>Total Quantity Per Box</label>
		    		<input class="form-control"onkeypress="NumbersOnly();"style="display:inline;width:65.5%;text-align:right" id="TQPB" type="text" maxlength="7" placeholder="Total Quantity Per Box" />
		    	    

		    		
		    	</div>
		      	<div class="modal-footer">

		      		<input type="button" class="btn btn-danger" value="Add new Product" id="Add"/>

		      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal"data-target=".addChoice" data-toggle="modal"value="Cancel"/>
		      	</div>
		    </div>
		  </div>
		</div>
	<!-- Modal for add New Brand DONE--> 
		<div class="modal fade addNewBrand" id="eto" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="overflow-y:scroll;">
		 <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		    	<div class="modal-header">
		    		<h3>
		    			Add New Brand
		    		</h3>	
		    	</div>
		    	<div class="modal-body" style="margin-left:90px;">
		    	<label >Product Name  </label>
		    	<input type="text"  name="ProductName1" id="ProductName1"class="form-control LetterText" style="display:inline;width:72.5%;" placeholder="Product Name" />
		    	<br/>
		    	<label >Generic Name </label>
		    	<input type="text"  name="GenericID1" id="GenericID1"class="form-control LetterText" style="display:inline;width:32%;" placeholder="Generic Name" />
		    	
		    		<label style=" margin-left:22px;">Dosage</label>
		    		<select id="DosageID1" class="form-control" style="display:inline;width:30%;">
		    		<option disabled selected default></option>
		    		<?php
		    		while($rows2=mysql_fetch_array($sqls2))
		    		{
		    		?>
		    		<option  value="<?php echo $rows2['DosageID']?>"><?php echo $rows2['Dosage'];?></option>
		    		<?php
					}
		    		?>
		    		</select>
		    		<br/>
		    		
		    	    <label style=" margin-left:36px;">Category</label>
		    		<select id="CategoryID1" class="form-control" style="display:inline;width:25%;">
		    		<option disabled selected default></option>
		    		<?php
		    		while($rows3=mysql_fetch_array($sqls3))
		    		{
		    		?>
		    		<option  value="<?php echo $rows3['CategoryID']?>"><?php echo $rows3['CategoryName'];?></option>
		    		<?php
					}
		    		?>
		    		</select>
		    	    <label style="margin-left:17px;">Quantity Per Box</label>
		    		<input id="Quantity1Box" type="number" min="10" max="10000" maxlength="5"class="form-control NumberText" style="display:inline;width:30%;"placeholder="Quantity" >
		    		<br/>
		    		<center>
		    	    <label style="margin-left:17px;">Quantity Per Piece</label>
		    		<input id="Quantity1Piece" type="number" min="10" max="10000" maxlength="5"class="form-control NumberText" style="display:inline;width:30%;"placeholder="Quantity" >
		    		<br/>
		    			
		    		</center>
		    		<label >Price Per Piece</label>
		    		<input class="form-control NumberText"style="display:inline;width:30%;text-align:right" id="PPP1" type="text" maxlength="7" placeholder="Price Per Piece" onkeypress='return event.charCode >= 48 && return event.charCode == 46 && event.charCode <= 57'/>
		    		<label>Price Per Box</label>
		    		<input class="form-control NumberText"style="display:inline;width:30%;text-align:right" id="PPB1" type="text" maxlength="7" placeholder="Price Per Box" maxlength="7" onkeypress='return event.charCode >= 48 && return event.charCode == 46 && event.charCode <= 57'/>
		    	    <br/>
		    		<label style="margin-left:-2px;">Expiration Date</label>
		    		<input type="text" id="Datepicker1" class="form-control" style="display:inline;width:72%;" placeholder="Select Expiration Date" maxlength="10" onkeypress='return event.charCode >= 48 && return event.charCode == 45 && event.charCode <= 57'/>
		    		<br/>
		    		<label>Holding Cost</label>
		    		<input class="form-control"onkeypress="NumbersOnly();"style="display:inline;width:30%;text-align:right;margin-left:14px;" id="HC1" type="text" maxlength="7" placeholder="Holding Cost" />
		    		<label>Ordering Cost</label>
		    		<input class="form-control"onkeypress="NumbersOnly();"style="display:inline;width:28.5%;text-align:right" id="OC1" type="text" maxlength="7" placeholder="Ordering Cost" />
		    		<label>Total Quantity Per Box</label>
		    		<input class="form-control"onkeypress="NumbersOnly();"style="display:inline;width:65.5%;text-align:right" id="TQPB1" type="text" maxlength="7" placeholder="Total Quantity Per Box" />
		    	    
		    		
		    	</div>
		      	<div class="modal-footer">
		      		<input type="button" class="btn btn-danger" value="Add new Brand" id="AddedBrand"/>
		      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal"data-toggle="modal" data-target=".addChoice"value="Cancel"/>
		      	</div>
		    </div>
		  </div>	
		</div>
	<!-- Modal for add New User DONE--> 
	  <div class="modal fade addNewUser" id="eto" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" >
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		    	<div class="modal-header">
					<h4>Add New User<span id="UserNmes"></span></h4>	
		    	</div>
		    	<div class="modal-body" style="margin-left:160px;">
	                
	                <div style="position:relative;">
	                    <label style="margin-top:10px;">Username:</label><br><br>
	                    <label style="margin-top:15px;">Password:</label><br><br>
	                    <label style="margin-top:15px;">First Name:</label><br><br>
	                    <label style="margin-top:15px;">Last Name:</label><br><br>
	                    <label style="margin-top:15px;">Contact No:</label>
	                </div>
	                
	                <div style="position:relative;margin-top:-290px; margin-left:150px">
		    		<input id="uname1" type="text"  maxlength="5" class="form-control NumberText" style="display:inline;width:72%;" placeholder="Username">
	                <br>
		    		<input class="form-control NumberText"style="display:inline;width:72%;" id="pass1" type="text" maxlength="7" placeholder="Password"/>
		    		<br/>	    		
		    		<input class="form-control NumberText"style="display:inline;width:72%;" id="fn1" type="text" maxlength="20" placeholder="First Name" onkeypress='return event.charCode >= 65 && event.charCode <= 90 || event.charCode >= 97 && event.charCode <= 122 '/>
		    	    <br/>
		    		<input type="text" id="ln1" class="form-control" style="display:inline;width:72%;" placeholder="Last Name" maxlength="20" onkeypress='return event.charCode >= 65 && event.charCode <= 90 || event.charCode >= 97 && event.charCode <= 122 '>
	                <br>
		    		<input type="text" id="cno1" class="form-control" style="display:inline;width:72%;" placeholder="Contact Number" maxlength="11" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/>
	                </div>
		    	</div>
		      	<div class="modal-footer">

		      		<input type="submit" class="btn btn-danger "id="Add3" value="Add User" />

		      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
		      	</div>
		    </div>
		  </div>
	 	</div>
    <!-- MODAL FOR ADD Supplier -->
	    <div class="modal fade addNewSupplier" id="addSup" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" >
			  <div class="modal-dialog modal-lg">
			    <div class="modal-content">
			    	<div class="modal-header">
						<h4>Add New Supplier<span id="Supplier"></span></h4>	
			    	</div>
			    	<div class="modal-body" style="margin-left:160px;">
		                <div style="position:relative;">
		                    <label style="margin-top:15px;" >Name:</label><br><br>
		                    <label style="margin-top:15px;" >Contact No:</label><br><br>
		                </div>
		                
		                <div style="position:relative;margin-top:-133px; margin-left:150px">
			    		<input type="text" id="cmn" class="form-control" style="display:inline;width:72%;" placeholder="Company Name" maxlength="30"/>
		                <br>
			    		<input type="text" id="con" class="form-control" style="display:inline;width:72%;" placeholder="eg. 09xxxxxxxxx" maxlength="11" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/>
                        <br>
		                </div>
			    	</div>
                    
                    
			      	<div class="modal-footer">

			      		<input type="submit" class="btn btn-danger "id="Add4" value="Add Supplier" />

			      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
			      	</div>
			    </div>
			  </div>
		 	</div> 
    <!-- MODAL FOR EDIT User -->
	 <div class="modal fade editUser" id="eto" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" >
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	    	<div class="modal-header">
			<h4>Edit <b><span id="Usseerrss">A</span></b></h4>	
	    	</div>
	    	<div class="modal-body" style="margin-left:160px;">
                
                <div style="position:relative;">
                    <label style="margin-top:10px;">UserName:</label><br><br>
                    <label style="margin-top:15px;">Password:</label><br><br>
                    <label style="margin-top:15px;">First Name:</label><br><br>
                    <label style="margin-top:15px;">Last Name:</label><br><br>
                    <label style="margin-top:15px;">Contact No:</label>
                </div>
                
                <div style="position:relative;margin-top:-290px; margin-left:150px">
                <input type="hidden" id="UserId" value=""/>
	    		<input id="uname" type="text"  maxlength="5" class="form-control NumberText" style="display:inline;width:72%;"value="<?php echo $row['UserName']; ?>" placeholder="Username">
                <br>
	    		<input class="form-control NumberText"style="display:inline;width:72%;" id="pass" type="text" maxlength="7" placeholder="Password"/>
	    		<br/>	    		
	    		<input class="form-control NumberText"style="display:inline;width:72%;" id="fn" type="text" maxlength="20" onkeypress='return event.charCode >= 65 && event.charCode <= 90 || event.charCode >= 97 && event.charCode <= 122 ' placeholder="First Name" />
	    	    <br/>
	    		<input type="text" id="ln" class="form-control" style="display:inline;width:72%;" maxlength="20" onkeypress='return event.charCode >= 65 && event.charCode <= 90 || event.charCode >= 97 && event.charCode <= 122 ' placeholder="Last Name"/>
                <br>    
	    		<input type="text" id="cno" class="form-control" style="display:inline;width:72%;" maxlength="11" onkeypress='return event.charCode >= 48 && event.charCode <= 57' placeholder="Contact Number"/>
                </div>

	    		
	    	</div>
	      	<div class="modal-footer">

	      		<input type="submit" class="btn btn-danger "id="Update2" value="Update user" />

	      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
	      	</div>
	    </div>
	  </div>
	 </div>
    <!-- MODAL FOR EDIT Supplier -->
	 <div class="modal fade editSupplier" id="eto" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" >
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	    	<div class="modal-header">
			<h4>Edit <b><span id="Suppliers">A</span></b></h4>	
	    	</div>
	    	<div class="modal-body" style="margin-left:160px;">
                
                <div style="position:relative;">
                    <label style="margin-top:15px;">Name:</label><br><br>
                    <label style="margin-top:15px;">Contact No:</label><br><br>

                </div>
                
                <div style="position:relative;margin-top:-150px; margin-left:150px">
                    <input type="hidden" id="SupplierId" placeholder="Company Name"/><br>
                    <input id="CompName" type="text"  maxlength="30" class="form-control NumberText" style="display:inline;width:72%;" placeholder="Company Name">
                    <br>
                    <input type="text" id="contactn" class="form-control" style="display:inline;width:72%;" placeholder="eg. 09xxxxxxxxx" maxlength="11" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/>
                </div>
	    	</div>
	      	<div class="modal-footer">

	      		<input type="submit" class="btn btn-danger "id="Update4" value="Update user" />

	      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
	      	</div>
	    </div>
	  </div>
	 </div>
	<!-- MODAL FOR Message -->
	 <div class="modal fade Message" id="messenger" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" >
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	    	<div class="modal-header">
			<h4>Notify the Supplier </h4>	
	    	</div>
	    	<div style="position: absolute; margin-top: 40px;margin-left:8%"><label>Choose Number:<label></div><div class="modal-body" style="margin-left:90px;"id="messagela" style="float: right">

	    	</div>
	      	<div class="modal-footer">
	      		<input type="submit" onclick="SendMes();"class="btn btn-danger " value="Send Message" />
	      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
	      	</div>
	    </div>
	  </div>
	 </div>
	<!-- Modal For Out Of Stock -->
		<div class="modal fade OutOfStock" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
			<div class="modal-dialog"style="width:90%;">
				<div class="modal-content">
					<div class="modal-header">
						<h4>Out Of Stock</h4>
					</div>
					<div class="modal-body" id="OutOfStock">
					</div>
					<br/>
					<br/>
					<div class="modal-footer">
		      			<input type="submit" class="btn btn-danger" style="float:right;"data-dismiss="modal" value="Ok"/>
					</div>
				</div>
			</div>
		</div>	 
	<!-- Modal For Nearly Expired -->
		<div class="modal fade NearlyExp" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
			<div class="modal-dialog"style="width:90%;">
				<div class="modal-content">
					<div class="modal-header">
						<h4>Nearly Expired</h4>
					</div>
					<div class="modal-body" id="NearlyExpired">
					</div>
					<br/>
					<br/>
					<div class="modal-footer">
		      			<input type="submit" class="btn btn-danger" style="float:right;"data-dismiss="modal" value="Ok"/>
					</div>
				</div>
			</div>
		</div>	 
	<!-- Modal For Nearly Expired -->
		<div class="modal fade reorderBox" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
			<div class="modal-dialog"style="width:50%;">
				<div class="modal-content">
					<div class="modal-header">
						<h4>Reorder Point Per Piece</h4>
					</div>
					<div class="modal-body" id="reorderP">
					</div>
					<br/>
					<br/>
					<div class="modal-footer">
		      			<input type="submit" class="btn btn-danger" style="float:right;"data-dismiss="modal" value="Ok"/>
					</div>
				</div>
			</div>
		</div>	 
		<div class="modal fade reorderPiece" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
			<div class="modal-dialog"style="width:50%;">
				<div class="modal-content">
					<div class="modal-header">
						<h4>Reorder Point Per Box</h4>
					</div>
					<div class="modal-body" id="reorderB">
					</div>
					<br/>
					<br/>
					<div class="modal-footer">
                        <input type="submit" class="btn btn-danger" style="float:right;"data-dismiss="modal" value="Ok"/>
					</div>
				</div>
			</div>
		</div>	 
	<!-- Modal For Choosing of Reports -->
		<div class="modal fade PrintingReports" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
				<div class="modal-dialog"style="width:50%;">
					<div class="modal-content">
						<div class="modal-header">
							<h4>Reports <button type="button" class="close" aria-label="Close"data-dismiss="modal"><span aria-hidden="true">&times;</span></button></h4>
						</div>
						<div class="modal-body">
							<div class="graphsz">
							   <div class="graphsz1">
								   <img src="images/reporting.png" alt="charts" style="width:100%;height:218px;">
								   <br/>
								   <br/>
								   <br/>
								   <br/>
								   <button class="btn btn-danger"style="margin-top:0px;float:initial;border-radius:0px;"data-toggle="modal"data-target=".CategoryGraph">Transaction Report</button>
								   <br/>
								   <br/>
								   <br/>
							   </div>
							    <div class="graphsz1">
								   <img src="images/charts.jpg" alt="charts" style="width:100%">
								   <br/>
								   <br/>
								   <br/>
								   <br/>
								   <button class="btn btn-danger"data-toggle="modal" data-target=".CategoryReport"style="margin-top:0px;float:initial;border-radius:0px;">Graphing Report</button>
								   <br/>
								   <br/>
								   <br/>
							   </div>

							</div>
						</div>
						<div class="modal-footer">
			      			<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
						</div>
					</div>
				</div>
			</div>	 
	<!-- Modal Printing of Reports Transactions-->
		<div class="modal fade CategoryGraph"  tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="top:100px;">
		  <div class="modal-dialog modal-sm">
		    <div class="modal-content">
		      	<div class="modal-header ">
		    		<h4 style="font-family:'Century Gothic';">Transactions Report</h4>
		    	</div>
		    	<div class="modal-body">
		    		<div class="btn-group-vertical"style="width:100%;">
		    		  <input type="text" class="form-control"id="Datepicker3"onchange="Datela();" placeholder="Please Choose Date"/>
		    		  <div id="replace"></div>
					  <button id="printed"type="button"class="btn btn-danger" data-toggle="modal"data-target=".Overlapped"onclick="Printers();Printers1();" style="margin-top:10px;border-radius:0px;" disabled>Print Report</button>

					</div>
			     </div>
		      	<div class="modal-footer">
		      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
		      	</div>
		    </div>
		  </div>
		</div>
	<!-- Modal Printing of Reports Graph-->
		<div class="modal fade CategoryReport"  tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="top:100px;">
		  <div class="modal-dialog modal-sm">
		    <div class="modal-content">
		      	<div class="modal-header ">
		    		<h4 style="font-family:'Century Gothic';">Graphs</h4>
		    	</div>
		    	<div class="modal-body">
		    		<div class="btn-group-vertical"style="width:100%;">
					  <button type="button"class="btn btn-danger" onclick="printContent('piechart_div')"style="border-radius:0px;">Pie Chart for Best Sellers</button>
					
	                  <button type="button"class="btn btn-danger" onclick="printContent('barchart_div')"style="margin-top:10px;border-radius:0px;" >Bar Chart for EOQ</button>
					</div>
			     </div>
		      	<div class="modal-footer">
		      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
		      	</div>
		    </div>
		  </div>
		</div>
	<!-- Modal for Printing Transaction -->
		<div class="modal fade Overlapped" id="finish" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
					  <div class="modal-dialog modal-lg">
					    <div class="modal-content">
					    	<div id="content">
						      	<div class="modal-header">
						    		<h1>Transaction <span id="TransactionalID"></span></h1>
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
									<tr><td><span style = "font-size:17px;">Senior Citizen ID  </span></td><td><span id="SeniorID" style = "font-size:18px;"></span>PHP</td></tr>
							  	</table>
							    </div>
							    <div class="modal-footer">	
							    	<h6><?php echo date("Y-m-d H-i-s");?></h6>
							    </div>
							</div>
					      	<div class="modal-footer">
					      		<input type="submit" class="btn btn-danger" id="buttonprint"style="float:right;"value="Finish Transation" onclick="printContent('content');"/>
					      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
					      	</div>
					    </div>
					  </div>
					</div>
	<!-- Modal For EDIT EOQ -->
	 <div class="modal fade economic" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" >
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	    	<div class="modal-header">
			<h4>Update EOQ For <span id="ProdNames">A</span></h4>	
	    	</div>
	    	<div class="modal-body" style="margin-left:160px;">
                
                <div style="position:relative;">
                    <label style="margin-top:10px;">Holding Cost</label><br><br>
                    <label style="margin-top:15px;">Ordering Cost</label><br><br>
                </div>
                
                <div style="position:relative;margin-top:-130px; margin-left:150px">
                <input type="hidden" id="ProductIDz" value=""/>
	    		<input id="HoldingCost" type="text"  maxlength="5" class="form-control NumberText" style="display:inline;width:72%;" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/>
                <br/>
	    		<input class="form-control NumberText"style="display:inline;width:72%;" id="OrderCost" type="text" maxlength="7" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/>
	    		<br/>	    		
	    		</div>
	    		
	    	</div>
	      	<div class="modal-footer">
	      		<input type="submit" class="btn btn-danger "id="Update3" value="Update EOQ" />

	      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
	      	</div>
	    </div>
	  </div>
	 </div>
	<!-- MODAL FOR EDIT DONE -->
	 <div class="modal fade editProduct" id="eto" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" >
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	    	<div class="modal-header">
			<h4>Edit <span id="ProductNamezxc">A</span></h4>	
	    	</div>
	    	<div class="modal-body" style="margin-left:90px;">
	    		<input type="hidden" id="ProdID" value=""/>
	    	    <label >Quantity Per Box</label>
	    		<input id="Quantity2" type="text"  maxlength="5"class="form-control NumberText" style="display:inline;width:30%;text-align:right;" onkeypress='return event.charCode >= 48 && event.charCode <= 57' placeholder="Quantity">
	    		<label >Quantity Per Piece</label>
	    		<input id="Quantity3" type="text"  maxlength="5"class="form-control NumberText" style="display:inline;width:30%;text-align:right;" onkeypress='return event.charCode >= 48 && event.charCode <= 57' placeholder="Quantity">
	    		<label style="margin-left:23px;">Price Per Box</label>
	    		<input class="form-control NumberText"style="display:inline;width:30%;text-align:right" id="PPB2" type="text" maxlength="7" onkeypress='return event.charCode >= 48 && return event.charCode == 46 && event.charCode <= 57' placeholder="Price Per Box"/>
	    		<label style="margin-left:23px">Price Per Piece</label>
	    		<input class="form-control NumberText"style="display:inline;width:30%;text-align:right" id="PPP2" type="text" maxlength="7" onkeypress='return event.charCode >= 48 && return event.charCode == 46 && event.charCode <= 57' placeholder="Price Per Piece"/>
	    		
	    	    <br/>
	    		<label style="margin-left:-5px;">Expiration Date</label>
	    		<input type="text" id="Datepicker2" class="form-control" style="display:inline;width:72%;" maxlength="10" onkeypress='return event.charCode >= 48 && return event.charCode == 45 && event.charCode <= 57' placeholder="Select Expiration Date"/>
	    		
	    	</div>
	      	<div class="modal-footer">

	      		<input type="submit" class="btn btn-danger "id="Update" value="Update Product" />

	      		<input type="submit" class="btn btn-default" style="float:left;"data-dismiss="modal" value="Cancel"/>
	      	</div>
	    </div>
	  </div>
	 </div>  
</body>
<script type="text/javascript">
	//For Printings
	    function printContent(data) {
	        
	        var restorePage = document.body.innerHTML;
	        var thisPrintContent = document.getElementById(data).innerHTML;
	        document.body.innerHTML = thisPrintContent;
	        window.print();
	        document.body.innerHTML = restorePage;
	        window.location.href="Inventory.php";
	    }   
    //validation
	 $(function()
			{
                supname();
				dashboard();
				Selects();
	//Code for Add
		//Code For Add of Product
        
        $('#Add').click(function(){
                    if($("#ProductName").val() == "" || $("#GenericID").val() == "" || $('#CategoryID').val() == "" || $('#DosageID').val() == ""  || $('#PPP').val() == "" || $('#Quantity').val() == "" || $('#PPB').val() == "" || $('#Datepicker').val() == "" || $("#HC").val() == "" || $("#OC").val() == "" ){
                                        alert("Please fill up all of the required information of product to add new!");
                    }
            else{
                var ProdName = $('#ProductName').val();
					var GenID = $('#GenericID').val();
					var CatID = $('#CategoryID').val();
					var DosID = $('#DosageID').val();
					var PPPrice = $('#PPP').val();
					var QuanBox = $('#QuantityBox').val();
					var QuanPiece = $('#QuantityPiece').val();
					var PPBox = $('#PPB').val();
					var Dates = $('#Datepicker').val();
					var Hold = $("#HC").val();
					var Order = $("#OC").val();
					var TotalQPB = $("#TQPB").val();
					$.ajax({
						url : "Inventory.php",
						type : "POST",
						async : false,
						data : {
								 buttonSave     : 1,
								 ProductName    : ProdName,
								 GenericID      : GenID,
								 CategoryID     : CatID,
								 DosageID       : DosID,
								 PPP            : PPPrice,
								 QuantityBox    : QuanBox,
								 QuantityPiece  : QuanPiece,
								 PPB            : PPBox,
								 Datepicker     : Dates,
								 Holding 		: Hold,
								 Ordering 		: Order,
								 TotalQuantity  : TotalQPB

							   },
						success : function(resul)
						{
							alert("Record Successfully Added");
							document.getElementById("ProductName").value="";
							document.getElementById("GenericID").value="Select a Generic Name";
							document.getElementById("CategoryID").value="Select a Category";
							document.getElementById("DosageID").value="Select a Dosage";
							document.getElementById("PPP").value="";
							document.getElementById("PPB").value="";
							document.getElementById("QuantityBox").value="";
							document.getElementById("QuantityPiece").value="";
							document.getElementById("Datepicker").value="";
							document.getElementById("HC").value="";
							document.getElementById("OC").value="";
							document.getElementById("TQPB").value="";
							$(".addNewProduct").modal('hide');
							showdata();
							
						}
					});
            }
					
			});
		//Code For Add of Brand
			$('#AddedBrand').click(function(){
                    if($("#ProductName1").val() == "" || $("#GenericID1").val() == "" || $('#CategoryID1').val() == "" || $('#DosageID1').val() == ""  || $('#PPP1').val() == "" || $('#Quantity1').val() == "" || $('#PPB1').val() == "" || $('#Datepicker1').val() == "" || $("#HC1").val() == "" || $("#OC1").val() == "" ){
                                        alert("Please fill up all of the required information of brand to add new!");
                    }
                    else{
                        var ProdName1 = $('#ProductName1').val();
                        var GenID1 = $('#GenericID1').val();
                        var CatID1 = $('#CategoryID1').val();
                        var DosID1 = $('#DosageID1').val();
                        var PPPrice1 = $('#PPP1').val();
                        var Quan1Box = $('#Quantity1Box').val();
                        var Quan1Piece = $('#Quantity1Piece').val();
                        var PPBox1 = $('#PPB1').val();
                        var Dates1 = $('#Datepicker1').val();
                        var Hold1 = $("#HC1").val();
                        var Order1 = $("#OC1").val();
                        var TotalQPB1 = $("#TQPB1").val();
                        $.ajax({
                            url : "Inventory.php",
                            type : "POST",
                            async : false,
                            data : {
                                     buttonSaved1     : 1,
                                     ProductName1     : ProdName1,
                                     GenericID1       : GenID1,
                                     CategoryID1      : CatID1,
                                     DosageID1        : DosID1,
                                     PPP1             : PPPrice1,
                                     Quantity1Box     : Quan1Box,
                                     Quantity1Piece   : Quan1Piece,
                                     PPB1             : PPBox1,
                                     Datepicker1      : Dates1,
                                     Holding 		  : Hold,
                                     Ordering 		  : Order,
                                     TotalPerBox 	  : TotalQPB1

                                   },
                            success : function(resul)
                            {
                                alert("Record Successfully Added");
                                document.getElementById("ProductName1").value="";
                                document.getElementById("GenericID1").value="";
                                document.getElementById("CategoryID1").value="Select a Category";
                                document.getElementById("DosageID1").value="Select a Dosage";
                                document.getElementById("PPP1").value="";
                                document.getElementById("PPB1").value="";
                                document.getElementById("Quantity1Box").value="";
                                document.getElementById("Quantity1Piece").value="";
                                document.getElementById("Datepicker1").value="";
                                document.getElementById("HC1").value="";
                                document.getElementById("OC1").value="";
                                document.getElementById("TQPB1").value="";
                                $(".addNewBrand").modal('hide');
                                showdata();

                            }
					}); 
                    }
					
			});		
		//Code For Add of User
			$('#Add3').click(function(){
                if($('#uname1').val() == "" || $('#pass1').val()  == "" || $('#fn1').val()  == "" || $('#ln1').val() == "" || $('#cno1').val() == "" ){
                    alert("Please fill up the required information to add new user!")
                }
                else{
                    var Uname = $('#uname1').val();
					var Pass = $('#pass1').val();
					var Fname = $('#fn1').val();
					var LName = $('#ln1').val();
					var Contact = $('#cno1').val();
					$.ajax({
						url : "Inventory.php",
						type : "POST",
						async : false,
						data : {
								 buttonSaveUser     : 1,
								 UserName    		: Uname,
								 password       	: Pass,
								 FirstName     	    : Fname,
								 LastName      		: LName,
								 ContactNumber  	: Contact,


							   },
						success : function(resultaz)
						{
							if(resultaz == 0)
							{
								alert("Record Successfully Added");
								document.getElementById("uname1").value="";
								document.getElementById("pass1").value="";
								document.getElementById("fn1").value="";
								document.getElementById("ln1").value="";
								document.getElementById("cno1").value="";
								$(".addNewUser").modal('hide');
								showdata1();
							}
						}
					});
                }
					
			});
   		//Code for Add of Supplier
            $('#Add4').click(function(){
                            if($("#con").val() == "" || $("#cmn").val() == "" ){
                                alert("Please type the company information to add!");
                            }
                            else{
                                var cmn = $('#cmn').val();
                                var con = $('#con').val();                      
                                $.ajax({
                                    url : "Inventory.php",
                                    type : "POST",
                                    async : false,
                                    data : {
                                             buttonSaveSupplier     : 1,
                                             cmn    		: cmn,
                                             con           	: con,
                                           },
                                    success : function(resultaz1)
                                    {
                                        if(resultaz1 == 0)
                                        {
                                            alert("Record Successfully Added");
                                            document.getElementById("cmn").value="";
                                            document.getElementById("con").value="";
                                            $("#addSup").modal('hide');
                                        	Selects();
                                        }

                                    }
                                });
                            }
                    });
	//Code For Delete More
	    	$('body').delegate('.delete','click',function(){
			var IdDelete = $(this).attr('idd');
			var Confirm = window.confirm("Do you really want to delete this record?");
			if(Confirm)
			{
			$.ajax({
				url     : "Inventory.php",
				type    : "POST",
				async   : false,
				data    : {
						deletes : 1,
						id : IdDelete
						  },
				success : function(resu)
				{
						alert("Delete Success");
						showdata();

				}
			});
			}
			});
	//Code For Delete User
			$('body').delegate('.delete1','click',function(){
			var IdDelete = $(this).attr('idd');
			var Confirm = window.confirm("Do you really want to delete this record?");
				if(Confirm)
				{
				$.ajax({
					url     : "Inventory.php",
					type    : "POST",
					async   : false,
					data    : {
							deleteUser : 1,
							id : IdDelete
							  },
					success : function(resu)
					{
							alert("Delete Success");
							showdata1();

					}
				});
				}
			});
	//Code For Delete Supplier 
            $('body').delegate('.delete2','click',function(){
			var IdDelete = $(this).attr('idd');
			var Confirm = window.confirm("Do you really want to delete this record?");
				if(Confirm)
				{
				$.ajax({
					url     : "Inventory.php",
					type    : "POST",
					async   : false,
					data    : {
							deleteSupplier : 1,
							id : IdDelete
							  },
					success : function(resu)
					{
							alert("Delete Success");
							showsupplier();

					}
				});
				}
			});
			})
		//Code For Edit Inventory
			$('body').delegate('.edit','click',function()
			{	
				var IdEdit = $(this).attr('ide');
				$.ajax
					({

						url      : "Inventory.php",
						type     : "POST",
						async    : false,
						data     :{
									editValue : 1,
									id        : IdEdit
								  },
						success  : function(e)
							{
							
								document.getElementById("ProductNamezxc").innerHTML = e.ProductName;
								$("#Datepicker2").val(e.ExpirationDate);
								$("#ProdID").val(e.ProdID);
								$("#PPB2").val(e.Price_Per_Box);
								$("#PPP2").val(e.Price_Per_Piece);
								$("#Quantity2").val(e.Quantity_Per_box);
								$("#Quantity3").val(e.Quantity_Per_Piece);
							} 
					  });

			});
		//Code For Edit EOQ
			$('body').delegate('.EOQs','click',function()
			{	
				var IdEdit = $(this).attr('idu');
				$.ajax({

						url      : "Inventory.php",
						type     : "POST",
						async    : false,
						data     :{
									editValue3 : 1,
									id        : IdEdit,
								  },
						success  : function(b)
							{
							
								document.getElementById("ProdNames").innerHTML = b.ProductName;
								$("#ProductIDz").val(b.ProdID);
                                $("#HoldingCost").val(b.Holding_Cost);
								$("#OrderCost").val(b.Ordering_Cost);
				
							} 
					  });

			});
		//Code For Edit User
            $('body').delegate('.edit2','click',function()
			{	
				var IdEdit = $(this).attr('ide');
				$.ajax({

						url      : "Inventory.php",
						type     : "POST",
						async    : false,
						data     :{
									editValue2 : 1,
									id        : IdEdit,
								  },
						success  : function(a)
							{
							
								document.getElementById("Usseerrss").innerHTML = a.FirstName;
								$("#UserId").val(a.UserId);
                                $("#uname").val(a.UserName);
								$("#pass").val(a.Password);
								$("#fn").val(a.FirstName);
								$("#ln").val(a.LastName);
								$("#cno").val(a.ContactNumber);
				
							} 
					  });

			});
		//Code For Edit Supplier
            $('body').delegate('.edit3','click',function()
			{	
				var IdEdit = $(this).attr('ide');
				$.ajax({

						url      : "Inventory.php",
						type     : "POST",
						async    : false,
						data     :{
									editValue4 : 1,
									id        : IdEdit,
								  },
						success  : function(h)
							{
							
								document.getElementById("Suppliers").innerHTML = h.SupName;
								document.getElementById("CompName").placeholder = h.SupName;
								$("#SupplierId").val(h.SupID);
                                $("#contactn").val(h.ContactNumber);
                                
							} 
					  });

			});
		//Code For Update Inventory
			$("#Update").click(function()
			{
                if( $("#Quantity2").val() == "" || $("#PPB2").val() == "" || $("#PPP2").val() == "" || $("#Datepicker2").val() == "" || $("#ProdID").val() == "" || $("#Quantity3").val() == "" ){
                    alert("Please type the things you want to update! Thank You!");
                }
                else{
                    var Quan = $("#Quantity2").val();
                    var QuanT = $("#Quantity3").val();
				var PPbox = $("#PPB2").val();
				var PPprice = $("#PPP2").val();
				var datezx = $("#Datepicker2").val();
				var PrrID = $("#ProdID").val();
				$.ajax
				({

						url     : "Inventory.php",
						type    : "POST",
						async   : false,
						data    :  {
									   Updatez : 1,
									   ProdID : PrrID,
									   QuantityPerBox : Quan,
									   QuantityPerPiece : QuanT,
									   PricePbox : PPbox,
									   PricePpiece : PPprice,
									   Datezv : datezx
							   	   },
					    success : function(ea)
					    {
					    	if(ea==0)
					    	{
					    		
					    		alert("Update Success");
					    		showdata();
					    		$(".editProduct").modal('hide');
					    	}
					    	
					    }

				});
                }
				
			});
        //Code For Update User
			$("#Update2").click(function()
			{
				var UserNamed = $("#uname").val();
				var pass = $("#pass").val();
				var contact = $("#cno").val();
				var firstname = $("#fn").val();
				var lastname = $("#ln").val();
				var UseId = $("#UserId").val();
				$.ajax({

						url     : "Inventory.php",
						type    : "POST",
						async   : false,
						data    :  {
									   UpdateUser : 1,
									   FirstName : firstname,
									   LastName : lastname,
									   ContactNumber : contact,
									   password : pass,
									   UserName : UserNamed,
									   UserId : UseId
							   	   },
					    success : function(each)
					    {
					    	if(each==0)
					    	{
					    		alert("Update Success");
					    		showdata1();
					    		$(".editUser").modal('hide');
					    	}
					    	
					    }

				});
	    	
				
			});
		//Code For Update Supplier
            $("#Update4").click(function()
			{
				var Supname = $("#Compname").val();
				var SupContact = $("#contactn").val();
				var SupId = $("#SupplierId").val();
				$.ajax({

						url     : "Inventory.php",
						type    : "POST",
						async   : false,
						data    :  {
									   UpdateSupplier : 1,
									   SupplierName : Supname,
									   Contacts : SupContact,
									   SupplierID : SupId
							   	   },
					    success : function(each1)
					    {
					    	if(each1==0)
					    	{
					    		alert("Update Success");
					    		showsupplier();
					    		$(".editSupplier").modal('hide');
					    	}
					    	
					    }

				});
	    	
				
			});
		//Code For Update EOQ
			$("#Update3").click(function()
			{
				var holdingCost =  $("#HoldingCost").val();
				var orderingCost =  	$("#OrderCost").val();
				var ProductID = $("#ProductIDz").val();
				$.ajax({

						url     : "Inventory.php",
						type    : "POST",
						async   : false,
						data    :  {
									   UpdateEOQ : 1,
									   Holding_Cost : holdingCost,
									   Ordering_Cost : orderingCost,
									   ProdID : ProductID
							   	   },
					    success : function(each)
					    {
					    	if(each==0)
					    	{
					    		alert("Update Success");
					    		showdata();
					    		$(".economic").modal('hide');
					    	}
					    	
					    }

				});
	    	
				
			});
	//Code For Search
			var Next = $(this).attr("idz");
			function showdata()
			{
            
			var tts  = $('#texttosee').val();
		  	var Sval = $('#Searchval').val();
		  	var paged = 0;
		  	Next=1;
		  	document.getElementById("hey").disabled=false;
			document.getElementById("Graph").disabled=false;
			document.getElementById("Reports").disabled=false;
			document.getElementById("Notify").disabled=false;
		  	document.getElementById("See").disabled=false;
		  	document.getElementById("Searchval").disabled=false;
		  	document.getElementById("texttosee").disabled=false;
		  	document.getElementById("AddProduct").disabled=false;
		  	document.getElementById("AddBrand").disabled=false;
		  	document.getElementById("AddUsers").disabled=true;
		  	$("#Legend").fadeIn("25000");
		  	if(tts!="")
		  		{
		  					$.ajax
		  					({
								url:"Inventory.php",
								type:"POST",
								async:false,
								data :  {
											SearchValue : 1,
											  Searchval : Sval,
											  texttosee : tts,
											  	      N : Next,
											        pgz : paged
									    },
								success : function(rea)
										{
											Showpaged1();
												if(Next == 1)
												{
													document.getElementById("Previous").disabled = true;
												}
												else if(Next != 1)
												{
													document.getElementById("Previous").disabled = false;
												}
												
											$('#showdata').html(rea);
										}
						   });
		  		}
		  	else
		  		{
		  			$('#Searchval').value="Search a Category";
			  			 	$.ajax
			  				 ({
			  					url:"Inventory.php",
								type:"POST",
								async:false,
								data    : {
										showtable : 1,
										pgz : paged,
										N : Next,

							  			},
								success : function(rez)
								{
									Showpaged();
										if(Next == 1)
										{
											document.getElementById("Previous").disabled = true;
										}
										else if(Next != 1)
										{
											document.getElementById("Previous").disabled = false;
										}
										else if(Next == "<?php echo $BA; ?>")
										{
											document.getElementById("Nextcx").disabled = true;
										}
									$('#showdata').html(rez);
								} 
		  					});

		  		}
		  		document.getElementById("Labelz").innerHTML="Manage Inventory";
		  		document.getElementById("showpagezs").style.visibility ="visible";
		  		document.getElementById("Legend").style.visibility ="visible";
		  		document.getElementById("Charts").style.visibility="hidden";
		     }
		    //Next 
			function showzxc1()
			{
		
			var tts  = $('#texttosee').val();
		  	var Sval = $('#Searchval').val();
		  	var paged = 0;
		  	Next++;
			
		  	if(tts!="")
		  		{
		  					$.ajax
		  					({
								url:"Inventory.php",
								type:"POST",
								async:false,
								data :  {
											SearchValue : 1,
											  Searchval : Sval,
											  texttosee : tts,
											  	   	  N : Next,
											        pgz : paged
									    },
								success : function(rea)
										{
											Showpaged1();
											$('#showdata').html(rea);
										}
						   });
		  		}
		  	else
		  		{
		  			$('#Searchval').value="Search a Category";
			  			 	$.ajax
			  				 ({
			  					url:"Inventory.php",
								type:"POST",
								async:false,
								data    : {
										showtable : 1,
										pgz : paged,
										N : Next,

							  			},
								success : function(rez)
								{
									Showpaged();
										if(Next == "<?php echo $BA ?>")
										{
											document.getElementById("Nextcx").disabled = true;
										}
									$('#showdata').html(rez);
								} 
		  					});

		  		}

		     }
			//Previous 

			function showzxc()
			{
			
			var tts  = $('#texttosee').val();
		  	var Sval = $('#Searchval').val();
		  	var paged = 0;
		  	Next--;
		  	if(tts!="")
		  		{
		  					$.ajax
		  					({
								url:"Inventory.php",
								type:"POST",
								async:false,
								data :  {
											SearchValue : 1,
											  Searchval : Sval,
											  texttosee : tts,
											          N : Next,
											        pgz : paged
									    },
								success : function(rea)
										{
											Showpaged1();
											if(Next == 1)
											{
												document.getElementById("Previous").disabled = true;
											}
											$('#showdata').html(rea);
										}
						   });
		  		}
		  	else
		  		{
		  			$('#Searchval').value="Search a Category";
			  			 	$.ajax
			  				 ({
			  					url:"Inventory.php",
								type:"POST",
								async:false,
								data    : {
										showtable : 1,
										pgz : paged,
										N : Next,

							  			},
								success : function(rez)
								{
									Showpaged();
										if(Next == 1)
										{
											document.getElementById("Previous").disabled = true;
										}
									$('#showdata').html(rez);
								} 
		  					});

		  		}

		     }
		   
		    $("body").delegate('.pagenum','click',function(){
		    	var Next = $(this).attr("idz");
			var tts  = $('#texttosee').val();
		  	var Sval = $('#Searchval').val();
		  	var paged = 0;
		  	if(Next < $(this).attr("idz") )
		  	{
		  		Next=parseInt($(this).attr("idz"));
		  	}
		  	if(Next > $(this).attr("idz") )
		  	{
		  		Next=parseInt($(this).attr("idz"));
		  	}

		  	if(tts!="")
		  		{
		  					$.ajax
		  					({
								url:"Inventory.php",
								type:"POST",
								async:false,
								data :  {
											SearchValue : 1,
											  Searchval : Sval,
											  texttosee : tts,
											          N : Next,
											        pgz : paged
									    },
								success : function(real)
										{
											Showpaged1();
											if(Next == 1)
											{
												document.getElementById("Previous").disabled = true;
											} 
											else if(Next != 1)
											{
												document.getElementById("Previous").disabled = false;
											}
											$('#showdata').html(real);
										}
						   });
		  		}
		  	else
		  		{
                               
		  			$('#Searchval').value="Search a Category";
			  			 	$.ajax
			  				 ({
			  					url:"Inventory.php",
								type:"POST",
								async:false,
								data    : {
										showtable : 1,
										pgz : paged,
										N : Next,

							  			},
								success : function(rezult)
								{
									Showpaged();
									if(Next == 1)
									{
										document.getElementById("Previous").disabled = true;
									} 
									else if(Next != 1)
									{
										document.getElementById("Previous").disabled = false;
									} 
									if(Next == "<?php echo $BA ?>")
									{
										document.getElementById("Nextcx").disabled = true;
									}
									$('#showdata').html(rezult);
								} 
		  					});

		  		}

		      });


		 //end
	//code for pagination
		  	function Showpaged()
		  	{

				  			$.ajax
		  				  	({
			  					url     :"Inventory.php",
								type    :"POST",
								async   :false,
								data    : {
										showpages : 1
							  			},
								success : function(regine)
									{

										$('#showpagezs').html(regine);
									} 
		  					});
		  	}

		  	function Showpaged1()
		  	{
		  		var tts  = $('#texttosee').val();
		  		var Sval = $('#Searchval').val();
				  			$.ajax
		  				  	({
			  					url     :"Inventory.php",
								type    :"POST",
								async   :false,
								data    : {
										showpages1 : 1,
										Searchval : Sval,
									    texttosee : tts
							  			},
								success : function(regines)
									{
										$('#showpagezs').html(regines);
									} 
		  					});
		  	}
		 
		  		//end
    //Code for UpdatePassword
 	   function ChangePass()
	    {
	    	var OldPass=$("#Old_Password").val();
	    	var NewPass=$("#New_Password").val();
	    	var ConfirmPass=$("#Confirm_Password").val();
	    	$.ajax
	    	({
	    		url : "Inventory.php",
	    		type : "POST",
	    		async : false,
	    		data : {
	    				 ChangePassword : 1,
	    				 Old_Password : OldPass,
	    				 New_Password : NewPass,
	    				 Confirm_Password : ConfirmPass

	    			   },
	    		success : function(suc)
	    		{
	    			if(suc==0)
	    			{
		    			alert("Password Has Been Changed");
		    			document.getElementById("Old_Password").value="";
		    			document.getElementById("New_Password").value="";
		    			document.getElementById("Confirm_Password").value="";
		    			$(".Changepassk").modal('hide');


	    			}
	    			else if(suc==1)
	    			{
	    				alert("Old Password is Incorrect");
	    			}
	    			else if(suc==2)
	    			{
	    				alert("New Password and Confirm Password doesn't Match")
	    			}
	    		}

	    	});
	    }
	//Code For Showing Users
	    var Next1  = 1;
		function showdata1()
		{
            
			$.ajax({
				url : 'Inventory.php',
				type : 'POST',
				async : false,
				data : {
					showuser : 1,
					N : Next1
				},
				success : function(perpek)
				{	
					document.getElementById("See").disabled= true;
					document.getElementById("Searchval").disabled= true;
					document.getElementById("texttosee").disabled= true;
					document.getElementById("Graph").disabled= true;
					document.getElementById("Reports").disabled= false;
					document.getElementById("Notify").disabled= true;
					document.getElementById("AddProduct").disabled= true;
					document.getElementById("AddBrand").disabled= true;
					document.getElementById("AddUsers").disabled= false;

					document.getElementById("Labelz").innerHTML="Manage User";
					document.getElementById("Legend").style.visibility="hidden";
					$('#showdata').html(perpek);
					document.getElementById("showpagezs").style.visibility ="hidden";
					document.getElementById("Charts").style.visibility="hidden";

				}
			});

		}

		var Next2 = <?php echo $nextrr ?>;
        function showsupplier()
		{
           
			$.ajax({
				url : 'Inventory.php',
				type : 'POST',
				async : false,
				data : {
					showsupplier : 1,
					N : Next2
				},
				success : function(perpek1)
				{	
					document.getElementById("See").disabled= true;
					document.getElementById("Graph").disabled= true;
					document.getElementById("Reports").disabled= false;
					document.getElementById("Notify").disabled= false;
					document.getElementById("AddProduct").disabled= true;
					document.getElementById("AddBrand").disabled= true;
					document.getElementById("AddUsers").disabled= false;
					document.getElementById("Labelz").innerHTML="Manage Supplier";
					document.getElementById("Legend").style.visibility="hidden";
					document.getElementById("showpagezs").style.visibility="hidden";
					$('#showdata').html(perpek1);
					document.getElementById("Charts").style.visibility="hidden";
				}
			});

		}
	//Code For Showing Dashboard
		function dashboard()
			{
				$.ajax({
					url : 'Inventory.php',
					type : 'POST',
					async : false,
					data : {
						dashboardz : 1
					},
					success : function(perpekto23)
					{
						document.getElementById("Labelz").innerHTML="Dashboard";
						$("#see1").css("display", "inherit");
	                    $("#see2").css("display", "none");
	                    $("#see3").css("display", "none");
	                    $("#see4").css("display", "none");
					  	document.getElementById("hey").disabled=false;
						document.getElementById("Graph").disabled=false;
						document.getElementById("Reports").disabled=false;
						document.getElementById("Notify").disabled=false;
					  	document.getElementById("See").disabled=true;
					  	document.getElementById("Searchval").disabled=true;
					  	document.getElementById("texttosee").disabled=true;
					  	document.getElementById("AddProduct").disabled=false;
					  	document.getElementById("AddBrand").disabled=false;
					  	document.getElementById("AddUsers").disabled=false;
	                    document.getElementById("")
	                    document.getElementById("Legend").style.visibility="hidden";
						document.getElementById("showpagezs").style.visibility="hidden";
						$('#showdata').html(perpekto23);
						document.getElementById("Charts").style.visibility="visible";
						CountedNormal();
						CountedNearlyExpired();
						CountedOutOfStock();
	


					}
				});

			}
	//Code For Color Coding	
		function OutOfStock()
		{
			$.ajax({

					url : 'Inventory.php',
					type : 'POST',
					async : false,
					data : {
						outOfStock : 1
					},
					success : function(oneway)
					{	
						$('#OutOfStock').html(oneway);
					}

			});
		}
		function ReorderPoint()
		{
			$.ajax({

					url : 'Inventory.php',
					type : 'POST',
					async : false,
					data : {
						reorderPoint : 1
					},
					success : function(secondway)
					{	
						$('#reorderP').html(secondway);
					}

			});
		}
		function ReorderPoint1()
		{
			$.ajax({

					url : 'Inventory.php',
					type : 'POST',
					async : false,
					data : {
						reorderPoint1 : 1
					},
					success : function(thirdway)
					{	
						$('#reorderB').html(thirdway);
					}

			});
		}
		function NearlyX()
		{
			$.ajax({

					url : 'Inventory.php',
					type : 'POST',
					async : false,
					data : {
						Nexpired : 1
					},
					success : function(fourth)
					{	
						$('#NearlyExpired').html(fourth);
					}

			});
		}
	//Code For Messaging
		function SendMes()

		{			
					var secondnum1 = $('#num2').val();
					var textmessage = $('#textmessage').val();
			$.ajax
			({

					url : 'Inventory.php',
					type : 'POST',
					async : false,
					data : {
							send : 1,
							secondnum : secondnum1,
							Message : textmessage
						},
					success : function(oneD)
						{	
							document.getElementById("num2").value="";
							document.getElementById("textmessage").value="";
							document.getElementById("names").value="";
							$("#messenger").modal('hide');
							alert("Message Sent");
						}
			});
		}
	//Code for Showing Graph
			function Graphing()
			{
				$.ajax({
					url : 'Inventory.php',
					type : 'POST',
					async : false,
					data : {
						Graphs : 1
					},
					success : function(perpektz)
					{	
						document.getElementById("Labelz").innerHTML="Graph reports";
						$('#showdata').html(perpektz);
						$("#Legend").fadeOut("25000");
						document.getElementById("showpagezs").style.visibility ="hidden";
					}
				});

			}
	//Code for Dashboard Count
		function CountedNormal()
		{
			$.ajax
			({
					url : 'Inventory.php',
						type : 'POST',
						async : false,
						data : {
							NormalInvent : 1
						},
						success : function(ColorCode)
						{	
							document.getElementById("NICount").innerHTML=ColorCode.CountedNormal;
						}

			});
		}
		function CountedOutOfStock()
		{
			$.ajax
			({
					url : 'Inventory.php',
						type : 'POST',
						async : false,
						data : {
							OutOfStocks  : 1
						},
						success : function(ColorCodeOutOfStock)
						{	
							document.getElementById("OOSCount").innerHTML=ColorCodeOutOfStock.CountedOutOFStocks;
						}

			});
		}
		function CountedNearlyExpired()
		{
			$.ajax
			({
					url : 'Inventory.php',
						type : 'POST',
						async : false,
						data : {
							NearlyExpiree : 1
						},
						success : function(ColorCodeNearlyExpire)
						{	
							document.getElementById("NearlyEx").innerHTML=ColorCodeNearlyExpire.CountedNearly;
						}

			});
		}
	//Code for Dropdown Suppliers/Name
	//Code for selecting numbers
		function Selects()
		{
			$.ajax({

						url : 'Inventory.php',
						type : 'POST',
						async : false,
						data : {
								mynumbers : 1,
							},
						success : function(namedz)
							{	
								$("#messagela").html(namedz);
							}

			});
		}
        function supname(){
            var category = $("#num2").val();
             $.ajax({
					url : 'Inventory.php',
					type : 'POST',
					async : false,
					data : {
							supnumber : 1,
                             catname: category
						},
					success : function(yy)
						{	
                            $("#names").val(yy.SupName);
				        }

			     });
            
        }
        function supsup(){
            document.getElementById("num2").value = "";
        }
        function venus(){
            
        }
    //Code for Dates
	    function Datela()
	    {
	    	var chooser = $("#Datepicker3").val();
	    	$.ajax({

	    		url : "Inventory.php",
	    		type : "POST",
	    		async : false,
	    		data:{
	    			Dated : 1,
	    			Datec : chooser
	    		},
	    		success : function(resultc)
	    		{


	    				if(chooser == "")
	    				{
	    					document.getElementById("printed").disabled=true;
	    					document.getElementById("replace").style.display="none";
	    					document.getElementById("replace").value="";
	    				}
	    				else if (chooser != "")
	    				{
		    				document.getElementById("printed").disabled=false;
		    				document.getElementById("replace").style.display="block";
	    					$("#replace").html(resultc);
	    				}
	    			
	    		}
	    	});
	    }
	//Code For print
		function Printers()
		{
			var dates=$("#Datepicker3").val();
			var TransacId=$("#transacnumber").val();
			$.ajax({

				url:"Inventory.php",
				type:"POST",
				async:false,
				data:{
					printReport:1,
					datos:dates,
					TransID:TransacId
				},
				success : function(Rezx)
				{
					document.getElementById("TransactionalID").innerHTML = Rezx.TransactionID; 
					Modaldal();
					Printers1();
				}

			});
		}
		function Printers1()
		{
			var dates=$("#Datepicker3").val();
			var TransacId=$("#transacnumber").val();
			$.ajax({

				url:"Inventory.php",
				type:"POST",
				async:false,
				data:{
					printReport1:1,
					datees:dates,
					TransaID:TransacId
				},
				success : function(Rezx3)
				{
					document.getElementById("FinalCT").innerHTML=Rezx3.CashTendered;
					document.getElementById("FinalTP").innerHTML=Rezx3.TotalSales;
					document.getElementById("FinalDI").innerHTML=Rezx3.Discounted;
					document.getElementById("FinalCH").innerHTML=Rezx3.Change;
					document.getElementById("SeniorID").innerHTML=Rezx3.SeniorID;
				}

			});
		}
		function Modaldal()
		{
			var dates=$("#Datepicker3").val();
			var TransacId=$("#transacnumber").val();
			$.ajax({

				url:"Inventory.php",
				type : "POST",
				async : false,
				data : {
					ETOWS : 1,
					transID:TransacId,
					Datos:dates
				},
				success : function(rezzult)
				{
					$('.Bodybody').html(rezzult);
					$('#bodybody1').html(rezzult);
				}	

			});
		}
		function ImessageMoAko()
		{
			$.ajax({

				url:"Inventory.php",
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
</html>