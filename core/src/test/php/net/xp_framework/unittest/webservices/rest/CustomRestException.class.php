<?php namespace net\xp_framework\unittest\webservices\rest;

use webservices\rest\RestException;


/**
 * Fixture for CustomRestResponseTest
 *
 * @see   xp://webservices.rest.RestResponse
 */
class CustomRestException extends RestException {
  protected $details;

  /**
   * Creates a new custom error
   * 
   * @param   [:var] details
   * @param   lang.Throwable cause
   */
  public function __construct($details, $cause= null) {
    parent::__construct($details['server.message'], $cause);
    $this->details= $details;
  }

  /**
   * Returns server message from details
   *
   * @return  string
   */
  public function serverMessage() {
    return $this->details['server.message'];
  }
}
