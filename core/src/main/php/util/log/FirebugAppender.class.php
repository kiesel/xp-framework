<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.log.Appender',
    'util.log.FirebugProtocol'
  );

  /**
   * Appender which appends all data to the FirePHP console
   *
   * @see      xp://util.log.Appender
   * @purpose  Appender
   */  
  class FirebugAppender extends Appender {

    /**
     * Append data
     *
     * @param   util.log.LoggingEvent event
     */ 
    public function append(LoggingEvent $event) {
      FirebugProtocol::getInstance()->append($event->getLevel(), $this->layout->format($event));
    }
  }
?>
