# MyFTP

> A PHP small class for FTP handling.

## Usage

```php
$ftp_settings array(
  'hostname' => 'ftp.remote-server.com',
  'username' => 'john',
  'password' => '123456',
  // 'port' => 21,
  // 'passive' => TRUE
);

// Initializing
$ftp = new MyFTP($ftp_settings);
$is_connected = $ftp->connect();

// Upload file
$filepath_local = 'local_dir/local_file.zip';
$filepath_remote = 'remote_dir/remote_file.zip';

// Checks if it connected and logged
if ($is_connected) {
  // Upload file
  $ftp->upload($filepath_local, $filepath_remote);
  $ftp->close();
} else {
  die("Couldn't connect to FTP Server");
}
```

## Licence
MIT licence

© 2016 [José Luis Quintana](http://quintana.io)
