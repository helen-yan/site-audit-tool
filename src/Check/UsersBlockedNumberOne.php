<?php
/**
 * @file
 * Contains Drupal\site_audit\Plugin\SiteAuditCheck\UsersBlockedNumberOne
 */

namespace SiteAudit\Check;

use SiteAudit\SiteAuditCheckBase;

/**
 * Provides the UsersBlockedNumberOne Check.
 */
class UsersBlockedNumberOne extends SiteAuditCheckBase {

  /**
   * {@inheritdoc}.
   */
  public function getId() {
    return 'users_blocked_number_one';
  }

  /**
   * {@inheritdoc}.
   */
  public function getLabel() {
    return $this->t('UID #1 access');
  }

  /**
   * {@inheritdoc}.
   */
  public function getDescription() {
    return $this->t("Determine if UID #1 is blocked.");
  }

  /**
   * {@inheritdoc}.
   */
  public function getReportId() {
    return 'users';
  }

  /**
   * {@inheritdoc}.
   */
  public function getResultFail() {
    return $this->t('UID #1 is blocked!');
  }

  /**
   * {@inheritdoc}.
   */
  public function getResultInfo() {}

  /**
   * {@inheritdoc}.
   */
  public function getResultPass() {
    return $this->t('UID #1 not blocked.');
  }

  /**
   * {@inheritdoc}.
   */
  public function getResultWarn() {}

  /**
   * {@inheritdoc}.
   */
  public function getAction() {
    if ($this->score != SiteAuditCheckBase::AUDIT_CHECK_SCORE_PASS) {
      return $this->t('Unblock UID #1');
    }
  }

  /**
   * {@inheritdoc}.
   */
  public function calculateScore() {
    $query = db_select('users_field_data', 'ufd');
    $query->addField('ufd', 'status');
    $query->condition('uid', 1);

    if (!$query->execute()->fetchField()) {
      return SiteAuditCheckBase::AUDIT_CHECK_SCORE_FAIL;
    }
    return SiteAuditCheckBase::AUDIT_CHECK_SCORE_PASS;
  }

}
