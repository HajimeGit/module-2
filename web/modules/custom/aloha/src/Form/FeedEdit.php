<?php

namespace Drupal\aloha\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Form for editing responses.
 */
class FeedEdit extends FeedForm {

  /**
   * {@inheritDoc}
   */
  public function getFormId(): string {
    return 'confirm_edit_form';
  }

  /**
   * Response to edit if any.
   *
   * @var object
   */

  protected $response;

  /**
   * Building extended form.
   *
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, int $id = NULL): array {
    $database = \Drupal::database();
    $result = $database->select('aloha', 'alo')
      ->fields('alo', [
        'id',
        'name',
        'email',
        'tel',
        'avatar',
        'text',
        'image',
        'date',
      ])
      ->condition('id', $id)
      ->execute();
    $response = $result->fetch();
    $this->response = $response;
    $form = parent::buildForm($form, $form_state);
    $form['#submit'] = ["::editSubmitForm"];
    $form['name']['#default_value'] = $response->name;
    $form['email']['#default_value'] = $response->email;
    $form['phone_number']['#default_value'] = $response->tel;
    $form['response']['#default_value'] = $response->text;
    $form['avatar']['#default_value'][] = $response->avatar;
    $form['response_image']['#default_value'][] = $response->image;
    $form['submit']['#value'] = $this->t('Edit response');
    return $form;
  }

  /**
   * Submit edit version of the response.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $database = \Drupal::database();
    $user_name = $form_state->getValue('name');
    $user_email = $form_state->getValue('email');
    $user_phone = $form_state->getValue('phone_number');
    $response_text = $form_state->getValue('response');
    $user_avatar = $form_state->getValue('avatar')[0];
    $response_image = $form_state->getValue('response_image')[0];
    $database
      ->update('aloha')
      ->condition('id', $this->response->id)
      ->fields(
        [
          'name' => $user_name,
          'email' => $user_email,
          'avatar' => $user_avatar,
          'tel' => $user_phone,
          'text' => $response_text,
          'image' => $response_image,
        ],
      )
      ->execute();
  }

  /**
   * Ajax submitting.
   */
  public function ajaxCallback(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    if (!$form_state->hasAnyErrors()) {
      \Drupal::messenger()->addStatus(t('Response rewrite'));
      $url = URL::fromRoute('aloha.page');
      $response->addCommand(new RedirectCommand($url->toString()));
      return $response;
    }
    return $form;
  }

}
