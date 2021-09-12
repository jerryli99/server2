<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/icon2.jpg">
    <link rel="apple-touch-icon" href="images/icon2.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Terrapin Exchange - My Product</title>
    <style>
    * {
        box-sizing: border-box;
    }

    body, html {
        font-family: 'Courier New', Courier, monospace;
        /* font-family: Arial, Helvetica, sans-serif; */
        /* Arial, Helvetica, sans-serif*/
        height: 100%;
        margin: 0;
        width: 100%;
        padding: 0;
        overflow-x: visible;  
        background-color: rgb(243, 246, 247);
        scroll-behavior: smooth;
    }

    .big-container{
        padding: 1rem;
    }

    
    h2{
        background-color: rgb(19, 19, 19);
        color:rgb(255, 255, 255);
        border-radius: 15px;
        /* box-shadow: 0 10px 20px 0 rgba(0, 0, 0, 0.932); */
        text-align:center; 
        font-size: 140%;
        padding: 8px 10px;
        /* margin-right: 2.5rem; */
        margin-top: 4%;
    }

    .container {  
        background-color: white;
        border: 1px solid black;
        border-radius: 15px;
        padding: 1rem;
        display: flex;
        justify-content: space-evenly;
        flex-wrap: wrap;  
    }

    .item {
        padding: 5px;
        margin: 5px;
        /* background-color: rgba(111,41,97,.3);
        border: 2px solid rgba(111,41,97,.5);  */
    }

    .container .item img{
        width:200px;
        height:180px;
        object-fit: cover;
        border-radius: 15px;
    }

    a{
        border: 1px solid black;
        border-radius: 15px;
        background-color: black;
        color: white;
        padding: 10px 20px;
        margin: 10px;
        margin-right: 20px;
        text-decoration: none;
        float: right;
        box-shadow:0 5px  8px 0 rgb(36, 36, 36);
    }

    a:hover{
        background-color: gray;
    } 

    p{
        text-align: left;
        hyphens: auto;
        font-size: 95%;
    }

    .alert-box{
        text-align: center;
    }
    </style>
</head>
<body>

<?php
    session_start();
//this file is in server 2: product.jerryalpa.com
//reason: I can make a product api using this server with API keys.
    date_default_timezone_set("America/New_York");
    $time = time();
    require_once('connectDB.php');
    require 'vendor/autoload.php';

    use Aws\S3\S3Client;
    use Aws\S3\Exception\S3Exception;

    if(isset($_POST['submit'])){  
        $_SESSION['this_user'] = $_POST['this_user'];
        $userName = $_POST['this_user'];
        $category = $_POST['category'];
        $location = $_POST['location'];
        $description = $_POST['description'];
        $productID = $_POST['productID'];
	//echo "Submitted";
        //chekc if user posted an image

	//I am just lazy to do the logic here, so here is a quick check
        //to see if a file is selected or not
/*	if(!isset($_FILES['file']['name'])){
	    header("Location: https://developer.jerryalpa.com/exchangeAdd");
	    exit();
	}
*/
//	echo "posted file is->" . $_POST['file'];
        if(isset($_FILES['file']['name'])){
	  //echo "Submitted image";
            //only allow user to upload image, not txt.
            $allowed = array('gif', 'png', 'jpg');
            //get the file type 
            $explode = explode('.', $_FILES['file']['name']);
            $extension = end($explode);
		//echo "OK before file size";
            //the file size for 1 file cannot exceed 10 MB. 
            if($_FILES['file']['size'] > 10*1024*1024){
                header("Location: https://developer.jerryalpa.com/exchangeAdd");
		exit();
            }else if(in_array($extension, $allowed) == false){
                header("Location: https://developer.jerryalpa.com/exchangeAdd");
		exit();
            }else{
	//	echo "in the aws s3 area start";
                //this section is to store the images to the cloud and image name to Database
                $bucketName = 'test1-1';
                $IAM_KEY = 'AKIAZXTHLVQD7OD6S4VY';
		$IAM_SECRET = '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';
		//CONNECT TO AWS S3
                try{
                    $s3 = S3Client::factory(
                        array(
                            'credentials' => array(
                                'key' => $IAM_KEY,
                                'secret' => $IAM_SECRET
                            ),
                            'version' => 'latest',
                            'region'  => 'us-east-1'
                        )
                    );
                }catch(Exception $e){
			echo "in the exception";
                    die("Error: " . $e->getMessage());
                }

	//	echo "Before keyName";
                //Generate a string for the keyName.
                $keyName = "{$_POST['this_user']}/product/" . basename($_FILES['file']['name']);
                $pathInS3 = 'https://s3.us-east-1.amazonaws.com/' . $bucketName . '/' . $keyName;
                //Add file to S3 now->
                try{
                $file = $_FILES['file']['tmp_name'];
                $s3->putObject(
                    array(
                        'Bucket' => $bucketName,
                        'Key' => $keyName,
                        'SourceFile' => $file,
                        'StorageClass' => 'REDUCED_REDUNDANCY',
                        'ContentType' => ''
                    )
                );
                }catch(S3Exception $e){
                    header("Location: https://product.jerryalpa.com/productPost.php");
                    die('Error: ' . $e->getMessage());
                }catch(Exception $e){
                    header("Location: https://product.jerryalpa.com/productPost.php");
                    die('Error: ' . $e->getMessage());
                }

	//	echo "before db store";
                $fileName = basename($keyName, '.' . $extension); 
                // store file name to database userFiles
                $stmt = $connect->prepare("INSERT INTO userFiles (userName, fileName, fileType, time) VALUES(?,?,?,?)");
                $stmt->bind_param("ssss", $userName, $fileName, $extension, $time);
                $execval = $stmt->execute();
                if(!$execval){
                    echo 'Opps! File is uploaded to the cloud, but cannot trace...';
                }
	    } //end of else

	    //end of if statement checking if $_FILES isset()
	  }else{
                header("Location: https://developer.jerryalpa.com/exchangeAdd");
                exit();
           }
	  //  echo "Going to store to Db";
            // store file name to database userFiles
            $stmt = $connect->prepare("INSERT INTO products (userName, category, location, description, productID, postedDate) VALUES(?,?,?,?,?,?)");
           //echo "prepare";
	    $stmt->bind_param("ssssss", $userName, $category, $location, $description, $productID, $time);
	//echo "binding";
            $execval = $stmt->execute();
          //  echo "executed";
            if(!$execval){
                echo 'Opps! Cannot upload' . $stmt->error;
            }else{
                echo "<div style=\"text-align:center;\"><h3>Uploaded Successfully!</h3></div>";
            }

        //end of the if statement after checking $_POST['submit']
    }else{
        header("Location: https://developer.jerryalpa.com/login");
    }
?>

<div class="big-container">
    <h2>My Exchange Post</h2>
    <div class="container" id="container">
        <div class="item">
        <?php
  //       echo $pathInS3;
         echo "<img src=\"{$pathInS3}\">";
        ?>
        </div>
        <div class="item">
        <!-- display user post info -->
        <?php
        if(isset($_POST['submit'])){
            echo "<p>" . 
                 "My UserName : " . $_POST['this_user']   . "<hr><br>" .
                 "My Category : " . $_POST['category']    . "<hr><br>" .
                 "My Location : " . $_POST['location']    . "<hr><br>" .
                 "My Text     : " . $_POST['description'] . "<hr><br>" .
                 "Exchange ID : " . $_POST['productID']   . "<hr><br>" .
                 "Posted Date : " . date("Y-m-d H:i:s", $time) 
                 . "</p>";
        }

        ?>
        </div>
    </div>
</div>
<a href="https://developer.jerryalpa.com/manager">
Back <i class="fa fa-arrow-circle-left"></i></a>
<br>
<br>
<br>
<br>
<br>
<br>
</body>
</html>


