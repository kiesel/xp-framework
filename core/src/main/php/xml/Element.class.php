<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  /**
   * Interface for node elements
   *
   */
  interface Element {

    /**
     * Retrieve source representation
     *
     * @param   int indent default INDENT_WRAPPED
     * @param   string encoding default "iso-8859-1"
     * @param   string inset default ""
     * @return  string
     */
    public function getSource($indent= INDENT_WRAPPED, $encoding= 'iso-8859-1', $inset= '');
  }
?>