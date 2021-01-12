### Example

```php
// 20 MEGA BYTES
$max_file_size = 20971520;

$log_dir = "./";

$logRotate = new LogRotate($max_file_size, $log_dir);
$logRotate->write("log texts");
```