<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.log.Traceable',
    'peer.Header',
    'peer.http.HttpConnection',
    'webservices.rest.RestRequest',
    'webservices.rest.RestResponse',
    'webservices.rest.RestFormat',
    'webservices.rest.RestMarshalling',
    'webservices.rest.RestException'
  );

  /**
   * REST client
   *
   * @test    xp://net.xp_framework.unittest.webservices.rest.RestClientTest
   * @test    xp://net.xp_framework.unittest.webservices.rest.RestClientSendTest
   * @test    xp://net.xp_framework.unittest.webservices.rest.RestClientExecutionTest
   */
  class RestClient extends Object implements Traceable {
    protected $connection= NULL;
    protected $cat= NULL;
    protected $serializers= array();
    protected $deserializers= array();
    protected $marshalling= NULL;

    /**
     * Creates a new Restconnection instance
     *
     * @param   var base default NULL
     */
    public function __construct($base= NULL) {
      if (NULL !== $base) $this->setBase($base);
      $this->marshalling= new RestMarshalling();
    }

    /**
     * Set trace
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }

    /**
     * Sets base
     *
     * @param   var base either a peer.URL or a string
     */
    public function setBase($base) {
      $this->setConnection(new HttpConnection($base));
    }

    /**
     * Sets base and returns this connection
     *
     * @param   var base either a peer.URL or a string
     * @return  self
     */
    public function withBase($base) {
      $this->setBase($base);
      return $this;
    }

    /**
     * Get base
     *
     * @return  peer.URL
     */
    public function getBase() {
      return $this->connection ? $this->connection->getURL() : NULL;
    }

    /**
     * Sets HTTP connection
     *
     * @param   peer.http.HttpConnection connection
     */
    public function setConnection(HttpConnection $connection) {
      $this->connection= $connection;
    }

    /**
     * Set connect timeout
     *
     * @param   float timeout
     * @throws  lang.IllegalStateException if no connection is set
     */
    public function setConnectTimeout($timeout) {
      if (NULL === $this->connection) {
        throw new IllegalStateException('No connection set');
      }

      $this->connection->setConnectTimeout($timeout);
    }

    /**
     * Retrieve connect timeout
     *
     * @return  float
     * @throws  lang.IllegalStateException if no connection is set
     */
    public function getConnectTimeout() {
      if (NULL === $this->connection) {
        throw new IllegalStateException('No connection set');
      }

      return $this->connection->getConnectTimeout();
    }

    /**
     * Set timeout
     *
     * @param   int timeout
     * @throws  lang.IllegalStateException if no connection is set
     */
    public function setTimeout($timeout) {
      if (NULL === $this->connection) {
        throw new IllegalStateException('No connection set');
      }

      $this->connection->setTimeout($timeout);
    }

    /**
     * Get timeout
     *
     * @return  int
     * @throws  lang.IllegalStateException if no connection is set
     */
    public function getTimeout() {
      if (NULL === $this->connection) {
        throw new IllegalStateException('No connection set');
      }

      return $this->connection->getTimeout();
    }

    /**
     * Sets deserializer
     *
     * @param   string mediaType e.g. "text/xml"
     * @param   webservices.rest.Deserializer deserializer
     */
    public function setDeserializer($mediaType, $deserializer) {
      $this->deserializers[$mediaType]= $deserializer;
    }

    /**
     * Returns a deserializer
     *
     * @param   string contentType
     * @param   bool throw
     * @return  webservices.rest.RestDeserializer
     * @throws  lang.IllegalArgumentException
     */
    public function deserializerFor($contentType, $throw= TRUE) {
      $mediaType= substr($contentType, 0, strcspn($contentType, ';'));
      if (isset($this->deserializers[$mediaType])) {
        return $this->deserializers[$mediaType];
      } else {
        $format= RestFormat::forMediaType($mediaType);
        if (RestFormat::$UNKNOWN->equals($format)) {
          if ($throw) {
            throw new IllegalArgumentException('No deserializer for "'.$contentType.'"');
          } else {
            return NULL;
          }
        }
        return $format->deserializer();
      }
    }

    /**
     * Sets deserializer
     *
     * @param   string mediaType e.g. "text/xml"
     * @param   webservices.rest.Serializer serializer
     */
    public function setSerializer($mediaType, $serializer) {
      $this->serializers[$mediaType]= $serializer;
    }

    /**
     * Returns a serializer
     *
     * @param   string contentType
     * @param   bool throw
     * @return  webservices.rest.RestSerializer
     * @throws  lang.IllegalArgumentException
     */
    public function serializerFor($contentType, $throw= TRUE) {
      $mediaType= substr($contentType, 0, strcspn($contentType, ';'));
      if (isset($this->serializers[$mediaType])) {
        return $this->serializers[$mediaType];
      } else {
        $format= RestFormat::forMediaType($mediaType);
        if (RestFormat::$UNKNOWN->equals($format)) {
          if ($throw) {
            throw new IllegalArgumentException('No serializer for "'.$contentType.'"');
          } else {
            return NULL;
          }
        }
        return $format->serializer();
      }
    }

    /**
     * Execute a request
     *
     * <code>
     *   $client->execute(new RestRequest('/', HttpConstants::GET));
     * </code>
     *
     * @param   var t either a string or a lang.Type - response type, defaults to webservices.rest.RestResponse
     * @param   webservices.rest.RestRequest request
     * @return  webservices.rest.RestResponse
     * @throws  lang.IllegalStateException if no connection is set
     */
    public function execute($t, $request= NULL) {
      if (1 === func_num_args()) {      // Overloaded version with single argument
        $request= $t;
        $type= NULL;
      } else if (is_string($t)) {       // Overloaded version with string type
        $type= Type::forName($t);
      } else if ($t instanceof Type) {  // Overloaded version with Type instance
        $type= $t;
      } else {
        throw new IllegalArgumentException('Given type is neither a Type nor a string, '.xp::typeOf($request).' given');
      }

      if (!$request instanceof RestRequest) {
        throw new IllegalArgumentException('Given request is not a RestRequest, '.xp::typeOf($request).' given');
      }

      if (NULL === $this->connection) {
        throw new IllegalStateException('No connection set');
      }

      $send= $this->connection->create(new HttpRequest());
      $send->addHeaders($request->headerList());
      $send->setMethod($request->getMethod());
      $send->setTarget($request->getTarget($this->connection->getUrl()->getPath('/')));

      // Compose body
      // * Serialize payloads using the serializer for the given mimetype
      // * Use bodies as-is, e.g. file uploads
      // * If no body and no payload is set, use parameters
      if ($request->hasPayload()) {
        $send->setParameters(new RequestData($this->serializerFor($request->getContentType())->serialize(
          $this->marshalling->marshal($request->getPayload())
        )));
      } else if ($request->hasBody()) {
        $send->setParameters($request->getBody());
      } else {
        $send->setParameters($request->getParameters());
      }
      
      try {
        $this->cat && $this->cat->debug('>>>', $send->getRequestString());
        $response= $this->connection->send($send);
      } catch (IOException $e) {
        throw new RestException('Cannot send request', $e);
      }

      $reader= new ResponseReader($this->deserializerFor(this($response->header('Content-Type'), 0), FALSE), $this->marshalling);
      if (NULL === $type) {
        $rr= new RestResponse($response, $reader);
      } else if ($type instanceof XPClass && $type->isSubclassOf('webservices.rest.RestResponse')) {
        $rr= $type->newInstance($response, $reader);
      } else {
        $rr= new RestResponse($response, $reader, $type);   // Deprecated!
      }

      $this->cat && $this->cat->debug('<<<', $response->toString(), $rr->contentCopy());
      return $rr;
    }

    /**
     * Creates a string representation
     *
     * @return string
     */
    public function toString() {
      return $this->getClassName().'(->'.xp::stringOf($this->connection).')';
    }
  }
?>
