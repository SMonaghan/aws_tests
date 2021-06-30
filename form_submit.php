<?php
include 'dynamic.php';
/**
 * Copyright 2010-2019 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * This file is licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License. A copy of
 * the License is located at
 *
 * http://aws.amazon.com/apache2.0/
 *
 * This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR
 * CONDITIONS OF ANY KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations under the License.
 *
 * If you need more information about configurations or implementing the sample code, visit the AWS docs:
 * https://aws.amazon.com/developers/getting-started/php/
 *
 */

require 'vendor/autoload.php';

use Aws\SecretsManager\SecretsManagerClient; 
use Aws\Exception\AwsException;

/**
 * In this sample we only handle the specific exceptions for the 'GetSecretValue' API.
 * See https://docs.aws.amazon.com/secretsmanager/latest/apireference/API_GetSecretValue.html
 * We rethrow the exception by default.
 *
 * This code expects that you have AWS credentials set up per:
 * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html
 */

// Create a Secrets Manager Client 
$client = new SecretsManagerClient(['profile' => 'default','version' => '2017-10-17', 'region' => 'us-east-1']);
// Decrypts secret using the associated KMS CMK.
// Depending on whether the secret is a string or binary, one of these fields will be populated.
$cmd = "aws secretsmanager get-secret-value --secret-id ${secretName} --region us-east-1";
$result = shell_exec($cmd);
$result = json_decode($result, true);

if (isset($result['SecretString'])) {
    $secret = $result['SecretString'];
} else {
    $secret = base64_decode($result['SecretBinary']);
}
$password = json_decode($secret, true)['password'];
$connection = null;
try{
$connection = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
$connection->exec("set names utf8");
}catch(PDOException $exception){
echo "Connection error: " . $exception->getMessage();
}

function saveData($name, $email, $message){
global $connection;
$query = "INSERT INTO test(name, email, message) VALUES( :name, :email, :message)";

$callToDb = $connection->prepare( $query );
$name=htmlspecialchars(strip_tags($name));
$email=htmlspecialchars(strip_tags($email));
$message=htmlspecialchars(strip_tags($message));
$callToDb->bindParam(":name",$name);
$callToDb->bindParam(":email",$email);
$callToDb->bindParam(":message",$message);

if($callToDb->execute()){
return '<h3 style="text-align:center;">We will get back to you very shortly!  Thanks!</h3>';
}
}
echo "<html>";
echo "<head>";
echo '  <title>Octank Demo - Submit</title>';
echo '  <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon.png">';
echo '  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">';
echo '  <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">';
echo '  <link rel="manifest" href="/site.webmanifest">';
echo '  <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">';
echo '  <meta name="msapplication-TileColor" content="#da532c">';
echo '  <meta name="theme-color" content="#ffffff">';
echo "</head>";
echo "<body>";
if( isset($_POST['submit'])){
    $name = htmlentities($_POST['name']);
    $email = htmlentities($_POST['email']);
    $message = htmlentities($_POST['message']);

    //then you can use them in a PHP function.
    $result = saveData($name, $email, $message);
    echo $result;
} else{
    echo '<h3 style="text-align:center;">A very detailed error message ( ͡° ͜ʖ ͡°)</h3>';
}
echo '  <form action="index.html" class="alt" method="POST">';
echo '    <input class="alt" value="Go Back" name="submit" type="submit">';
echo '  </form>';
echo "</body>";
echo '</html>';
$myfile = fopen("log.log", "a") or die("Unable to open file!");
date_default_timezone_set("UTC");
$txt = date("Y-m-d H:i:s") . " Inserting Name: ${name} Email: ${email} with Message: ${message} into database\n";
fwrite($myfile, $txt);
fclose($myfile);
?>
