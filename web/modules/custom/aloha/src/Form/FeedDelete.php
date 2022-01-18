<?php

namespace Drupal\aloha\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

/**
 * Defines a confirmation form to confirm deletion of something by id.
 */
class FeedDelete extends ConfirmFormBase {

  /**
   * ID of the item to delete.
   *
   * @var int
   */
  protected $id;

  /**
   * Building form for deleting.
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, string $id = NULL): array {
    $this->id = $id;
    return parent::buildForm($form, $form_state);
  }

  /**
   * Submitting deleteform.
   *
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $query = \Drupal::database();
    $query->delete('aloha')
      ->condition('id', $this->id,)
      ->execute();
    $this->messenger()->addStatus($this->t('Response was deleted.'));
    $form_state->setRedirect('aloha.page');
  }

  /**
   * Returning form id.
   *
   * {@inheritdoc}
   */
  public function getFormId() : string {
    return "confirm_delete_form";
  }

  /**
   * Canceling return.
   *
   * {@inheritdoc}
   */
  public function getCancelUrl(): Url {
    return new Url('aloha.page');
  }

  /**
   * Description before submitting.
   *
   * {@inheritdoc}
   */
  public function getDescription(): TranslatableMarkup {
    return $this->t('Are you sure about this action?');
  }

  /**
   * Deleting and get question.
   *
   * {@inheritdoc}
   */
  public function getQuestion():TranslatableMarkup {
    $query = \Drupal::database()->select('aloha', 'alo');
    $user_name = $query->condition('id', $this->id)
      ->fields('alo', [
        'id',
        'name',
      ])
      ->execute()->fetch();
    return $this->t('Do you want to delete %name?',
      ['%name' => $user_name->name]);
  }

}
