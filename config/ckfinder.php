<?php

$config = array();

$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$basePath = trim(str_replace('/index.php', '', $scriptName), '/');
$requestHost = $_SERVER['HTTP_HOST'] ?? null;
$requestScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$requestBaseUrl = $requestHost ? $requestScheme . '://' . $requestHost . ($basePath ? '/' . $basePath : '') : null;
$ckfinderBaseUrl = rtrim(env('CKFINDER_BASE_URL', $requestBaseUrl ?: config('app.url')), '/') . '/data/';

$config['loadRoutes'] = false;
$config['authentication'] = '\App\Http\Middleware\ckFinderAuthentication';

$config['licenseName'] = 'localhost';
$config['licenseKey']  = 'EY8YRUDXTXLK9UVYTWWG4DA337AG1';

$config['privateDir'] = array(
    'backend' => 'laravel_cache',
    'tags'    => 'ckfinder/tags',
    'cache'   => 'ckfinder/cache',
    'thumbs'  => 'ckfinder/cache/thumbs',
    'logs'    => array(
        'backend' => 'laravel_logs',
        'path'    => 'ckfinder/logs',
    ),
);

$config['images'] = array(
    'maxWidth'  => 2000,
    'maxHeight' => 12000,
    'quality'   => 100,
);

$config['backends']['laravel_cache'] = array(
    'name' => 'laravel_cache',
    'adapter' => 'local',
    'root' => storage_path('framework/cache'),
);

$config['backends']['laravel_logs'] = array(
    'name' => 'laravel_logs',
    'adapter' => 'local',
    'root' => storage_path('logs'),
);

$config['backends']['default'] = array(
    'name' => 'default',
    'adapter' => 'local',
    'baseUrl' => $ckfinderBaseUrl,
    'root' => public_path('/data/'),
    'chmodFiles' => 0777,
    'chmodFolders' => 0755,
    'filesystemEncoding' => 'UTF-8',
);

$config['defaultResourceTypes'] = 'Images,Audios';

$config['resourceTypes'][] = array(
    'name' => 'Images',
    'directory' => 'images',
    'maxSize' => 0,
    'allowedExtensions' => 'bmp,gif,jpeg,jpg,png,svg,webp',
    'deniedExtensions' => '',
    'backend' => 'default',
);

$config['resourceTypes'][] = array(
    'name' => 'Audios',
    'directory' => 'audio',
    'maxSize' => '50M',
    'allowedExtensions' => 'aac,aiff,flac,m4a,mid,mp3,mp4,mpeg,mpg,ogg,wav,wma',
    'deniedExtensions' => '',
    'backend' => 'default',
);

$config['roleSessionVar'] = 'CKFinder_UserRole';
$config['accessControl'][] = array(
    'role' => '*',
    'resourceType' => '*',
    'folder' => '/',
    'FOLDER_VIEW' => true,
    'FOLDER_CREATE' => true,
    'FOLDER_RENAME' => true,
    'FOLDER_DELETE' => true,
    'FILE_VIEW' => true,
    'FILE_UPLOAD' => true,
    'FILE_RENAME' => true,
    'FILE_DELETE' => true,
    'IMAGE_RESIZE' => true,
    'IMAGE_RESIZE_CUSTOM' => true,
);

$config['overwriteOnUpload'] = false;
$config['checkDoubleExtension'] = true;
$config['disallowUnsafeCharacters'] = false;
$config['secureImageUploads'] = true;
$config['checkSizeAfterScaling'] = true;
$config['htmlExtensions'] = array('html', 'htm', 'xml', 'js');
$config['hideFolders'] = array('.*', 'CVS', '__thumbs');
$config['hideFiles'] = array('.*');
$config['forceAscii'] = true;
$config['xSendfile'] = false;
$config['debug'] = false;
$config['plugins'] = array();
$config['cache'] = array(
    'imagePreview' => 24 * 3600,
    'thumbnails' => 24 * 3600 * 365,
);
$config['tempDirectory'] = sys_get_temp_dir();
$config['sessionWriteClose'] = true;
$config['csrfProtection'] = true;

return $config;
