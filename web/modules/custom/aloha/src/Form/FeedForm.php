<?php

namespace Drupal\aloha\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for adding responses.
 */
class FeedForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'feed_form';
  }

  /**
   * Drupal\Core\Database\ definition.
   *
   * @var \Drupal\Core\Database\
   */
  protected $database;

  /**
   * Drupal\Core\Form\FormBuilder definition.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * Dependency Injection.
   */
  public static function create(ContainerInterface $container): FeedForm {
    $instanse = parent::create($container);
    $instanse->database = $container->get('database');
    $instanse->messenger = $container->get('messenger');
    return $instanse;
  }

  /**
   * Building form for adding response.
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#prefix'] = '<div id="form-wrapper">';
    $form['#suffix'] = '</div>';
    $form['#attached'] = ['library' => ['aloha/aloha_library']];
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your name:'),
      '#required' => TRUE,
      '#description' => $this->t('Minimum number of characters must be 2 and the maximum 100'),
      '#placeholder' => $this->t("Name"),
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your email:'),
      '#pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
      '#placeholder' => $this->t("Email"),
      '#description' => $this->t('Email must be validated'),
      '#required' => TRUE,
      '#attributes' => [
        'maxlength' => 30,
      ],
      '#suffix' => '<div class="email-validation-message"></div>',
    ];
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Your phone:'),
      '#required' => TRUE,
      '#description' => $this->t('Start with + and your country code.'),
      '#placeholder' => $this->t("Phone number"),
      '#pattern' => '^(?:\+38)?(0\d{9})$',
      '#attributes' => [
        'maxlength' => 20,
      ],
    ];
    $form['response'] = [
      '#title' => t('Your response text:'),
      '#type' => 'textarea',
      '#description' => 'Enter your response text',
      '#required' => TRUE,
      '#placeholder' => $this->t("Text"),
      '#attributes' => [
        'maxlength' => 200,
      ],
    ];
    $form['avatar'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Your Avatar'),
      '#name' => 'my_custom_file',
      '#description' => $this->t('Picture only in png, jpg or jpeg format'),
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
        'file_validate_size' => [(2097152)],
      ],
      '#upload_location' => 'public://avatar_images',
    ];
    $form['response_image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Response Photo'),
      '#name' => 'my_custom_file',
      '#description' => $this->t('Picture only in png, jpg or jpeg format'),
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
        'file_validate_size' => [(5242880)],
      ],
      '#upload_location' => 'public://response_images',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add response'),
      '#ajax' => [
        'callback' => '::ajaxCallback',
        'effect' => 'fade',
        'wrapper' => 'form-wrapper',
        'progress' => [
          'type' => 'throbber',
        ],
      ],
    ];
    return $form;
  }

  /**
   * Validation form fields.
   *
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $name = $form_state->getValue(['name']);
    if (mb_strlen($name) < 2) {
      $form_state->setErrorByName('name',
        $this->t('Name is too short.'));
    }
    elseif (mb_strlen($name) > 32) {
      $form_state->setErrorByName('name',
        $this->t('Name is too long'));
    }
  }

  /**
   * Submitting form and saving dates in database.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $file_avatar = $form_state->getValue(['avatar']);
    $file_image = $form_state->getValue(['response_image']);
    if ($file_avatar != NULL) {
      $data_avatar = File::load($file_avatar[0]);
      $data_avatar->setPermanent();
      $data_avatar->save();
    }
    elseif ($file_image != NULL) {
      $data_image = File::load($file_image[0]);
      $data_image->setPermanent();
      $data_image->save();
    }
    $this->database
      ->insert('aloha')
      ->fields([
        'name' => $form_state->getValue(['name']),
        'email' => $form_state->getValue('email'),
        'tel' => $form_state->getValue('phone_number'),
        'image' => $form_state->getValue(['response_image'])[0],
        'avatar' => $form_state->getValue(['avatar'])[0],
        'text' => $form_state->getValue(['response']),
        'date' => time(),
      ])
      ->execute();
  }

  /**
   * Submit Ajax.
   */
  public function ajaxCallback(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    if (!$form_state->hasAnyErrors()) {
      $this->messenger()->addStatus(t('Response added!'));
      $url = URL::fromRoute('aloha.page');
      $response->addCommand(new RedirectCommand($url->toString()));
      return $response;
    }
    return $form;
  }

}
