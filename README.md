fsocker
=======

This repository is a PHP fsockopen Class Wrapper that handles HTTP Requests without the need of getting response.

=======

Sample Usage
=======

```
$fsock = new fsocker(array(
	"method" => "POST",
	"headers" => array("X-Custom-Header: header.value")
));

//Single Request
$fsock->execute("http://www.example.com/path/to/execute/request/");

//Multiple Requests
$fsock->execute("http://www.example.com/path/to/execute/request/1");
$fsock->execute("http://www.example.com/path/to/execute/request/2");
$fsock->execute("http://www.example.com/path/to/execute/request/3");
....

```
