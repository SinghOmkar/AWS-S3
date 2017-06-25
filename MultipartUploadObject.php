<?php
/*This script create object by multipart upload
using "Upload a File in Multiple Parts Using the PHP SDK Low-Level API"
for more info visit http://docs.aws.amazon.com/AmazonS3/latest/dev/LLuploadFilePHP.html*/

header('Content-Type: text/plain; charset=utf-8');
require 'vendor/autoload.php';
use Aws\S3\S3Client;

//Object key
$keyname	=	'myvideo.mp4';

//Path to and Name of the File to Upload
$fileName	=	'myvideo.mp4';

//initiate s3client
$s3Client	=	new S3Client([
		'version'		=>	'latest',
		'region'		=>	'ap-south-1',
		'credentials'	=>	[
				'key'	=>	AWS_KEY,	
				'secret'=>	AWS_SECRET
		]
]);

//Create a new multipart upload and get the upload ID.
$result		=	$s3Client->createMultipartUpload(array(
	'Bucket'	=>	BUCKET,
	'Key'		=>	$keyname,
	'ACL'		=>	'public-read'
));

//get uploadId
$uploadId	=	$result['UploadId'];

//upload file in parts
try{
	//open file in read mode
	$file	=	fopen($fileName, 'r');
	$parts 	=	array();
	$partNumber	=	1;
	while (!feof($file)) {
		echo "Uploading part {$partNumber} of {$fileName}.\n";
		$result	=	$s3Client->uploadPart(array(
			'Bucket'	=>	BUCKET,
			'Key'		=>	$keyname,
			'UploadId'	=>	$uploadId,
			'PartNumber'=>	$partNumber,
			'Body'		=>	fread($file, 5*1024*1024),
		));
		//print_r($result);
		$parts[] 	=	array(
				'ETag'		=>	$result['ETag'],
				'PartNumber'=>	$partNumber++,	
			);
	}
	//close file
	fclose($file);
}
catch(S3Exception $e){
	//abort uploading if exception occured
	$result	=	$s3Client->abortMultipartUpload(array(
			'Bucket'	=>	BUCKET,
			'Key'		=>	$keyname,
			'UploadId'	=>	$uploadId,
		));
	echo "Upload of $fileName failed.\n";
}

$result	=	$s3Client->completeMultipartUpload(array(
	'Bucket'	=>	BUCKET,
	'Key'		=>	$keyname,
	'MultipartUpload'	=>	array(
			'Parts'	=>	$parts,
		),
	'UploadId'	=>	$uploadId,
	));

$uri	=	$result['Location'];
echo "Uploaded $fileName to $uri.";
