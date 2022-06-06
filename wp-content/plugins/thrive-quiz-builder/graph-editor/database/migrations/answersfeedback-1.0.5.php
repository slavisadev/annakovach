<?php
/**
 * Thrive Themes  https://thrivethemes.com
 *
 * @package thrive-quiz-builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

/** @var $this TD_DB_Migration $questions */

$answers = tge_table_name( 'answers' );
$this->add_or_modify_column( $answers, 'feedback', 'TEXT NULL DEFAULT NULL AFTER `result_id`' );
