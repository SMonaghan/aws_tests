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

if ( getenv('FARGATE') == 'TRUE' ){
    $html_title = 'Octank Demo - Fargate (Get Datav1)';
} else {
    $html_title = 'Octank Demo - EC2 (Get Data)';
}
echo "<html>";
echo "<head>";
echo "  <title>" . $html_title . "</title>";
echo '  <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon.png">';
echo '  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">';
echo '  <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">';
echo '  <link rel="manifest" href="/site.webmanifest">';
echo '  <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">';
echo '  <meta name="msapplication-TileColor" content="#da532c">';
echo '  <meta name="theme-color" content="#ffffff">';
echo "</head>";
echo "<body>";
if (isset($result['SecretString'])) {
        $secret = $result['SecretString'];
} else {
        $secret = base64_decode($result['SecretBinary']);
}
$password = json_decode($secret, true)['password'];
$connection = new mysqli($host, $username, $password, $db_name);

function saveData(){
        global $connection;
        $query = "SELECT * FROM test";

        $result = $connection->query($query);

        if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                        echo "id: " . $row["id"]. " - Name: " . $row["name"]. " Email: " . $row["email"] . " Message: " . $row["message"] . "<br>";
                }
        } else {
                echo "<b>0 results</b>";
        }
}

echo '  <form action="/" class="alt" method="POST">';
echo '    <input class="alt" value="Go Back" name="submit" type="submit">';
echo '  </form>';
echo "</body>";
$result = saveData();
echo $result;
echo "</html>";
