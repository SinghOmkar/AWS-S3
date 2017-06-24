<?php
header('Content-Type: text/plain; charset=utf-8');
//including SDK to script
require 'vendor/autoload.php';
use Aws\S3\S3Client;

//initiate S3 client
$s3Client	=	new S3Client([
		'version'		=>	'latest',
		'region'		=>	'ap-south-1',
		'credentials'	=>	[
				'key'	=>	AWS_KEY,	
				'secret'=>	AWS_SECRET
		]
]);
$bucket	=	BUCKET_NAME;
//upload object
$result	=	$s3Client->putObject([
	'ACL'	=>	'public-read',
	'Bucket'=>	$bucket,
	'Key'	=>	'myvideo.mp4',
	'SourceFile'	=>	'myvideo.mp4'
]);
//display object URL
echo $result['ObjectURL'];
