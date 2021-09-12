<?php
//header('Content-type: application/json');
/*$_GET['set'] = 2;
$content = array('we','are','array');
$_GET['set1'] = $content[0] . $content[1] . $content[2];
$data = 'Hi' . 'set ' . $_GET['set'] . $_GET['set1'];
echo json_encode($data);
*/
require_once('connectDB.php');
$sql_products = "SELECT * FROM products";
$result_products = $connect->query($sql_products);
if(mysqli_num_rows($result_products) > 0){
  while($row1 = mysqli_fetch_assoc($result_products)){
    $userNames[] = $row1['userName'];
    $categories[] = $row1['category'];
    $locations[] = $row1['location'];
    $descriptions[] = $row1['description'];
    $productIDs[] = $row1['productID'];
    $postedDates[] = $row1['postedDate'];
  }
}
/*
echo '<h2>The products array</h2>';
echo '<pre>';
print_r($userNames);
print_r($categories);
print_r($locations);
print_r($descriptions);
print_r($productIDs);
print_r($postedDates);
echo '</pre>';*/
?>


<?php
//this section is to query the userFiles (for products)
$sql_userFiles = "SELECT * FROM userFiles";
$result_userFiles = $connect->query($sql_userFiles);
if(mysqli_num_rows($result_userFiles) > 0){
  while($row2 = mysqli_fetch_assoc($result_userFiles)){
    $fileNames[] = 'https://test1-1.s3.amazonaws.com/'. $row2['userName'] . '/product/' .$row2['fileName'] . '.' . $row2['fileType'];
    $fileTypes[] = $row2['fileType'];
    $uploadTimes[] = $row2['time'];
  }
}
/*
echo '<h2>The userFiles array</h2>';
echo '<pre>';
print_r($userNames_file);
print_r($fileNames);
print_r($fileTypes);
print_r($uploadTimes);
echo '</pre>';*/
?>

<?php
$connect->close();
$data = array_map(null, $userNames, $categories, $locations, $descriptions, $productIDs, $postedDates, $fileNames, $fileTypes, $uploadTimes);
echo json_encode($data);
$userNames[] = array();
$categories[] = array();
$locations[] = array();
$descriptions[]=array();
$productIDs[] = array();
$postedDates[]=array();
$fileNames[]=array();
$fileTypes[]=array();
$uploadTimes[]=array();
?>

