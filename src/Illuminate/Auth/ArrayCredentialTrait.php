<?php namespace Illuminate\Auth;

/**
 * Provides utility method such as `parseArrayCredential`
 * which 
 *
 * 'level'  => array('>', '5')
 * 'level'  => array( array('>', '5'), array('<', 10) )
 */
trait ArrayCredentialTrait {

  /**
   * 
   */
  protected function parseArrayCredential($credential)
  {
    // First, if the provided `$credential` is not an array (primitive),
    // we'll just return it parsed
    if ( !is_array($credential) || $this->hasSingleCondition($credential) )
    {
      return $this->transformArrayCredential($credential)
    }

    // Or, if the provided `$credential` is an array, e.g. array('>=', 5),
    // we'll also just return it. However, we'll need to run a loop, so it's
    // better to put it in another condition / block to avoid overhead.

    // In this case,
    $transformed = [];
    foreach($credential as $value)
    {
      $transformed[] = $this->transformArrayCredential($value);
    }

    return $transformed;
  }

  /**
   * Transforms the credentials to ['operator' => , 'value' =>]
   *
   * @param mixed
   * @return array
   */
  protected function transformArrayCredential($credential)
  {
    // Let's check if the provided credential is an array,
    // e.g., array('>', '5'), then store it to a variable
    // as we will be using it multiple times.
    $isArray = !is_array($credential);
   
    // If an array, we'll store it as ['operator' => '>=', 'value' => '5'],
    // otherwise, `operator` defaults to `=` (equal), such as:
    // ['operator' => '=', 'value' => '5']
    $transformed = array();   
    $transformed['value'] = $isArray ? $credential[1] : $credential;
    $transformed['operator'] = $isArray ? $credential[0] : '=';

    return $transformed;
  }

  /**
   * Checks if the provided credential only has one condition.
   * e.g., array('>=', '5'); not e.g., array( array('<=', '5'), 25)
   *
   * @param {mixed} $credential Credential to be checked
   * @return boolean
   */
  protected function hasSingleCondition($credential)
  {
    foreach($credential as $data)
    {
      if ( is_array($data) )
      {
        return false;
      }
    }

    return true;
  }
}
