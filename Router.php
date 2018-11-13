<?php
class Router
{
  private $request;
  private $supportedHttpMethods = array(
    "GET",
    "POST"
  );
  function __construct(IRequest $request)
  {
   $this->request = $request;
  }
  function __call($name, $args)
  {
    list($route, $method) = $args;
    if(!in_array(strtoupper($name), $this->supportedHttpMethods))
    {
      $this->invalidMethodHandler();
    }
    $this->{strtolower($name)}[$this->formatRoute($route)] = $method;
  }
  /**
   * Removes trailing forward slashes from the right of the route.
   * @param route (string)
   */
  private function formatRoute($route)
  {
    $result = rtrim($route, '/');
    if ($result === '')
    {
      return '/';
    }
    return $result;
  }
  private function invalidMethodHandler()
  {
    header("{$this->request->serverProtocol} 405 Method Not Allowed");
  }
  private function defaultRequestHandler()
  {
    header("{$this->request->serverProtocol} 404 Not Found");
  }
  /**
   * Resolves a route
   */
  function resolve()
  {
    $methodDictionary = $this->{strtolower($this->request->requestMethod)};
    $formatedRoute = $this->formatRoute($this->request->requestUri);
	if (isset($methodDictionary[$formatedRoute])) {
		$method = $methodDictionary[$formatedRoute];
	} else {
		$method = null;
	}
	
	$headerList = [];
		foreach ($_SERVER as $name => $value) {
			if (preg_match('/^HTTP_/',$name)) {
				// convert HTTP_HEADER_NAME to Header-Name
				$name = strtr(substr($name,5),'_',' ');
				$name = ucwords(strtolower($name));
				$name = strtr($name,' ','-');
				// add to list
				$headerList[$name] = $value;
			}
	}
	
	ob_start();
	print_r($headerList);
	echo "\n------------------------------------------------------------------";
	error_log(ob_get_clean(), 4);
	
    if(is_null($method))
    {
      $this->defaultRequestHandler();
      return;
    }
    echo call_user_func_array($method, array($this->request));
  }
  function __destruct()
  {
    $this->resolve();
  }
}